<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NinModificationController extends Controller
{
    /**
     * Display NIN Modification dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get NIN Modification service
        $ninService = Service::where('name', 'NIN Modification')
            ->where('is_active', true)
            ->first();

        if (!$ninService) {
            return back()->with([
                'status' => 'error',
                'message' => 'NIN Modification service is not available.'
            ]);
        }

        // Fetch service fields
        $serviceFields = ServiceField::where('service_id', $ninService->id)
            ->where('is_active', true)
            ->get();

        // Base query
        $query = AgentService::with(['serviceField', 'transaction'])
            ->where('user_id', $user->id)
            ->where('service_type', 'nin_modification');

        // Filters
        if ($request->filled('search')) {
            $query->where('nin', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        // CRM submission history with custom ordering
        $crmSubmissions = $query->orderByRaw("
                CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'query' THEN 2 
                    WHEN 'processing' THEN 3 
                    WHEN 'successful' THEN 4 
                    WHEN 'resolved' THEN 5 
                    WHEN 'rejected' THEN 6 
                    ELSE 7 
                END
            ")
            ->orderByDesc('submission_date')
            ->paginate(10)
            ->withQueryString();

        return view('nin.modification', compact(
            'serviceFields',
            'crmSubmissions',
            'wallet',
            'ninService'
        ));
    }

    /**
     * Submit NIN Modification Request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validation
        $rules = [
            'service_field_id' => 'required|exists:service_fields,id',
            'nin'             => 'required|string|regex:/^[0-9]{11}$/',
        ];

        if ($request->has('modification_data')) {
            $rules['modification_data'] = 'required|array';
            // Basic validation for key fields in modification_data
            $rules['modification_data.first_name'] = 'required|string';
            $rules['modification_data.surname'] = 'required|string';
        } else {
            $rules['description'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules);

        // Fetch service & field
        $serviceField = ServiceField::with('service')
            ->findOrFail($validated['service_field_id']);

        $service = $serviceField->service;

        if (!$service || !$service->is_active) {
            return back()->with([
                'status' => 'error',
                'message' => 'Selected service is not available.'
            ])->withInput();
        }

        // Service price
        $servicePrice = $serviceField->getPriceForUserType($user->role);

        if ($servicePrice === null) {
            return back()->with([
                'status' => 'error',
                'message' => 'Service price not configured for your user type.'
            ])->withInput();
        }

        // Wallet check
        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        if ($wallet->status !== 'active') {
            return back()->with([
                'status' => 'error',
                'message' => 'Your wallet is not active. Please contact support.'
            ])->withInput();
        }

        if ($wallet->balance < $servicePrice) {
            return back()->with([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. You need NGN ' .
                    number_format($servicePrice - $wallet->balance, 2) . ' more.'
            ])->withInput();
        }

        // API Call First
        $apiKey = env('AREWA_API_TOKEN');
        $apiBaseUrl = env('AREWA_BASE_URL');
        $apiUrl = rtrim($apiBaseUrl, '/') . '/nin/modification';

        // Prepare description payload for Arewa API as a string
        if ($request->has('modification_data')) {
            $modData = $request->input('modification_data');
            if ($serviceField->field_code === '032') {
                // For name correction, join names into a single string
                $apiDescription = trim(($modData['first_name'] ?? '') . ' ' . ($modData['middle_name'] ?? '') . ' ' . ($modData['surname'] ?? ''));
            } else {
                // For others, just JSON encode or join values
                $apiDescription = json_encode($modData);
            }
        } else {
            $apiDescription = $request->input('description');
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post($apiUrl, [
                    'field_code'  => $serviceField->field_code,
                    'nin'         => $validated['nin'],
                    'description' => (string) $apiDescription,
                ]);

            $apiData = $response->json();

            if (!$response->successful() || (isset($apiData['success']) && $apiData['success'] === false)) {
                Log::error('Arewa Smart API NIN Modification Failed', [
                    'response' => $apiData,
                    'payload' => [
                        'field_code' => $serviceField->field_code,
                        'nin' => $validated['nin']
                    ]
                ]);
                return back()->with([
                    'status' => 'error',
                    'message' => 'API Submission Failed: ' . ($apiData['message'] ?? 'Unknown API error.')
                ])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Arewa Smart API Connection Error', ['error' => $e->getMessage()]);
            return back()->with([
                'status' => 'error',
                'message' => 'Connection Error: Unable to reach service provider.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate Reference from API if available, otherwise use local
            $transactionRef = $apiData['data']['reference'] ?? ('M1' . strtoupper(Str::random(10)));
            $performedBy = trim($user->first_name . ' ' . $user->last_name);

            // Create Transaction
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'        => $user->id,
                'amount'         => $servicePrice,
                'description'    => "NIN modification for {$serviceField->field_name}",
                'type'           => 'debit',
                'status'         => 'completed',
                'performed_by'   => $performedBy,
                'metadata'       => [
                    'service'          => $service->name,
                    'service_field'    => $serviceField->field_name,
                    'field_code'       => $serviceField->field_code,
                    'nin'              => $validated['nin'],
                    'price_details'    => [
                        'base_price' => $serviceField->base_price,
                        'user_price' => $servicePrice
                    ],
                    'api_response'     => $apiData
                ],
            ]);

            // Create NIN Modification record
            AgentService::create([
                'reference'          => $transactionRef,
                'user_id'            => $user->id,
                'service_field_id'   => $serviceField->id,
                'service_id'         => $service->id,
                'field_code'         => $serviceField->field_code,
                'amount'             => $servicePrice,
                'service_name'       => $service->name,
                'service_field_name' => $serviceField->field_name,
                'nin'                => $validated['nin'],
                'description'        => $validated['description'] ?? $serviceField->field_name,
                'modification_data'  => $request->input('modification_data'),
                'performed_by'       => $performedBy,
                'transaction_id'     => $transaction->id,
                'submission_date'    => now(),
                'status'             => 'pending',
                'service_type'       => 'nin_modification',
                'comment'            => $apiData['message'] ?? null,
            ]);

            // Debit Wallet
            $wallet->decrement('balance', $servicePrice);

            DB::commit();

            return redirect()->route('nin-modification')->with([
                'status' => 'success',
                'message' => 'NIN Modification Submitted Successfully. Reference: ' .
                             $transactionRef . '. Charged: NGN ' .
                             number_format($servicePrice, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('NIN Modification DB Save failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with([
                'status' => 'error',
                'message' => 'Internal Error: Failed to save record locally. Please contact support.',
            ])->withInput();
        }
    }

    /**
     * Check Status of NIN Modification.
     */
    public function checkStatus(Request $request, $id)
    {
        $agentService = AgentService::findOrFail($id);

        $apiKey = env('AREWA_API_TOKEN');
        $apiBaseUrl = env('AREWA_BASE_URL');
        $apiUrl = rtrim($apiBaseUrl, '/') . '/nin/modification';

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->get($apiUrl, [
                    'reference' => $agentService->reference,
                    // 'nin' => $agentService->nin, // Alternative
                ]);

            $apiResponse = $response->json();

            if ($response->successful() && isset($apiResponse['success']) && $apiResponse['success']) {
                $data = $apiResponse['data'] ?? [];
                
                $updateData = [];
                // Map status
                if (isset($data['status'])) {
                    $updateData['status'] = $this->normalizeStatus($data['status']);
                }
                
                // Map comment (check for 'comment' first, then 'reason' as fallback)
                if (isset($data['comment'])) {
                    $updateData['comment'] = $data['comment'];
                } elseif (isset($data['reason'])) {
                    $updateData['comment'] = $data['reason'];
                }

                // Map file url
                if (isset($data['file_url'])) {
                    $updateData['file_url'] = $data['file_url'];
                }

                if (!empty($updateData)) {
                    $isFailingNow = isset($updateData['status']) && $updateData['status'] === 'failed' && $agentService->status !== 'failed';

                    if ($isFailingNow) {
                        DB::beginTransaction();
                        try {
                            $agentService->update($updateData);

                            $wallet = Wallet::where('user_id', $agentService->user_id)->lockForUpdate()->first();
                            if ($wallet) {
                                $wallet->increment('balance', $agentService->amount);

                                Transaction::create([
                                    'transaction_ref' => 'REF_' . $agentService->reference,
                                    'user_id'         => $agentService->user_id,
                                    'amount'          => $agentService->amount,
                                    'performed_by'    => 'System Auto-Refund',
                                    'description'     => "Refund for failed NIN Modification Request",
                                    'type'            => 'credit',
                                    'status'          => 'completed',
                                    'metadata'        => [
                                        'original_reference' => $agentService->reference,
                                        'api_reason'         => $updateData['comment'] ?? 'Failed submission',
                                    ],
                                ]);
                            }

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('NIN Modification Auto Refund Error', ['error' => $e->getMessage(), 'submission_id' => $agentService->id]);
                            throw $e;
                        }
                    } else {
                        $agentService->update($updateData);
                    }
                }

                return back()->with('success', 'Status updated successfully. Current status: ' . ucfirst($agentService->status));
            }

            return back()->with('error', 'Unable to fetch status: ' . ($apiResponse['message'] ?? 'Unknown error.'));

        } catch (\Exception $e) {
            Log::error('NIN Modification Status Check Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Connection Error: Unable to reach service provider.');
        }
    }

    public function batchCheck()
    {
        try {
            $pendingSubmissions = AgentService::where('service_type', 'nin_modification')
                ->whereIn('status', ['pending', 'processing'])
                ->where(function($query) {
                    $query->where('updated_at', '<', now()->subMinutes(10))
                          ->orWhereNull('updated_at');
                })
                ->limit(20)
                ->get();

            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $url = rtrim($apiBaseUrl, '/') . '/nin/modification';

            $checked = 0;

            foreach ($pendingSubmissions as $submission) {
                try {
                    $response = Http::withToken($apiKey)
                        ->acceptJson()
                        ->get($url, [
                            'reference' => $submission->reference,
                        ]);

                    $apiResponse = $response->json();

                    if ($response->successful() && isset($apiResponse['success']) && $apiResponse['success']) {
                        $data = $apiResponse['data'] ?? [];
                        
                        $updateData = [];
                        if (isset($data['status'])) {
                            $updateData['status'] = $this->normalizeStatus($data['status']);
                        }
                        
                        if (isset($data['comment'])) {
                            $updateData['comment'] = $data['comment'];
                        } elseif (isset($data['reason'])) {
                            $updateData['comment'] = $data['reason'];
                        }

                        if (isset($data['file_url'])) {
                            $updateData['file_url'] = $data['file_url'];
                        }

                        if (!empty($updateData)) {
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
                                            'description'     => "Refund for failed NIN Modification",
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
                                    Log::error('NIN Modification Auto Refund Error in Batch', ['error' => $e->getMessage(), 'submission_id' => $submission->id]);
                                }
                            } else {
                                $submission->update($updateData);
                            }
                        }
                        $checked++;
                    }
                } catch (\Exception $e) {
                    Log::error('Batch check error for NIN Modification ' . $submission->id . ': ' . $e->getMessage());
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Batch check completed. Checked {$checked} submissions.",
                'checked' => $checked
            ]);

        } catch (\Exception $e) {
            Log::error('Batch Check Error (NIN Modification): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Batch check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'pending', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            'query', 'queried' => 'query',
            default => 'pending',
        };
    }
}
