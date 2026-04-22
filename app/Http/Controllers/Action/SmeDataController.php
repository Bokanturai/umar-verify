<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\SmeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\ActiveUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SmeDataController extends Controller
{
    use ActiveUsers;

    // API Configuration - matching DataController
    private function getApiBaseUrl()
    {
        return env('AREWA_BASE_URL', 'https://api.arewasmart.com.ng/api/v1');
    }

    private function getApiToken()
    {
        return env('AREWA_API_TOKEN');
    }

    /**
     * Show SME Data Purchase Page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $networks = SmeData::select('network')->distinct()->get();

        // Price lists for the advert section
        $priceList1 = DB::table('data_variations')->where('service_id', 'mtn-data')->paginate(10, ['*'], 'table1_page');
        $priceList2 = DB::table('data_variations')->where('service_id', 'airtel-data')->paginate(10, ['*'], 'table2_page');
        $priceList3 = DB::table('data_variations')->where('service_id', 'glo-data')->paginate(10, ['*'], 'table3_page');
        $priceList4 = DB::table('data_variations')->where('service_id', 'etisalat-data')->paginate(10, ['*'], 'table4_page');
        $priceList5 = DB::table('data_variations')->where('service_id', 'smile-direct')->paginate(10, ['*'], 'table5_page');
        $priceList6 = DB::table('data_variations')->where('service_id', 'spectranet')->paginate(10, ['*'], 'table6_page');

        return view('utilities.buy-sme-data', compact(
            'user', 
            'wallet', 
            'networks',
            'priceList1',
            'priceList2',
            'priceList3',
            'priceList4',
            'priceList5',
            'priceList6'
        ));
    }

    /**
     * Fetch Data Types for a Network
     */
    public function fetchDataType(Request $request)
    {
        $network = $request->id;
        $types = SmeData::where('network', $network)
            ->select('plan_type')
            ->distinct()
            ->get();
        return response()->json($types);
    }

    /**
     * Fetch Data Plans for a Network and Type
     */
    public function fetchDataPlan(Request $request)
    {
        $network = $request->id;
        $type = $request->type;
        $plans = SmeData::where('network', $network)
            ->where('plan_type', $type)
            ->where('status', 'enabled')
            ->get();
        return response()->json($plans);
    }

    /**
     * Fetch Plan Price
     */
    public function fetchSmeBundlePrice(Request $request)
    {
        $planId = $request->id;
        $plan = SmeData::where('data_id', $planId)->first();
        
        if (!$plan) {
            return response()->json("0.00");
        }

        $user = Auth::user();
        $finalPrice = $plan->calculatePriceForRole($user->user_type ?? 'user');

        return response()->json(number_format((float)$finalPrice, 2));
    }

    /**
     * Buy SME Data Bundle
     */
    public function buySMEdata(Request $request)
    {
        $request->validate([
            'network'  => 'required|string',
            'type'     => 'required|string',
            'plan'     => 'required|string',
            'mobileno' => 'required|numeric|digits:11'
        ]);

        $user = Auth::user();
        $mobile = $request->mobileno;
        $planId = $request->plan;
        
        $plan = SmeData::where('data_id', $planId)->first();
        if (!$plan) {
            return back()->with('error', 'Invalid data plan selected.');
        }

        $payableAmount = $plan->calculatePriceForRole($user->user_type ?? 'user');
        $description = "{$plan->size} {$plan->plan_type} for {$mobile} ({$plan->network})";

        // Check Wallet Balance
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $payableAmount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance! You need ₦' . number_format($payableAmount, 2));
        }

        $requestId = RequestIdHelper::generateRequestId();

        // API Call to Smart Idea
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                 ])->post($this->getApiBaseUrl() . '/sme-data/purchase', [
                'network'    => $plan->network, // e.g., MTN
                'mobileno'   => $mobile,
                'plan_id'    => $planId,
                'request_id' => $requestId,
            ]);

            $data = $response->json();
            Log::info('SME Data API Response', ['response' => $data]);

            if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
                // Success path
                $wallet->decrement('balance', $payableAmount);
                $apiData = $data['data'] ?? [];
                $transactionRef = $apiData['transaction_ref'] ?? $requestId;
                
                // Extract plan info from response if available, otherwise use our description
                $apiDescription = $apiData['plan'] ?? $description;

                Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id'         => $user->id,
                    'amount'          => $payableAmount,
                    'description'     => "SME Data purchase: " . $apiDescription,
                    'type'            => 'debit',
                    'status'          => 'completed',
                    'metadata'        => json_encode([
                        'phone'        => $mobile,
                        'network'      => $plan->network,
                        'plan_type'    => $plan->plan_type,
                        'data_id'      => $planId,
                        'api_response' => $data,
                        'api_data'     => $apiData
                    ]),
                    'performed_by' => $user->first_name . ' ' . $user->last_name,
                    'approved_by'  => $user->id,
                ]);

                return redirect()->route('thankyou')->with([
                    'success'         => 'Data purchase successful!',
                    'transaction_ref' => $transactionRef,
                    'request_id'      => $requestId,
                    'mobile'          => $mobile,
                    'network'         => $plan->network,
                    'amount'          => $payableAmount,
                    'paid'            => $payableAmount,
                    'type'            => 'data'
                ]);
            } else {
                $errorMessage = $data['message'] ?? 'Data purchase failed. Please try again.';
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('SME Data API Connection Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not connect to data provider. Please try again later.');
        }
    }
}
