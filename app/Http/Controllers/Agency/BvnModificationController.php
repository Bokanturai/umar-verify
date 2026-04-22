<?php

namespace App\Http\Controllers\Agency;

use App\Models\ServiceField;
use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $banks = AgentService::where('user_id', $user->id)
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
            'banks',
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
            'affidavit_file' => 'nullable|file|mimes:pdf|max:2048',
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


            $transactionRef = 'M1' . date('is') . strtoupper(Str::random(5));
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
                    ]
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
                'description' => $validated['description'] ?? null,
                'modification_data' => $request->modification_data ?? null,
                'amount' => $totalAmount,
                'affidavit_file' => $fileName,
                'affidavit' => $validated['affidavit'],
                'affidavit_file_url' => $fileUrl,
                'transaction_id' => $transaction->id,
                'submission_date' => now(),
                'status' => 'pending',
                'service_type' => 'bvn_modification',
                'comment' => null,
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
        return back()->with('info', 'Manual status check required. Please contact support or check back later.');
    }
}