<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\ActiveUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    use ActiveUsers;

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
     * Ensure Data service and network fields exist in database
     */
    private function ensureDataServiceExists()
    {
        // 1. Create or get Data service
        $service = \App\Models\Service::firstOrCreate(
            ['name' => 'Data'],
            ['is_active' => '1']
        );

        // 2. Define network fields with their codes
        $networks = [
            [
                'field_name' => 'MTN Data',
                'description' => 'mtn-data',
                'field_code' => '104',
                'base_price' => 0, 
            ],
            [
                'field_name' => 'Airtel Data',
                'description' => 'airtel-data',
                'field_code' => '105',
                'base_price' => 0,
            ],
            [
                'field_name' => 'Glo Data',
                'description' => 'glo-data',
                'field_code' => '106',
                'base_price' => 0,
            ],
            [
                'field_name' => '9mobile Data',
                'description' => 'etisalat-data',
                'field_code' => '107',
                'base_price' => 0,
            ],
        ];

        // 3. Create service fields if they don't exist
        foreach ($networks as $network) {
            \App\Models\ServiceField::firstOrCreate(
                [
                    'service_id' => $service->id,
                    'description' => $network['description']
                ],
                [
                    'field_name' => $network['field_name'],
                    'field_code' => $network['field_code'],
                    'base_price' => $network['base_price'],
                    'is_active' => '1',
                ]
            );
        }
    }

    /**
     * Show Data Services & Price Lists
     */
    public function data(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        // Ensure Data service and network fields exist
        $this->ensureDataServiceExists();

        try {
            // Fetch services that end with 'data' or are relevant data services
            $servicename = DB::table('data_variations')
                ->select(['service_id', 'service_name'])
                ->where('status', 'enabled')
                ->where(function($query) {
                    $query->where('service_id', 'LIKE', '%data')
                          ->orWhere('service_id', 'smile-direct')
                          ->orWhere('service_id', 'spectranet');
                })
                ->distinct()
                ->limit(6)
                ->get();

            $priceList1 = DB::table('data_variations')->where('service_id', 'mtn-data')->paginate(10, ['*'], 'table1_page');
            $priceList2 = DB::table('data_variations')->where('service_id', 'airtel-data')->paginate(10, ['*'], 'table2_page');
            $priceList3 = DB::table('data_variations')->where('service_id', 'glo-data')->paginate(10, ['*'], 'table3_page');
            $priceList4 = DB::table('data_variations')->where('service_id', 'etisalat-data')->paginate(10, ['*'], 'table4_page');
            $priceList5 = DB::table('data_variations')->where('service_id', 'smile-direct')->paginate(10, ['*'], 'table5_page');
            $priceList6 = DB::table('data_variations')->where('service_id', 'spectranet')->paginate(10, ['*'], 'table6_page');

            return view('utilities.buy-data', compact(
                'user',
                'wallet',
                'servicename',
                'priceList1',
                'priceList2',
                'priceList3',
                'priceList4',
                'priceList5',
                'priceList6'
            ));
        } catch (\Exception $e) {
            Log::error('Data page error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load data services.');
        }
    }

    /**
     * Verify transaction PIN
     */
    public function verifyPin(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized']);
        }

        // Use Hash::check since PIN is now encrypted
        $isValid = Hash::check($request->pin, $user->pin);
        return response()->json(['valid' => $isValid]);
    }

    /**
     * Sync Variations from API
     */
    public function getVariation(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Accept' => 'application/json',
            ])->get($this->getApiBaseUrl() . '/data/variations');

            if ($response->successful()) {
                $data = $response->json();
                
                // Parse the new API response format
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $variation) {
                        // Extract service info from variation_code (e.g., "mtn-10mb-100")
                        $variationCode = $variation['variation_code'] ?? '';
                        $parts = explode('-', $variationCode);
                        $network = $parts[0] ?? 'unknown';
                        
                        DB::table('data_variations')->updateOrInsert(
                            ['variation_code' => $variationCode],
                            [
                                'service_name'    => ucfirst($network) . ' Data',
                                'service_id'      => $network . '-data',
                                'convinience_fee' => 0,
                                'name'            => $variation['name'] ?? $variationCode,
                                'variation_amount'=> $variation['price'] ?? 0,
                                'fixedPrice'      => 'Yes',
                                'status'          => 'enabled',
                                'created_at'      => Carbon::now(),
                                'updated_at'      => Carbon::now()
                            ]
                        );
                    }
                    
                    return response()->json(['success' => true, 'message' => 'Variations synced successfully']);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Failed to fetch variations'], 400);
        } catch (\Exception $e) {
            Log::error('Get variation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error syncing variations'], 500);
        }
    }

    /**
     * Buy Data Bundle
     */
    public function buydata(Request $request)
    {
        $request->validate([
            'network'  => 'required|string',
            'mobileno' => 'required|numeric|digits:11',
            'bundle'   => 'required|string'
        ]);

        $user = Auth::user();
        $networkKey = $request->network; // e.g., mtn-data
        $mobile = $request->mobileno;
        $requestId = RequestIdHelper::generateRequestId();

        // Fetch Bundle Details from database
        $variation = DB::table('data_variations')->where('variation_code', $request->bundle)->first();
        if (!$variation) {
            return back()->with('error', 'Invalid data bundle selected.');
        }
        
        $amount = $variation->variation_amount; // Face value / API price
        $description = $variation->name ?? 'Data Bundle';

        // 1. Find the Data Service
        $service = \App\Models\Service::where('name', 'Data')->first();
        if (!$service) {
            $service = \App\Models\Service::firstOrCreate(['name' => 'Data'], ['is_active' => '1']);
        }

        // 2. Find the specific Network Field (e.g., mtn-data)
        $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
            ->where(function($q) use ($networkKey) {
                $q->where('field_name', 'LIKE', "%{$networkKey}%")
                  ->orWhere('description', 'LIKE', "%{$networkKey}%");
            })->first();

        // 3. Calculate Discount
        $discountPercentage = 0;
        if ($serviceField) {
            $userType = $user->user_type ?? 'user';
            
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

        // 5. Call Smart Idea Data API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->getApiBaseUrl() . '/data/purchase', [
                'network'    => $networkKey,  // mtn-data, airtel-data, etc.
                'mobileno'   => $mobile,
                'bundle'     => $request->bundle,
                'request_id' => $requestId,
            ]);

        } catch (\Exception $e) {
            Log::error('Smart Idea Data API Connection Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not connect to data provider. Please try again later.');
        }

        // 6. Process Response
        $data = $response->json();
        Log::info('Smart Idea Data API Response', ['response' => $data]);

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

            // Create Transaction Record
            Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id'         => $user->id,
                'amount'          => $payableAmount,
                'description'     => "Data purchase of {$description} for {$mobile}",
                'type'            => 'debit',
                'status'          => 'completed',
                'metadata'        => json_encode([
                    'phone'        => $mobile,
                    'network'      => $networkKey,
                    'bundle'       => $request->bundle,
                    'original_amt' => $amount,
                    'discount'     => $discountAmount,
                    'api_response' => $data,
                ]),
                'performed_by' => $user->first_name . ' ' . $user->last_name,
                'approved_by'  => $user->id,
            ]);

            return redirect()->route('thankyou')->with([
                'success'         => 'Data purchase successful!',
                'transaction_ref' => $transactionRef,
                'request_id'      => $requestId,
                'mobile'          => $mobile,
                'network'         => ucfirst(str_replace('-data', '', $networkKey)),
                'amount'          => $amount,
                'paid'            => $payableAmount,
                'type'            => 'data'
            ]);
        }

        Log::error('Smart Idea Data API Response Error', ['response' => $data]);
        $errorMessage = $data['message'] ?? 'Data purchase failed. Please try again.';
        return redirect()->back()->with('error', $errorMessage);
    }

    /**
     * Fetch Bundles by Service ID
     */
    public function fetchBundles(Request $request)
    {
        try {
            $bundles = DB::table('data_variations')
                ->select(['name', 'variation_code'])
                ->where('service_id', $request->id)
                ->where('status', 'enabled')
                ->get();

            return response()->json($bundles);
        } catch (\Exception $e) {
            Log::error('Fetch bundles error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Fetch Bundle Price
     */
    public function fetchBundlePrice(Request $request)
    {
        try {
            $price = DB::table('data_variations')
                ->where('variation_code', $request->id)
                ->value('variation_amount');

            return response()->json(number_format((float)$price, 2));
        } catch (\Exception $e) {
            Log::error('Fetch bundle price error: ' . $e->getMessage());
            return response()->json("0.00", 500);
        }
    }
}
