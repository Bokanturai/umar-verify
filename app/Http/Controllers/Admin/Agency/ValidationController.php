<?php

namespace App\Http\Controllers\Admin\Agency;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\AgentService;
use App\Models\User;
use App\Models\ServiceField;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidationController extends Controller
{
    /**
     * Check status of a validation request using Smart Idea API
     */
    public function checkStatus($id)
    {
        try {
            $enrollment = AgentService::findOrFail($id);
            
            $apiToken = env('AREWA_API_TOKEN');
            $baseUrl = env('AREWA_BASE_URL');
            $endpoint = rtrim($baseUrl, '/') . '/nin/validation';

            $payload = [
                'description' => $enrollment->description ?? "Admin Status Check",
                'nin' => $enrollment->nin,
                'field_code' => '015'
            ];

            $response = Http::withToken($apiToken)
                ->acceptJson()
                ->get($endpoint, $payload);

            if ($response->successful()) {
                $apiResponse = $response->json();
                $cleanResponse = $this->cleanApiResponse($apiResponse);
                
                $updateData = [
                    'comment' => $cleanResponse,
                ];

                if (isset($apiResponse['status'])) {
                    $updateData['status'] = $this->normalizeStatus($apiResponse['status']);
                } elseif (isset($apiResponse['response'])) {
                    $updateData['status'] = $this->normalizeStatus($apiResponse['response']);
                }
                
                if (isset($apiResponse['file_url'])) {
                    $updateData['file_url'] = $apiResponse['file_url'];
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
            Log::error('Admin Validation Status Check Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * List validation services with filters and pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $bankFilter = $request->input('bank');

        // Base query filtering by service_type
        $query = AgentService::query()
            ->select('agent_services.*', 'users.email as user_email')
            ->join('users', 'agent_services.user_id', '=', 'users.id')
            ->where('agent_services.service_type', 'NIN_VALIDATION');

        // Enhanced search
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

        // Status counts filtered by service_type (Fixed to match 'NIN_VALIDATION')
        $statusCounts = [
            'pending'    => AgentService::where('service_type', 'NIN_VALIDATION')->where('status', 'pending')->count(),
            'processing' => AgentService::where('service_type', 'NIN_VALIDATION')->where('status', 'processing')->count(),
            'resolved'   => AgentService::where('service_type', 'NIN_VALIDATION')->whereIn('status', ['resolved', 'successful'])->count(),
            'rejected'   => AgentService::where('service_type', 'NIN_VALIDATION')->whereIn('status', ['rejected', 'failed'])->count(),
        ];

        // Get distinct banks for filter
        $banks = $this->getDistinctBanks();

        return view('admin.validation.index', compact('enrollments', 'search', 'statusFilter', 'bankFilter', 'statusCounts', 'banks'));
    }

    /**
     * Show details of a single validation service
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

        return view('admin.validation.view', compact('enrollmentInfo', 'statusHistory', 'user'));
    }

    /**
     * Update the status of a validation service
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,in-progress,resolved,successful,rejected,failed,query,remark',
            'comment' => 'nullable|string',
            'force_refund' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $enrollment = AgentService::findOrFail($id);
            $oldStatus = $enrollment->status;
            $user = User::find($enrollment->user_id);

            $enrollment->status = $request->status;
            $enrollment->comment = $request->comment;
            $enrollment->save();

            // No refund for NIN Validation

            DB::commit();
            return redirect()->route('admin.validation.index')
                ->with('successMessage', 'Status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.validation.index')
                ->with('errorMessage', 'Failed to update status: ' . $e->getMessage());
        }
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


    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            // Prioritize human-readable message fields
            if (isset($response['comment']) && is_string($response['comment'])) {
                return $response['comment'];
            }
            if (isset($response['message']) && is_string($response['message'])) {
                return $response['message'];
            }

            // Exclude common structural keys and format the rest nicely
            $toExclude = ['status', 'success', 'nin', 'response', 'message', 'comment', 'reference', 'file_url'];
            $toKeep = array_diff_key($response, array_flip($toExclude));

            if (empty($toKeep)) {
                return (isset($response['success']) && $response['success']) ? 'Successful' : (isset($response['status']) ? ucfirst($response['status']) : 'Processed');
            }

            $parts = [];
            foreach ($toKeep as $key => $value) {
                // Skip non-scalar values and very long strings (potential base64)
                if (!is_scalar($value) || strlen((string)$value) > 255) continue;

                $label = ucfirst(str_replace(['_', '-'], ' ', $key));
                if (is_bool($value)) {
                    $parts[] = $label . ': ' . ($value ? 'Yes' : 'No');
                } else {
                    $parts[] = $label . ': ' . $value;
                }
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
}
