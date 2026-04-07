<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NinValidationController extends Controller
{
    public function index(Request $request)
    {
        $validationService = Service::where('name', 'Validation')->first();
        $validationFields = $validationService ? $validationService->fields : collect();

        $services = collect();
        $user = Auth::user();
        $role = $user->role ?? 'user';
        
        foreach ($validationFields as $field) {
            $price = $field->prices()->where('user_type', $role)->value('price') ?? $field->base_price;
            $services->push([
                'id' => $field->id,
                'name' => $field->field_name,
                'price' => $price,
                'type' => 'validation',
                'service_id' => $field->service_id
            ]);
        }
        
        $wallet = Wallet::where('user_id', Auth::id())->first();
        
        $query = AgentService::where('user_id', Auth::id())
            ->where('service_type', 'NIN_VALIDATION'); // Specific to Validation

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('nin', 'like', "%{$searchTerm}%");
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $submissions = $query->orderByRaw("
          CASE status 
        WHEN 'pending' THEN 1 
        WHEN 'processing' THEN 2 
        WHEN 'successful' THEN 3 
        WHEN 'failed' THEN 4 
        WHEN 'resolved' THEN 5 
        WHEN 'rejected' THEN 6 
        ELSE 7 
            END
        ")->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('nin.validation', compact('services', 'wallet', 'submissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_field' => 'required',
            'nin' => 'required|digits:11',
        ]);

        $fieldId = $request->service_field;
        $serviceField = ServiceField::with('service')->findOrFail($fieldId);
        
        $user = Auth::user();
        $role = $user->role ?? 'user';
        
        $servicePrice = $serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price;

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $servicePrice) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        $apiKey = env('AREWA_API_TOKEN');
        $apiBaseUrl = env('AREWA_BASE_URL');
        $apiUrl = rtrim($apiBaseUrl, '/') . '/nin/validation';

        $payload = [
            'description' => $request->description ?? "My Reference",
            'nin' => $request->nin,
            'field_code' => '015', // Code for Validation
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post($apiUrl, $payload);
            
            $data = $response->json();

            if (!$response->successful() || (isset($data['status']) && $data['status'] == 'error')) {
                return back()->with('error', 'API Submission Failed: ' . ($data['message'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return back()->with('error', 'Connection Error: Unable to reach service provider.');
        }

        DB::beginTransaction();

        try {
            $wallet->decrement('balance', $servicePrice);

            $transactionRef = 'TRX-' . strtoupper(Str::random(10));
            $performedBy = $user->first_name . ' ' . $user->last_name;

            $cleanResponse = $this->cleanApiResponse($data);

            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "NIN Validation for {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $serviceField->service->name,
                    'service_field' => $serviceField->field_name,
                    'nin' => $request->nin,
                ],
            ]);

            $status = $this->normalizeStatus($data['status'] ?? 'processing');

            AgentService::create([
                'reference' => 'REF-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'field_code' => $serviceField->field_code,
                'transaction_id' => $transaction->id,
                'service_type' => 'NIN_VALIDATION',
                'nin' => $request->nin,
                'amount' => $servicePrice,
                'status' => $status,
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => $cleanResponse,
                'performed_by' => $performedBy,
            ]);

            DB::commit();
            return back()->with('success', 'NIN Validation Request submitted successfully. Status: ' . $status);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction Error: ' . $e->getMessage());
            return back()->with('error', 'System Error: Failed to record transaction. Please contact support.');
        }
    }

    public function checkStatus(Request $request, $id = null)
    {
        try {
            if ($id) {
                $agentService = AgentService::findOrFail($id);
            } else {
                $request->validate([
                    'nin' => 'required|string',
                ]);
                $agentService = AgentService::where('nin', $request->nin)
                    ->orderBy('created_at', 'desc')
                    ->firstOrFail();
            }

            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $url = rtrim($apiBaseUrl, '/') . '/nin/validation';
            
            $payload = [
                'description' => $agentService->description ?? "Status Check",
                'nin' => $agentService->nin,
                'field_code' => '015'
            ];

            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->get($url, $payload);
            
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

            $agentService->update($updateData);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'nin' => $agentService->nin,
                    'status' => $agentService->status,
                    'response' => $apiResponse,
                ]);
            }

            return back()->with('success', 'Status checked successfully. Current status: ' . $agentService->status);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to check status: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Unable to complete the status check. Please try again.');
        }
    }

    public function batchCheck()
    {
        try {
            $pendingSubmissions = AgentService::where('service_type', 'NIN_VALIDATION')
                ->whereIn('status', ['pending', 'processing'])
                ->where(function($query) {
                    $query->where('updated_at', '<', now()->subMinutes(10))
                          ->orWhereNull('updated_at');
                })
                ->limit(20)
                ->get();

            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $url = rtrim($apiBaseUrl, '/') . '/nin/validation';

            $checked = 0;

            foreach ($pendingSubmissions as $submission) {
                try {
                    $payload = [
                        'description' => $submission->description ?? "Batch Check",
                        'nin' => $submission->nin,
                        'field_code' => '015'
                    ];

                    $response = Http::withToken($apiKey)
                        ->acceptJson()
                        ->get($url, $payload);

                    $apiResponse = $response->json();

                    if ($response->successful()) {
                        $cleanResponse = $this->cleanApiResponse($apiResponse);
                        
                        $updateData = [
                            'comment' => $cleanResponse,
                        ];

                        if (isset($apiResponse['status'])) {
                            $updateData['status'] = $this->normalizeStatus($apiResponse['status']);
                        } elseif (isset($apiResponse['response'])) {
                            $updateData['status'] = $this->normalizeStatus($apiResponse['response']);
                        }

                        $isFailingNow = isset($updateData['status']) && $updateData['status'] === 'failed' && $submission->status !== 'failed';

                        if ($isFailingNow) {
                            DB::beginTransaction();
                            try {
                                $submission->update($updateData);

                                $wallet = Wallet::where('user_id', $submission->user_id)->lockForUpdate()->first();
                                if ($wallet) {
                                    $wallet->increment('balance', $submission->amount);

                                    Transaction::create([
                                        'transaction_ref' => 'REF_' . $submission->reference,
                                        'user_id'         => $submission->user_id,
                                        'amount'          => $submission->amount,
                                        'performed_by'    => 'System Auto-Refund',
                                        'description'     => "Refund for failed NIN Validation",
                                        'type'            => 'credit',
                                        'status'          => 'completed',
                                        'metadata'        => [
                                            'original_reference' => $submission->reference,
                                            'api_reason'         => $updateData['comment'] ?? 'Failed submission',
                                        ],
                                    ]);
                                }

                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error('NIN Validation Auto Refund Error in Batch', ['error' => $e->getMessage(), 'submission_id' => $submission->id]);
                            }
                        } else {
                            $submission->update($updateData);
                        }
                        $checked++;
                    }
                } catch (\Exception $e) {
                    Log::error('Batch check error for NIN Validation ' . $submission->id . ': ' . $e->getMessage());
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Batch check completed. Checked {$checked} submissions.",
                'checked' => $checked
            ]);

        } catch (\Exception $e) {
            Log::error('Batch Check Error (NIN Validation): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Batch check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info('NIN Validation Webhook Received', $data);

        $identifier = $data['nin'] ?? null;

        if ($identifier) {
            $submission = AgentService::where('nin', $identifier)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($submission) {
                $cleanResponse = $this->cleanApiResponse($data);
                
                $updateData = [
                    'comment' => $cleanResponse,
                ];

                if (isset($data['status'])) {
                    $updateData['status'] = $this->normalizeStatus($data['status']);
                }

                $submission->update($updateData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook received successfully'
        ]);
    }

    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            $toKeep = array_diff_key($response, array_flip(['status', 'message', 'response']));
            return json_encode($toKeep);
        }
        return (string) $response;
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            default => 'pending',
        };
    }
}