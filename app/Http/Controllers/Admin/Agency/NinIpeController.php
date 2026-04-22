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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NinIpeController extends Controller
{
    /**
     * Check status of a nin_ipe request using Smart Idea API
     */
    public function checkStatus($id)
    {
        try {
            $enrollment = AgentService::findOrFail($id);
            
            $apiToken = env('AREWA_API_TOKEN');
            $baseUrl = env('AREWA_BASE_URL', 'https://api.arewasmart.com.ng/api/v1');
            $endpoint = rtrim($baseUrl, '/') . '/nin/ipe';

            $response = Http::withToken($apiToken)
                ->acceptJson()
                ->get($endpoint, [
                    'tracking_id' => $enrollment->tracking_id,
                ]);

            if ($response->successful()) {
                $apiResponse = $response->json();
                $cleanResponse = $this->cleanApiResponse($apiResponse);
                
                $updateData = [
                    'comment' => $cleanResponse,
                ];

                $data = $apiResponse['data'] ?? $apiResponse;

                if (isset($data['status'])) {
                    $updateData['status'] = $this->normalizeStatus($data['status']);
                } elseif (isset($apiResponse['status'])) {
                    $updateData['status'] = $this->normalizeStatus($apiResponse['status']);
                }
                
                if (isset($data['file_url'])) {
                    $updateData['file_url'] = $data['file_url'];
                }

                $enrollment->update($updateData);

                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully using reference: ' . ($enrollment->reference ?? 'N/A'),
                    'data' => [
                        'status' => $enrollment->status,
                        'comment' => $enrollment->comment,
                        'file_url' => $enrollment->file_url,
                        'updated_at' => $enrollment->updated_at->format('M j, Y g:i A')
                    ]
                ]);
            }

            $lastError = $response->json('message') ?? $response->json('error') ?? 'Record not found or API error.';

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch status from API: ' . $lastError,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * List nin_ipe requests with filters and pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $bankFilter = $request->input('bank');

        // Base query filtering by service_type (handling both 'ipe' and 'nin_ipe' for consistency)
        $query = AgentService::query()
            ->select('agent_services.*', 'users.email as user_email')
            ->join('users', 'agent_services.user_id', '=', 'users.id')
            ->whereIn('service_type', ['ipe', 'nin_ipe']);

        // Enhanced search: BVN, NIN, transaction_ref, agent name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('agent_services.bvn', 'like', "%$search%")
                  ->orWhere('agent_services.nin', 'like', "%$search%")
                  ->orWhere('agent_services.tracking_id', 'like', "%$search%")
                  ->orWhere('agent_services.reference', 'like', "%$search%")
                  ->orWhere('agent_services.performed_by', 'like', "%$search%")
                  ->orWhere('agent_services.user_id', 'like', "%$search%");
            });
        }

        if ($statusFilter) {
            $query->where('agent_services.status', $statusFilter);
        }

        if ($bankFilter) {
            $query->where('agent_services.bank', $bankFilter);
        }

        // Apply custom status order + submission_date
        $enrollments = $query
            ->orderByRaw("CASE agent_services.status
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
            ->orderByDesc('agent_services.submission_date')
            ->paginate(10);

        // Status counts filtered by service_type
        $statusCounts = [
            'pending'    => AgentService::whereIn('service_type', ['ipe', 'nin_ipe'])->where('status', 'pending')->count(),
            'processing' => AgentService::whereIn('service_type', ['ipe', 'nin_ipe'])->where('status', 'processing')->count(),
            'resolved'   => AgentService::whereIn('service_type', ['ipe', 'nin_ipe'])->whereIn('status', ['resolved', 'successful'])->count(),
            'rejected'   => AgentService::whereIn('service_type', ['ipe', 'nin_ipe'])->whereIn('status', ['rejected', 'failed'])->count(),
        ];

        // Get distinct banks for filter
        $banks = $this->getDistinctBanks();

        return view('admin.ninipe.index', compact('enrollments', 'search', 'statusFilter', 'bankFilter', 'statusCounts', 'banks'));
    }

    /**
     * Show details of a single nin_ipe request
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

        return view('admin.ninipe.view', compact('enrollmentInfo', 'statusHistory', 'user'));
    }

    /**
     * Update the status of a nin_ipe request
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
                $filePath = $file->storeAs('project-files/nin-ipe', $fileName, 'public');
                $fileUrl = Storage::url($filePath);
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
                $enrollment->comment = $request->comment ?? 'Success! Your NIN IPE request has been processed successfully.';
                $enrollment->save();
            }

            DB::commit();
            return redirect()->route('admin.ninipe.index')
                ->with('successMessage', 'Status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.ninipe.index')
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

    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            $data = $response['data'] ?? $response;

            if (isset($data['comment']) && is_string($data['comment'])) {
                return $data['comment'];
            }
            if (isset($response['message']) && is_string($response['message'])) {
                return $response['message'];
            }

            $toExclude = ['status', 'success', 'nin', 'response', 'message', 'comment', 'reference', 'file_url', 'tracking_id'];
            $toKeep = array_diff_key($data, array_flip($toExclude));

            if (empty($toKeep)) {
                return (isset($response['success']) && $response['success']) ? 'Successful' : (isset($data['status']) ? ucfirst($data['status']) : 'Processed');
            }

            $parts = [];
            foreach ($toKeep as $key => $value) {
                if (!is_scalar($value) || strlen((string)$value) > 255) continue;
                $label = ucfirst(str_replace(['_', '-'], ' ', $key));
                $parts[] = $label . ': ' . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value);
            }

            return !empty($parts) ? implode(', ', $parts) : 'Processed';
        }

        return (string) $response;
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'submitted', 'new', 'pending' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            'query', 'queried' => 'query',
            default => 'pending',
        };
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
