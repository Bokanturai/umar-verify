<?php

namespace App\Http\Controllers\Admin\Agency;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\AgentService;
use App\Models\User;
use App\Models\ServiceField;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VninToNibssController extends Controller
{
    /**
     * List vnin to nibss requests with filters and pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $bankFilter = $request->input('bank');

        // Base query filtering by service_type
        $query = AgentService::query()
            ->where('service_type', 'vnin to nibss');

        // Enhanced search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('bvn', 'like', "%$search%")
                  ->orWhere('nin', 'like', "%$search%")
                  ->orWhere('reference', 'like', "%$search%")
                  ->orWhere('performed_by', 'like', "%$search%")
                  ->orWhere('user_id', 'like', "%$search%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($bankFilter) {
            $query->where('bank', $bankFilter);
        }

        // Apply custom status order + submission_date
        $enrollments = $query
            ->orderByRaw("CASE status
                WHEN 'pending' THEN 1
                WHEN 'processing' THEN 2
                WHEN 'in-progress' THEN 3
                WHEN 'query' THEN 4
                WHEN 'resolved' THEN 5
                WHEN 'successful' THEN 6
                WHEN 'rejected' THEN 7
                WHEN 'failed' THEN 8
                WHEN 'remark' THEN 9
                ELSE 999 END")
            ->orderByDesc('submission_date')
            ->paginate(10);

        // Status counts filtered by service_type
        $statusCounts = [
            'pending'    => AgentService::where('service_type', 'vnin to nibss')->where('status', 'pending')->count(),
            'processing' => AgentService::where('service_type', 'vnin to nibss')->where('status', 'processing')->count(),
            'resolved'   => AgentService::where('service_type', 'vnin to nibss')->whereIn('status', ['resolved', 'successful'])->count(),
            'rejected'   => AgentService::where('service_type', 'vnin to nibss')->whereIn('status', ['rejected', 'failed'])->count(),
        ];

        // Get distinct banks for filter
        $banks = $this->getDistinctBanks();

        return view('admin.vnin-nibss.index', compact('enrollments', 'search', 'statusFilter', 'bankFilter', 'statusCounts', 'banks'));
    }

    /**
     * Show details of a single request
     */
    public function show($id)
    {
        $enrollmentInfo = AgentService::findOrFail($id);
        $user = User::find($enrollmentInfo->user_id);

        $statusHistory = collect([
            [
                'status' => $enrollmentInfo->status,
                'comment' => $enrollmentInfo->comment,
                'submission_date' => $enrollmentInfo->created_at,
                'updated_at' => $enrollmentInfo->updated_at,
                'file_url' => $enrollmentInfo->file_url,
            ]
        ]);

        return view('admin.vnin-nibss.view', compact('enrollmentInfo', 'statusHistory', 'user'));
    }


    /**
     * Update the status of a request
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,in-progress,resolved,successful,rejected,failed,query,remark',
            'comment' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048', // 2MB max
            'force_refund' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $enrollment = AgentService::findOrFail($id);
            $oldStatus = $enrollment->status;
            $user = User::find($enrollment->user_id);

            // Handle file upload
            $fileUrl = $enrollment->file_url;
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($fileUrl && Storage::disk('public')->exists($fileUrl)) {
                    Storage::disk('public')->delete($fileUrl);
                }

                // Store new file
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('vnin-files', $fileName, 'public');
                $baseUrl = rtrim(config('app.url'), '/');
                $fileUrl = $baseUrl . '/storage/vnin-files/' . $fileName;
            }

            // Update enrollment
            $enrollment->status = $request->status;
            $enrollment->comment = $request->comment;
            $enrollment->file_url = $fileUrl;
            $enrollment->save();

            // Handle refund logic if rejected or failed
            if (in_array($request->status, ['rejected', 'failed'])) {
                if (!in_array($oldStatus, ['rejected', 'failed']) || $request->force_refund) {
                    $this->processRefund($enrollment, $request->force_refund);
                }
            }

            // User alert on success
            if ($request->status === 'successful') {
                $enrollment->comment = $request->comment ?? 'Success! Your VNIN to NIBSS request has been processed successfully.';
                $enrollment->save();
            }

            DB::commit();
            return redirect()->route('admin.vnin-nibss.index')
                ->with('successMessage', 'Status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.vnin-nibss.index')
                ->with('errorMessage', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Handle refund when an enrollment is rejected
     */
    private function processRefund($enrollment, $forceRefund = false)
    {
        $user = User::find($enrollment->user_id);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        $role = strtolower($user->role ?? 'default');

        // Check if refund already exists (via description or metadata)
        $refundExists = Transaction::where('type', 'refund')
            ->where(function($q) use ($enrollment) {
                $q->where('description', 'LIKE', "%Request ID #{$enrollment->id}%")
                  ->orWhere('metadata->agent_service_id', $enrollment->id);
            })->exists();

        if ($refundExists && !$forceRefund) {
            throw new \Exception('Refund already processed for this request.');
        }

        // Use the actual amount paid by the user stored in the enrollment record
        $paidAmount = $enrollment->amount;

        if (!$paidAmount || $paidAmount <= 0) {
            throw new \Exception('No valid payment amount found for refund.');
        }

        $refundAmount = round($paidAmount, 2);
        $debitAmount = 0;

        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if (!$wallet) {
            throw new \Exception('Wallet not found for user.');
        }

        // Update wallet balance
        $wallet->balance += $refundAmount;
        $wallet->save();

        // Create refund transaction
        Transaction::create([
            'transaction_ref' => strtoupper(Str::random(12)),
            'user_id' => $user->id,
            'performed_by' => Auth::user()->first_name . ' ' . (Auth::user()->last_name ?? ''),
            'amount' => $refundAmount,
            'fee' => 0.00,
            'net_amount' => $refundAmount,
            'description' => "Full Refund for rejected/failed service [{$enrollment->service_field_name}], Request ID #{$enrollment->id}",
            'type' => 'refund',
            'status' => 'completed',
            'metadata' => json_encode([
                'service_id' => $enrollment->service_id,
                'service_field_id' => $enrollment->service_field_id,
                'field_code' => $enrollment->field_code,
                'field_name' => $enrollment->service_field_name ?? null,
                'user_role' => $role,
                'total_paid' => $paidAmount,
                'percentage_refunded' => 100,
                'amount_debited_by_system' => $debitAmount,
                'forced_refund' => $forceRefund,
                'agent_service_id' => $enrollment->id,
            ]),
        ]);
    }

    /**
     * Get distinct banks from agent_services table
     */
    private function getDistinctBanks()
    {
        return AgentService::whereNotNull('bank')
            ->where('bank', '!=', '')
            ->distinct()
            ->pluck('bank')
            ->sort()
            ->values();
    }
}
