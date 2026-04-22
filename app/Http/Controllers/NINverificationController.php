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
use App\Repositories\NIN_PDF_Repository;
use Carbon\Carbon;

class NINverificationController extends Controller
{
    /**
     * Show NIN verification page
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get Verification Service using ServiceManager
        $service = ServiceManager::getServiceWithFields('Verification', [
            ['name' => 'Verify NIN', 'code' => '610', 'price' => 80],
            ['name' => 'standard slip', 'code' => '611', 'price' => 100],
            ['name' => 'preminum slip', 'code' => '612', 'price' => 150],
            ['name' => '1Vnin slip', 'code' => '616', 'price' => 100],
        ]);
        
        // Get Prices
        $verificationPrice = 0;
        $standardSlipPrice = 0;
        $premiumSlipPrice = 0;
        $vninSlipPrice = 0;

        if ($service) {
            $verificationField = $service->fields()->where('field_code', '610')->first();
            $standardSlipField = $service->fields()->where('field_code', '611')->first();
            $premiumSlipField = $service->fields()->where('field_code', '612')->first();
            $vninSlipField = $service->fields()->where('field_code', '616')->first();

            $verificationPrice = $verificationField ? $verificationField->getPriceForUserType($user->role) : 0;
            $standardSlipPrice = $standardSlipField ? $standardSlipField->getPriceForUserType($user->role) : 0;
            $premiumSlipPrice = $premiumSlipField ? $premiumSlipField->getPriceForUserType($user->role) : 0;
            $vninSlipPrice = $vninSlipField ? $vninSlipField->getPriceForUserType($user->role) : 0;
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        return view('verification.nin-verification', [
            'wallet' => $wallet,
            'verificationPrice' => $verificationPrice,
            'standardSlipPrice' => $standardSlipPrice,
            'premiumSlipPrice' => $premiumSlipPrice,
            'vninSlipPrice' => $vninSlipPrice,
        ]);
    }

    /**
     * Store new NIN verification request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'number_nin' => 'required|string|size:11|regex:/^[0-9]{11}$/',
        ]);

        // 1. Get Service and Field
        $service = ServiceManager::getServiceWithFields('Verification', [
            ['name' => 'Verify NIN', 'code' => '610', 'price' => 80],
        ]);

        if (!$service) {
            return back()->with(['status' => 'error', 'message' => 'Verification service not available.']);
        }

        $serviceField = $service->fields()
            ->where('field_code', '610')
            ->where('is_active', true)
            ->first();

        if (!$serviceField) {
            return back()->with(['status' => 'error', 'message' => 'NIN verification service is not available.']);
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
                'description' => "NIN Verification - {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => 'verification',
                    'service_field' => $serviceField->field_name,
                    'nin' => $request->number_nin,
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
                'nin' => $request->number_nin,
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private2'));
            $url = config('services.validator.domain') . '/api/validator-service/open/nin/inquire';

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

            if ($respCode === '00000000') {
                // SUCCESS
                $this->finalizeSuccessTransaction($transaction, $serviceField, $service, $decodedData);
                session()->flash('verification', $decodedData);
                return redirect()->route('nin.verification.index')->with([
                    'status' => 'success',
                    'message' => "NIN Verification successful. Charged: NGN " . number_format($servicePrice, 2),
                ]);
            } elseif ($respCode === '99120010') {
                // NIN NOT FOUND (Still charged as per business rule)
                $this->updateTransactionMetadata($transaction, $decodedData, 'NIN_NOT_FOUND');
                return back()->with([
                    'status' => 'error',
                    'message' => "NIN does not exist. You have been charged NGN " . number_format($servicePrice, 2) . " for this search."
                ]);
            } else {
                // REFUNDABLE ERROR (Param errors, system errors)
                $this->refundTransaction($transaction, $wallet, $decodedData);
                $message = $decodedData['respDescription'] ?? 'Verification failed';
                if ($respCode == '99120012') $message = 'Parameter error in the interface call.';
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
    private function finalizeSuccessTransaction($transaction, $serviceField, $service, $ninData)
    {
        $user = Auth::user();
        $performedBy = $user->first_name . ' ' . $user->last_name;

        DB::beginTransaction();
        try {
            // Update Transaction Metadata
            $transaction->update([
                'metadata' => array_merge($transaction->metadata, [
                    'field_code' => $serviceField->field_code,
                    'api_response' => $ninData,
                    'result' => 'SUCCESS',
                ])
            ]);

            Verification::create([
                'user_id' => $user->id,
                'service_field_id' => $serviceField->id,
                'service_id' => $service->id,
                'transaction_id' => $transaction->id,
                'reference' => $transaction->transaction_ref,
                'number_nin' => $ninData['data']['nin'] ?? '',
                'firstname' => $ninData['data']['firstName'] ?? '',
                'middlename' => $ninData['data']['middleName'] ?? '',
                'surname' => $ninData['data']['surname'] ?? '',
                'birthdate' =>  $ninData['data']['birthDate'] ?? '',
                'gender' => $ninData['data']['gender'] ?? '',
                'telephoneno' => $ninData['data']['telephoneNo'] ?? '',
                'photo_path' => $ninData['data']['photo'] ?? '',
                'performed_by'    => $performedBy,
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
                'description' => "Refund for Failed NIN Verification",
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
                ['name' => 'standard slip', 'code' => '611', 'price' => 100],
                ['name' => 'preminum slip', 'code' => '612', 'price' => 150],
                ['name' => '1Vnin slip', 'code' => '616', 'price' => 100],
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
     * Download NIN slips
     */
    public function standardSlip($nin_no)
    {
        try {
            $this->chargeForSlip(Auth::user(), '611'); // Charge for Standard Slip
            
            $repObj = new NIN_PDF_Repository();
            return $repObj->standardPDF($nin_no);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function premiumSlip($nin_no)
    {
        try {
            $this->chargeForSlip(Auth::user(), '612'); // Charge for Premium Slip
            
            $repObj = new NIN_PDF_Repository();
            return $repObj->premiumPDF($nin_no);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function vninSlip($nin_no)
    {
        try {
            $this->chargeForSlip(Auth::user(), '616'); // Charge for VNIN Slip
            
            $repObj = new NIN_PDF_Repository();
            return $repObj->vninPDF($nin_no);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
