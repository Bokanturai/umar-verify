<?php

namespace App\Http\Controllers\Agency;

use App\Models\ServiceField;
use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class BvnModificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch only bank-related services
        $bankServices = Service::with(['fields' => function ($query) {
            $query->where('is_active', 1);
        }])
            ->where('name', 'like', '%BANK%')
            ->get();

        $query = AgentService::where('user_id', $user->id)
            ->where('service_type', 'bvn_modification');

        // Apply optional filters
        if ($request->filled('search')) {
            $query->where('bvn', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bank')) {
            $query->where('bank', $request->bank);
        }

        // Paginate results
        $crmSubmissions = $query->orderByDesc('submission_date')
            ->paginate(10)
            ->withQueryString();

        // Distinct user-specific banks (for dropdown)
        $userBanks = AgentService::where('user_id', $user->id)
            ->whereNotNull('bank')
            ->where('bank', '<>', '')
            ->distinct()
            ->pluck('bank');

        // Ensure wallet exists
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        // Fetch affidavit field and price for the user
        $role = $user->role ?? 'user';
        $affidavitField = ServiceField::where('field_code', '900')->first();
        $affidavitPrice = 0;
        if ($affidavitField) {
            $affidavitPrice = $affidavitField->prices()
                ->where('user_type', $role)
                ->value('price') ?? $affidavitField->base_price;
        }

        // Return view with data
        return view('bvn.modification', compact(
            'userBanks',
            'crmSubmissions',
            'bankServices',
            'wallet',
            'affidavitPrice'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'enrolment_bank' => 'required|exists:services,id',
            'service_field'  => 'required|exists:service_fields,id',
            'bank'           => 'nullable|string|max:255',
            'bvn'            => 'required|string|size:11',
            'nin'            => 'required|string|size:11',
            'affidavit'      => 'required|in:available,not_available',
            'affidavit_file' => 'nullable|file|mimes:pdf|max:5120',
        ];

        if ($request->has('modification_data')) {
            $rules['modification_data'] = 'required|array';
        } else {
            $rules['description'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules);

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        if ($wallet->status !== 'active') {
            return back()->with([
                'status' => 'error',
                'message' => 'Your wallet is not active.',
            ])->withInput();
        }

        $role = $user->role ?? 'user';
        $service = Service::findOrFail($validated['enrolment_bank']);
        $serviceField = ServiceField::findOrFail($validated['service_field']);

        // Calculate prices
        $modificationFee = $serviceField->prices()
            ->where('user_type', $role)
            ->value('price') ?? $serviceField->base_price;

        $affidavitField = ServiceField::where('field_code', '900')->firstOrFail();

        $affidavitFee = $affidavitField->prices()
            ->where('user_type', $role)
            ->value('price') ?? $affidavitField->base_price;

        $affidavitUploaded = $request->hasFile('affidavit_file');
        $chargeAffidavit = !$affidavitUploaded;

        $totalAmount = $modificationFee + ($chargeAffidavit ? $affidavitFee : 0);

        if ($wallet->balance < $totalAmount) {
            $msg = "Insufficient wallet balance. Required: NGN " . number_format($totalAmount, 2);
            return redirect()->route('modification')->withErrors(['wallet' => $msg])->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle affidavit upload
            $fileName = null;
            $fileUrl = null;
            
            if ($affidavitUploaded) {
                $file = $request->file('affidavit_file');
                $fileName = 'affidavit_' . Str::slug($user->email) . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store in storage/app/public/uploads/affidavits
                $path = $file->storeAs('uploads/affidavits', $fileName, 'public');
                $fileUrl = Storage::disk('public')->url($path);
            }

            // Debit wallet
            $wallet->decrement('balance', $totalAmount);

            // API Call to Arewa Smart
            $apiKey = env('AREWA_API_TOKEN');
            $apiBaseUrl = env('AREWA_BASE_URL');
            $apiUrl = rtrim($apiBaseUrl, '/') . '/bvn/modification';

            // Prepare description payload as a string
            $description = $validated['description'] ?? '';
            if ($request->has('modification_data')) {
                $m = $request->modification_data;
                // Name correction codes often need space-separated fields. 
                // Using common codes for BVN name correction: 022, 003, 007, 69
                if (in_array($serviceField->field_code, ['022', '003', '007', '69'])) {
                    $description = trim(($m['first_name'] ?? '') . ' ' . ($m['middle_name'] ?? '') . ' ' . ($m['surname'] ?? ''));
                } else {
                    $description = json_encode($m);
                }
            }

            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->post($apiUrl, [
                        'field_code'  => $serviceField->field_code,
                        'bvn'         => $validated['bvn'],
                        'nin'         => $validated['nin'],
                        'description' => $description, // Sent as a string
                    ]);

                $apiData = $response->json();

                if (!$response->successful() || (isset($apiData['success']) && $apiData['success'] === false)) {
                    Log::error('Arewa Smart API BVN Modification Failed', [
                        'response' => $apiData,
                        'payload' => [
                            'field_code' => $serviceField->field_code,
                            'nin' => $validated['nin']
                        ]
                    ]);
                    // If API fails, we could potentially refund, but usually we throw and let the catch handle it
                    throw new \Exception('API Submission Failed: ' . ($apiData['message'] ?? 'Unknown API error.'));
                }
            } catch (\Exception $e) {
                Log::error('Arewa Smart API Connection Error', ['error' => $e->getMessage()]);
                throw $e;
            }

            $transactionRef = $apiData['data']['reference'] ?? ('M1' . date('is') . strtoupper(Str::random(5)));
            $performedBy = trim("{$user->first_name} {$user->last_name}");

            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'description' => "BVN modification for {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => $service->name,
                    'service_field' => $serviceField->field_name,
                    'field_code'    => $serviceField->field_code,
                    'bvn' => $validated['bvn'],
                    'nin' => $validated['nin'],
                    'price_details' => [
                        'modification_fee' => $modificationFee,
                        'affidavit_fee' => $chargeAffidavit ? $affidavitFee : 0,
                    ],
                    'api_response' => $apiData
                ],
            ]);

            // Store submission
            AgentService::create([
                'reference' => $transactionRef,
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'service_name' => $service->name,
                'field_code' => $serviceField->field_code,
                'service_field_name' => $serviceField->field_name, // Fixed field name mapping
                'bank' => $service->name,
                'bvn' => $validated['bvn'],
                'nin' => $validated['nin'],
                'description' => $description,
                'modification_data' => $request->modification_data ?? null,
                'amount' => $totalAmount,
                'affidavit_file' => $fileName,
                'affidavit' => $validated['affidavit'],
                'affidavit_file_url' => $fileUrl,
                'transaction_id' => $transaction->id,
                'submission_date' => now(),
                'status' => 'pending',
                'service_type' => 'bvn_modification',
                'comment' => $apiData['message'] ?? null,
                'performed_by' => $performedBy,
            ]);

            DB::commit();

            $msg = "BVN Modification Submitted Successfully. Charged: NGN " . number_format($totalAmount, 2);
            return redirect()->route('modification')->with([
                'status' => 'success',
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if exists
            if ($affidavitUploaded && isset($fileName)) {
                Storage::disk('public')->delete('uploads/affidavits/' . $fileName);
            }

            return redirect()->route('modification')->withErrors([
                'error' => 'Something went wrong: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    // AJAX endpoint: fetch active service fields for a given service
    public function getServiceFields($serviceId)
    {
        $role = auth()->user()->role ?? 'user';

        $fields = ServiceField::where('service_id', $serviceId)
            ->where('is_active', 1)
            ->get();

        // Fallback: If no fields, look for Agency Banking
        if ($fields->isEmpty()) {
            $agencyBanking = Service::where('name', 'Agency Banking')->first();
            if ($agencyBanking && $agencyBanking->id != $serviceId) {
                $fields = ServiceField::where('service_id', $agencyBanking->id)
                    ->where('is_active', 1)
                    ->get();
            }
        }

        $mappedFields = $fields->map(function ($field) use ($role) {
            $price = $field->prices()->where('user_type', $role)->value('price') ?? $field->base_price;
            return [
                'id' => $field->id,
                'field_name' => $field->field_name,
                'description' => $field->description,
                'price' => $price,
            ];
        });

        return response()->json($mappedFields);
    }

    /**
     * Check Status of BVN Modification.
     */
    public function checkStatus(Request $request, $id)
    {
        $agentService = AgentService::findOrFail($id);

        $apiKey = env('AREWA_API_TOKEN');
        $apiBaseUrl = env('AREWA_BASE_URL');
        $apiUrl = rtrim($apiBaseUrl, '/') . '/bvn/modification';

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->get($apiUrl, [
                    'reference' => $agentService->reference,
                    // 'bvn' => $agentService->bvn, // Alternative
                ]);

            $apiResponse = $response->json();

            if ($response->successful() && isset($apiResponse['success']) && $apiResponse['success']) {
                $data = $apiResponse['data'] ?? [];
                
                $updateData = [];

                // Map basic identifiers if present
                if (isset($data['bvn'])) $updateData['bvn'] = $data['bvn'];
                if (isset($data['nin'])) $updateData['nin'] = $data['nin'];
                
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

                // Map description if provided by API
                if (isset($data['description'])) {
                    $updateData['description'] = is_array($data['description']) ? json_encode($data['description']) : $data['description'];
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
                                    'description'     => "Refund for failed BVN Modification Request",
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
                            Log::error('BVN Modification Auto Refund Error', ['error' => $e->getMessage(), 'submission_id' => $agentService->id]);
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
            Log::error('BVN Modification Status Check Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Connection Error: Unable to reach service provider.');
        }
    }

    private function normalizeStatus($status): string
    {
        $s = strtolower(trim((string) $status));
        
        return match ($s) {
            'successful', 'success', 'resolved', 'approved', 'completed' => 'successful',
            'processing', 'in_progress', 'in-progress', 'submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            'query', 'queried' => 'query',
            default => 'pending',
        };
    }
}