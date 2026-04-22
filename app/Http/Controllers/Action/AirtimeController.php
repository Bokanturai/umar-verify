<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirtimeController extends Controller
{
    protected $loginUserId;
    
    // API Configuration - loaded from .env
    private function getApiBaseUrl()
    {
        return env('AREWA_BASE_URL', 'https://api.arewasmart.com.ng/api/v1');
    }

    private function getApiToken()
    {
        return env('AREWA_API_TOKEN');
    }

    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    /**
     * Show Airtime purchase form
     */
    public function airtime()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        return view('utilities.index', [
            'user'   => $user,
            'wallet' => $wallet,
        ]);
    }

    /**
     * Handle Airtime Purchase
     */
    /**
     * Handle Airtime Purchase
     */
    public function buyAirtime(Request $request)
    {
        $request->validate([
            'network'   => ['required', 'string', 'in:mtn,airtel,glo,etisalat'],
            'mobileno'  => 'required|numeric|digits:11',
            'amount'    => 'required|numeric|min:50|max:10000',
        ]);

        $user   = Auth::user();
        $networkKey = strtolower($request->network); // mtn, airtel, etc.
        $mobile  = $request->mobileno;
        $amount  = $request->amount;
        $requestId = RequestIdHelper::generateRequestId();

        // Map network names to Smart Idea API codes
        $networkCodes = [
            'airtel' => '100',
            'mtn'    => '101',
            'glo'    => '102',
            'etisalat' => '103', // 9mobile
        ];
        $networkCode = $networkCodes[$networkKey];

        // 1. Find the Airtime Service
        $service = Service::where('name', 'Airtime')->first();
        if (!$service) {
             $service = Service::firstOrCreate(['name' => 'Airtime'], ['status' => 'active']);
        }

        // 2. Find the specific Network Field (e.g., MTN)
        $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
            ->where(function($q) use ($networkKey) {
                $q->where('field_name', 'LIKE', "%{$networkKey}%")
                  ->orWhere('field_code', 'LIKE', "%{$networkKey}%");
            })->first();

        // 3. Calculate Discount
        $discountPercentage = 0;
        if ($serviceField) {
            $userType = $user->user_type ?? 'personal'; 
            
            $servicePrice = \App\Models\ServicePrice::where('service_field_id', $serviceField->id)
                ->where('user_type', $userType)
                ->first();

            if ($servicePrice) {
                $discountPercentage = $servicePrice->price;
            } else {
                $discountPercentage = $serviceField->base_price ?? 0; 
            }
        }

        $discountAmount = ($amount * $discountPercentage) / 100;
        $payableAmount = $amount - $discountAmount;

        // 4. Check Wallet Balance
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $payableAmount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance! You need ₦' . number_format($payableAmount, 2));
        }

        // 5. Call Smart Idea Airtime API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->getApiBaseUrl() . '/airtime/purchase', [
                'network'    => $networkCode,
                'mobileno'   => $mobile,
                'amount'     => $amount,
                'request_id' => $requestId,
            ]);

        } catch (\Exception $e) {
            Log::error('Smart Idea API Connection Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not connect to airtime provider. Please try again later.');
        }

        // 6. Process Response
        $data = $response->json();
        Log::info('Smart Idea API Response', ['response' => $data]);

        $isSuccessful = false;
        
        if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
            $isSuccessful = true;
        }

        if ($isSuccessful) {
            // Deduct Wallet (Payable Amount)
            $oldBalance = $wallet->balance;
            $wallet->decrement('balance', $payableAmount);
            $newBalance = $wallet->balance;

            // Extract API response data
            $apiData = $data['data'] ?? [];
            $transactionRef = $apiData['transaction_ref'] ?? $requestId;
            $commissionEarned = $apiData['commission_earned'] ?? 0;

            // Create Transaction Record
            Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'         => $user->id,
                'amount'          => $payableAmount,
                'description'     => "Airtime purchase of ₦{$amount} for {$mobile} ({$networkKey})",
                'type'            => 'debit',
                'status'          => 'completed',
                'metadata'        => json_encode([
                    'phone'             => $mobile,
                    'network'           => $networkKey,
                    'network_code'      => $networkCode,
                    'original_amt'      => $amount,
                    'discount'          => $discountAmount,
                    'commission_earned' => $commissionEarned,
                    'api_response'      => $data,
                ]),
                'performed_by' => $user->first_name . ' ' . $user->last_name,
                'approved_by'  => $user->id,
            ]);

            return redirect()->route('thankyou')->with([
                'success'           => 'Airtime purchase successful!',
                'transaction_ref'   => $transactionRef,
                'request_id'        => $requestId,
                'mobile'            => $mobile,
                'network'           => ucfirst($networkKey),
                'amount'            => $amount,
                'paid'              => $payableAmount,
                'commission_earned' => $commissionEarned,
                'type'              => 'airtime'
            ]);
        }

        Log::error('Smart Idea API Response Error', ['response' => $data]);
        $errorMessage = $data['message'] ?? 'Airtime purchase failed. Please try again.';
        return redirect()->back()->with('error', $errorMessage);
    }
}
