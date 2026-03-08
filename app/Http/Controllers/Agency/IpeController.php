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

class IpeController extends Controller
{
    public function index(Request $request)
    {
        $ipeService = Service::where('name', 'IPE')->first();
        $ipeFields = $ipeService ? $ipeService->fields : collect();

        $services = collect();
        $user = Auth::user();
        $role = $user->role ?? 'user';
        
        foreach ($ipeFields as $field) {
            $price = $field->prices()->where('user_type', $role)->value('price') ?? $field->base_price;
            $services->push([
                'id' => $field->id,
                'name' => $field->field_name,
                'price' => $price,
                'type' => 'ipe',
                'service_id' => $field->service_id,
                'field_code' => $field->field_code ?? '002'
            ]);
        }
        
        $wallet = Wallet::where('user_id', Auth::id())->first();
        
        $query = AgentService::where('user_id', Auth::id())
            ->where('service_type', 'IPE'); // Filter by IPE service type

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('tracking_id', 'like', "%{$searchTerm}%")
                  ->orWhere('reference', 'like', "%{$searchTerm}%");
            });
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

        return view('nin.ipe', compact('services', 'wallet', 'submissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_field' => 'required',
            'tracking_id' => 'required|string|min:10|max:50',
            'description' => 'nullable|string|max:255',
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
        $apiUrl = rtrim($apiBaseUrl, '/') . '/nin/ipe';

        $payload = [
            'field_code' => $serviceField->field_code ?? '002',
            'tracking_id' => $request->tracking_id,
            'description' => $request->description ?? 'My Reference',
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post($apiUrl, $payload);
            
            $data = $response->json();

            if (!$response->successful() || !($data['success'] ?? false)) {
                return back()->with('error', 'API Submission Failed: ' . ($data['message'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
            Log::error('IPE API Error: ' . $e->getMessage());
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
                'description' => "IPE Clearance for {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $serviceField->service->name,
                    'service_field' => $serviceField->field_name,
                    'tracking_id' => $request->tracking_id,
                ],
            ]);

            $status = $this->normalizeStatus($data['data']['status'] ?? $data['status'] ?? 'processing');

            AgentService::create([
                'reference' => $data['data']['reference'] ?? 'REF-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'field_code' => $serviceField->field_code,
                'transaction_id' => $transaction->id,
                'service_type' => 'IPE',
                'tracking_id' => $request->tracking_id,
                'amount' => $servicePrice,
                'status' => $status,
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'comment' => $cleanResponse,
                'performed_by' => $performedBy,
            ]);

            DB::commit();
            return back()->with('success', 'IPE request submitted successfully. Status: ' . $status);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('IPE Transaction Error: ' . $e->getMessage());
            return back()->with('error', 'System Error: Failed to record transaction. Please contact support.');
        }
    }

    public function check($id)
    {
        try {
            $agentService = AgentService::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('service_type', 'IPE')
                ->firstOrFail();

            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $url = rtrim($apiBaseUrl, '/') . '/nin/ipe';
            
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->get($url, [
                    'tracking_id' => $agentService->tracking_id,
                ]);
            
            $apiResponse = $response->json();

            if (!$response->successful()) {
                return back()->with('error', 'Failed to check status: ' . ($apiResponse['message'] ?? 'API Error'));
            }

            $cleanResponse = $this->cleanApiResponse($apiResponse);

            $updateData = [
                'comment' => $apiResponse['comment'] ?? $cleanResponse,
            ];

            if (isset($apiResponse['status'])) {
                $updateData['status'] = $this->normalizeStatus($apiResponse['status']);
            }

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
                            'description'     => "Refund for failed IPE Request",
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
                    Log::error('IPE Auto Refund Error', ['error' => $e->getMessage(), 'submission_id' => $agentService->id]);
                    throw $e;
                }
            } else {
                $agentService->update($updateData);
            }

            return back()->with('success', 'Status checked successfully. Current status: ' . $agentService->status);

        } catch (\Exception $e) {
            Log::error('IPE Status Check Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to complete the status check. Please try again.');
        }
    }

    public function details($id)
    {
        try {
            $submission = AgentService::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('service_type', 'IPE')
                ->firstOrFail();

            return response()->json([
                'id' => $submission->id,
                'tracking_id' => $submission->tracking_id,
                'reference' => $submission->reference,
                'service_field_name' => $submission->service_field_name,
                'status' => $submission->status,
                'amount' => $submission->amount,
                'description' => $submission->description,
                'comment' => $submission->comment,
                'details' => $submission->details ?? null,
                'created_at' => $submission->created_at,
                'last_checked_at' => $submission->updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    }

    public function batchCheck()
    {
        try {
            $pendingSubmissions = AgentService::where('service_type', 'IPE')
                ->whereIn('status', ['pending', 'processing'])
                ->where(function($query) {
                    $query->where('updated_at', '<', now()->subMinutes(30))
                          ->orWhereNull('updated_at');
                })
                ->limit(20)
                ->get();

            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $url = rtrim($apiBaseUrl, '/') . '/nin/ipe';

            $checked = 0;

            foreach ($pendingSubmissions as $submission) {
                try {
                    $response = Http::withToken($apiKey)
                        ->acceptJson()
                        ->get($url, [
                            'tracking_id' => $submission->tracking_id,
                        ]);

                    $apiResponse = $response->json();

                    if ($response->successful() && ($apiResponse['success'] ?? false)) {
                        $cleanResponse = $this->cleanApiResponse($apiResponse);
                        
                        $updateData = [
                            'comment' => $apiResponse['comment'] ?? $cleanResponse,
                        ];

                        if (isset($apiResponse['status'])) {
                            $updateData['status'] = $this->normalizeStatus($apiResponse['status']);
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
                                        'description'     => "Refund for failed IPE Request",
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
                                Log::error('IPE Auto Refund Error in Batch', ['error' => $e->getMessage(), 'submission_id' => $submission->id]);
                            }
                        } else {
                            $submission->update($updateData);
                        }
                        $checked++;
                    }
                } catch (\Exception $e) {
                    Log::error('Batch check error for submission ' . $submission->id . ': ' . $e->getMessage());
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Batch check completed. Checked {$checked} submissions.",
                'checked' => $checked
            ]);

        } catch (\Exception $e) {
            Log::error('Batch Check Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Batch check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            $toKeep = array_diff_key($response, array_flip(['status', 'message', 'response', 'success']));
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