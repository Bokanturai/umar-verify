<?php

namespace App\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Http\Controllers\Controller;

class BvnServicesController extends Controller
{
    /**
     * Display the service form and submission history (CRM or Send VNIN).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $routeName = $request->route()->getName();
        $isSendVnin = ($routeName === 'send-vnin');
        $serviceKey = $isSendVnin ? 'VNIN TO NIBSS' : 'CRM';

        // Search field depends on service type
        $searchField = $isSendVnin ? 'request_id' : 'batch_id';

        // Query only this user's submissions
        $submissions = AgentService::with('transaction')
            ->where('user_id', $user->id)
            ->where('service_name', $serviceKey)
            ->when($request->filled('search'), fn($q) =>
                $q->where($searchField, 'like', "%{$request->search}%"))
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->status))
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    WHEN status = 'successful' THEN 3
                    WHEN status = 'query' THEN 4
                    ELSE 99
                END
            ")->orderByDesc('submission_date')
            ->paginate(10)
            ->withQueryString();

        // Load active service and its fields
        $service = Service::where('name', $serviceKey)
            ->where('is_active', true)
            ->with(['fields' => fn($q) => $q->where('is_active', true), 'prices'])
            ->first();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $fields = $service?->fields ?? collect();
        $prices = $service?->prices ?? collect();
        $view = $isSendVnin ? 'bvn.send-vnin' : 'bvn.crm';

        return view($view, [
            'fieldname'     => $fields,
            'services'      => Service::where('is_active', true)->get(),
            'serviceName'   => $serviceKey,
            'submissions'   => $submissions,
            'servicePrices' => $prices,
            'wallet'        => $wallet,
        ]);
    }

    /**
     * Store submission for CRM or Send VNIN.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $routeName = $request->route()->getName();
        $isSendVnin = ($routeName === 'send-vnin.store');
        $serviceKey = $isSendVnin ? 'VNIN TO NIBSS' : 'CRM';

        // Validation rules per service
        $rules = [
            'field_code' => 'required|exists:service_fields,id',
        ];

        if ($isSendVnin) {
            $rules += [
                'request_id' => 'required|string|size:7|regex:/^[0-9]{7}$/',
                'bvn'        => 'required|string|size:11|regex:/^[0-9]{11}$/',
                'nin'        => 'required|string|size:11|regex:/^[0-9]{11}$/',
                'field'      => 'required|string',
            ];
        } else {
            $rules += [
                'ticket_id' => 'required|string|size:8|regex:/^[0-9]{8}$/',
                'batch_id'  => 'required|string|size:7|regex:/^[0-9]{7}$/',
            ];
        }

        $validated = $request->validate($rules);

        $serviceField = ServiceField::with(['service', 'prices'])->findOrFail($validated['field_code']);
        $serviceName = $serviceField->service->name;
        $fieldName = $serviceField->field_name;

        //  Determine correct price for user
        $servicePrice = $serviceField->prices
            ->where('user_type', $user->role)
            ->first()?->price ?? $serviceField->base_price;

        $totalAmount = $servicePrice; // You can expand this if future surcharges apply

        if ($servicePrice === null) {
            return back()->with([
                'status'  => 'error',
                'message' => 'Service price not configured for your account type.'
            ])->withInput();
        }

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        if ($wallet->status !== 'active') {
            return back()->with(['status' => 'error', 'message' => 'Your wallet is not active.'])->withInput();
        }

        if ($wallet->balance < $totalAmount) {
            return back()->with([
                'status'  => 'error',
                'message' => 'Insufficient balance. You need NGN ' .
                    number_format($totalAmount - $wallet->balance, 2) . ' more.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $reference = 'BVN' . date('is') . strtoupper(substr(uniqid(mt_rand(), true), -5));
            $performedBy = trim($user->first_name . ' ' . $user->last_name);

            //  Capture complete transaction metadata
            $fullMetadata = [
                'service_key'   => $serviceKey,
                'field_details' => [
                    'id'         => $serviceField->id,
                    'name'       => $fieldName,
                    'code'       => $serviceField->field_code,
                ],
                'user_details'  => [
                    'id'    => $user->id,
                    'name'  => $performedBy,
                    'role'  => $user->role,
                    'email' => $user->email,
                ],
                'request_data'  => $validated,
                'pricing'       => [
                    'unit_price'  => $servicePrice,
                    'total_amount' => $totalAmount,
                ],
                'wallet_before' => $wallet->balance,
                'transaction_time' => now()->toDateTimeString(),
                'channel' => $isSendVnin ? 'Send VNIN Portal' : 'CRM Dashboard',
            ];

            //  Create transaction
            $transaction = Transaction::create([
                'transaction_ref' => $reference,
                'user_id'         => $user->id,
                'amount'          => $totalAmount,
                'performed_by'    => $performedBy,
                'description'     => "{$serviceName} Request for {$fieldName}",
                'type'            => 'debit',
                'status'          => 'completed',
                'metadata'        => $fullMetadata, // full trace
            ]);

            //  Create main submission record with amount_paid
            AgentService::create([
                'reference'       => $reference,
                'user_id'         => $user->id,
                'service_id'      => $serviceField->service_id,
                'service_field_id'  => $serviceField->id,
                'field_code'      => $serviceField->field_code,
                'service_name'    => $serviceName,
                'field_name'      => $fieldName,
                'ticket_id'       => $validated['ticket_id'] ?? null,
                'batch_id'        => $validated['batch_id'] ?? null,
                'request_id'      => $validated['request_id'] ?? null,
                'bvn'             => $validated['bvn'] ?? null,
                'nin'             => $validated['nin'] ?? null,
                'field'           => $validated['field'] ?? null,
                'amount'          => $totalAmount, 
                'performed_by'    => $performedBy,
                'transaction_id'  => $transaction->id,
                'submission_date' => now(),
                'status'          => 'pending',
                'service_type'    => $serviceName,
            ]);

            //  Deduct wallet after record creation
            $wallet->decrement('balance', $totalAmount);

            DB::commit();

            $redirectRoute = $isSendVnin ? 'send-vnin' : 'bvn-crm';

            return redirect()->route($redirectRoute)->with([
                'status'  => 'success',
                'message' => "{$serviceKey} request submitted successfully. Ref: {$reference}. Charged: ₦" .
                    number_format($totalAmount, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return back()->with([
                'status'  => 'error',
                'message' => 'Submission failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }
}
