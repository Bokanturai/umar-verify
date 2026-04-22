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


        DB::beginTransaction();

        try {
            // Generate Reference
            $transactionRef = 'M1' . strtoupper(Str::random(10));
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
                    ]
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
                'comment'            => null,
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
        return back()->with('info', 'Manual status check required. Please contact support or check back later.');
    }
}
