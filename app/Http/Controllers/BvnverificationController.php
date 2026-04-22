<?php

namespace App\Http\Controllers;

use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Helpers\ServiceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Wallet;
use App\Repositories\BVN_PDF_Repository;
use Carbon\Carbon;

class BvnverificationController extends Controller
{

    /**
     * Show BVN verification page
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get Verification Service using ServiceManager
        $service = ServiceManager::getServiceWithFields('Verification', [
            ['name' => 'Bvn verification', 'code' => '600', 'price' => 70],
            ['name' => 'standard slip', 'code' => '601', 'price' => 50],
            ['name' => 'preminum slip', 'code' => '602', 'price' => 100],
            ['name' => 'plastic slip', 'code' => '603', 'price' => 150],
        ]);
        
        // Get Prices
        $verificationPrice = 0;
        $standardSlipPrice = 0;
        $premiumSlipPrice = 0;
        $plasticSlipPrice = 0;

        if ($service) {
            $verificationField = $service->fields()->where('field_code', '600')->first();
            $standardSlipField = $service->fields()->where('field_code', '601')->first();
            $premiumSlipField = $service->fields()->where('field_code', '602')->first();
            $plasticSlipField = $service->fields()->where('field_code', '603')->first();

            $verificationPrice = $verificationField ? $verificationField->getPriceForUserType($user->role) : 0;
            $standardSlipPrice = $standardSlipField ? $standardSlipField->getPriceForUserType($user->role) : 0;
            $premiumSlipPrice = $premiumSlipField ? $premiumSlipField->getPriceForUserType($user->role) : 0;
            $plasticSlipPrice = $plasticSlipField ? $plasticSlipField->getPriceForUserType($user->role) : 0;
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        return view('verification.bvn-verification', [
            'wallet' => $wallet,
            'verificationPrice' => $verificationPrice,
            'standardSlipPrice' => $standardSlipPrice,
            'premiumSlipPrice' => $premiumSlipPrice,
            'plasticSlipPrice' => $plasticSlipPrice,
        ]);
    }

    /**
     * Store new BVN verification request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bvn' => 'required|string|size:11|regex:/^[0-9]{11}$/',
        ]);

        // 1. Get Service and Field
        $service = ServiceManager::getServiceWithFields('Verification', [
            ['name' => 'Bvn verification', 'code' => '600', 'price' => 70],
        ]);

        if (!$service) {
            return back()->with(['status' => 'error', 'message' => 'Verification service not available.']);
        }

        $serviceField = $service->fields()
            ->where('field_code', '600')
            ->where('is_active', true)
            ->first();

        if (!$serviceField) {
            return back()->with(['status' => 'error', 'message' => 'BVN verification service is not available.']);
        }

        $servicePrice = $serviceField->getPriceForUserType($user->role);

        // 2. Atomic Debit (Debit First to prevent race conditions)
        $transactionRef = 'Ver-' . (time() % 1000000000) . '-' . mt_rand(100, 999);
        $performedBy = $user->first_name . ' ' . $user->last_name;

        DB::beginTransaction();
        try {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($wallet->status !== 'active') {
                throw new \Exception('Your wallet is not active.');
            }

            if ($wallet->balance < $servicePrice) {
                throw new \Exception('Insufficient wallet balance. You need NGN ' . number_format($servicePrice - $wallet->balance, 2));
            }

            // Deduct immediately
            $wallet->decrement('balance', $servicePrice);

            // Create Transaction Record (initial state)
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "BVN Verification - {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed', // We mark as completed because money is taken
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => 'verification',
                    'service_field' => $serviceField->field_name,
                    'bvn' => $request->bvn,
                    'user_role' => $user->role,
                ],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['status' => 'error', 'message' => $e->getMessage()]);
        }

        // 3. API Call
        try {
            $requestTime = (int) (microtime(true) * 1000);
            $noncestr = noncestrHelper::generateNonceStr();
            $data = [
                'version' => config('services.validator.version'),
                'nonceStr' => $noncestr,
                'requestTime' => $requestTime,
                'bvn' => $request->bvn,
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private2'));
            $url = config('services.validator.domain') . '/api/validator-service/open/bvn/inquire';

            $response = Http::withHeaders([
                'Accept' => 'application/json, text/plain, */*',
                'CountryCode' => 'NG',
                'Signature' => $signature,
                'Authorization' => 'Bearer ' . config('services.validator.bearer'),
            ])->timeout(45)->post($url, $data);

            if ($response->failed()) {
                throw new \Exception('API Connection Failed');
            }

            $decodedData = $response->json();
            $respCode = $decodedData['respCode'] ?? 'UNKNOWN';

            // Define Charged Error Codes
            $chargedErrorCodes = ['99120020', '99120024', '99120026', '99120027', '99120028', '99120029'];

            if ($respCode === '00000000') {
                // SUCCESS
                $this->finalizeSuccessTransaction($transaction, $serviceField, $service, $decodedData);
                session()->flash('verification', $decodedData);
                return redirect()->route('bvn.verification.index')->with([
                    'status' => 'success',
                    'message' => "BVN Verification successful. Charged: NGN " . number_format($servicePrice, 2),
                ]);
            } elseif (in_array($respCode, $chargedErrorCodes)) {
                // FAILED BUT CHARGEABLE
                $this->updateTransactionMetadata($transaction, $decodedData, 'BVN_ERROR_CHARGED');
                $msg = $decodedData['respDescription'] ?? 'Verification failed';
                if ($respCode == '99120020') $msg = 'BVN does not exist';
                if ($respCode == '99120024') $msg = 'BVN suspended';

                return back()->with([
                    'status' => 'error',
                    'message' => "$msg. You have been charged NGN " . number_format($servicePrice, 2) . " for this search."
                ]);
            } else {
                // REFUNDABLE ERROR
                $this->refundTransaction($transaction, $wallet, $decodedData);
                $message = $decodedData['respDescription'] ?? 'Verification failed';
                if ($respCode == '99120012') $message = 'Parameters wrong';
                return back()->with(['status' => 'error', 'message' => "$message. Amount has been refunded."]);
            }

        } catch (\Exception $e) {
            // SYSTEM ERROR -> REFUND
            $this->refundTransaction($transaction, $wallet, ['respDescription' => 'System Error: ' . $e->getMessage()]);
            return back()->with(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage() . '. Amount refunded.']);
        }
    }

    /**
     * Finalize successful transaction (Verification Record)
     */
    private function finalizeSuccessTransaction($transaction, $serviceField, $service, $bvnData)
    {
        $user = Auth::user();
        $performedBy = $user->first_name . ' ' . $user->last_name;

        DB::beginTransaction();
        try {
            // Update Transaction Metadata
            $transaction->update([
                'metadata' => array_merge($transaction->metadata, [
                    'field_code' => $serviceField->field_code,
                    'api_response' => $bvnData,
                    'result' => 'SUCCESS',
                ])
            ]);

            Verification::create([
                'user_id' => $user->id,
                'service_field_id' => $serviceField->id,
                'service_id' => $service->id,
                'transaction_id' => $transaction->id,
                'reference' => $transaction->transaction_ref,
                'idno' => $bvnData['data']['bvn'] ?? '',
                'firstname' => $bvnData['data']['firstName'] ?? '',
                'middlename' => $bvnData['data']['middleName'] ?? '',
                'surname' => $bvnData['data']['lastName'] ?? '',
                'birthdate' =>  $bvnData['data']['birthday'] ?? '',
                'gender' => $bvnData['data']['gender'] ?? '',
                'telephoneno' => $bvnData['data']['phoneNumber'] ?? '',
                'photo_path' => $bvnData['data']['photo'] ?? '',
                'performed_by' => $performedBy,
                'submission_date' => Carbon::now()
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    /**
     * Update transaction metadata for chargeable failures
     */
    private function updateTransactionMetadata($transaction, $data, $resultCode)
    {
        $transaction->update([
            'metadata' => array_merge($transaction->metadata, [
                'result' => $resultCode,
                'api_code' => $data['respCode'] ?? 'UNKNOWN',
                'api_message' => $data['respDescription'] ?? 'Verification Failed',
            ])
        ]);
    }

    /**
     * Refund a failed transaction
     */
    private function refundTransaction($transaction, $wallet, $data)
    {
        DB::beginTransaction();
        try {
            // Check if already refunded to prevent double refund
            if (($transaction->metadata['refunded'] ?? false) === true) {
                DB::rollBack();
                return;
            }

            // 1. Update Transaction Status
            $transaction->update([
                'status' => 'failed',
                'metadata' => array_merge($transaction->metadata, [
                    'result' => 'REFUNDED',
                    'refunded' => true,
                    'api_message' => $data['respDescription'] ?? 'Refunded due to error',
                ])
            ]);

            // 2. Refund Wallet
            $wallet->increment('balance', $transaction->amount);

            // 3. Create Refund Transaction Record
            Transaction::create([
                'transaction_ref' => 'REF-' . $transaction->transaction_ref,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'description' => "Refund for Failed BVN Verification",
                'type' => 'credit',
                'status' => 'completed',
                'performed_by' => 'SYSTEM',
                'metadata' => [
                    'original_ref' => $transaction->transaction_ref,
                    'reason' => $data['respDescription'] ?? 'System Error',
                ],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    /**
     * Charge for Slip Download
     */
    private function chargeForSlip($user, $fieldCode)
    {
        DB::beginTransaction();
        try {
             // 1. Get Verification Service using ServiceManager
             $service = ServiceManager::getServiceWithFields('Verification', [
                ['name' => 'standard slip', 'code' => '601', 'price' => 50],
                ['name' => 'preminum slip', 'code' => '602', 'price' => 100],
                ['name' => 'plastic slip', 'code' => '603', 'price' => 150],
            ]);

            if (!$service) {
                throw new \Exception('Verification service not available.');
            }

            // 2. Get ServiceField
            $serviceField = $service->fields()
                ->where('field_code', $fieldCode)
                ->where('is_active', true)
                ->first();

            if (!$serviceField) {
                 throw new \Exception('Slip service not available.');
            }

            // 3. Determine service price based on user role
            $servicePrice = $serviceField->getPriceForUserType($user->role);

            // 4. Check wallet
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($wallet->status !== 'active') {
                 throw new \Exception('Your wallet is not active.');
            }

            if ($wallet->balance < $servicePrice) {
                 throw new \Exception('Insufficient wallet balance.');
            }
            
            $transactionRef = 'Slip-' . (time() % 1000000000) . '-' . mt_rand(100, 999);
            $performedBy = $user->first_name . ' ' . $user->last_name;

            Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "Slip Download: {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by'    => $performedBy,
                'metadata' => [
                    'service' => 'slip_download',
                    'service_field' => $serviceField->field_name,
                    'field_code' => $serviceField->field_code,
                    'user_role' => $user->role,
                    'price_details' => [
                        'base_price' => $serviceField->base_price,
                        'user_price' => $servicePrice,
                    ],
                ],
            ]);

            // Deduct wallet balance
            $wallet->decrement('balance', $servicePrice);
            
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Download PDF slips
     */
    public function standardBVN($bvn_no)
    {
        try {
            $this->chargeForSlip(Auth::user(), '601'); // Charge for Standard Slip
            
            if (Verification::where('idno', $bvn_no)->exists()) {
                $veridiedRecord = Verification::where('idno', $bvn_no)
                    ->latest()
                    ->first();

                $view = view('freeBVN', compact('veridiedRecord'))->render();
                return response()->json(['view' => $view]);
            } else {
                return response()->json([
                    "message" => "Error",
                    "errors" => array("Not Found" => "Verification record not found !")
                ], 422);
            }
        } catch (\Exception $e) {
             return response()->json([
                "message" => "Error",
                "errors" => array("Charge Failed" => $e->getMessage())
            ], 422);
        }
    }

    public function premiumBVN($bvn_no)
    {
        try {
            $this->chargeForSlip(Auth::user(), '602'); // Charge for Premium Slip

            if (Verification::where('idno', $bvn_no)->exists()) {
                $veridiedRecord = Verification::where('idno', $bvn_no)
                    ->latest()
                    ->first();

                $view = view('PremiumBVN', compact('veridiedRecord'))->render();
                return response()->json(['view' => $view]);
            } else {
                return response()->json([
                    "message" => "Error",
                    "errors" => array("Not Found" => "Verification record not found !")
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
               "message" => "Error",
               "errors" => array("Charge Failed" => $e->getMessage())
           ], 422);
       }
    }

    public function plasticBVN($bvn_no)
    {
         try {
            $this->chargeForSlip(Auth::user(), '603'); // Charge for Plastic Slip
            
            $repObj = new BVN_PDF_Repository();
            return $repObj->plasticPDF($bvn_no);
         } catch (\Exception $e) {
             // For plastic PDF, we might need to return a view or redirect with error since it's a direct link
             return back()->with('error', $e->getMessage());
        }
    }
}
