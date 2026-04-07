<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AgentService;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        $submissions = AgentService::where('user_id', $user->id)
            ->where('service_type', 'license_request')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('license.index', compact('wallet', 'submissions'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $fee = 1000;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'category' => 'required|string',
            'license_type' => 'required|string',
            'description' => 'required|string|min:10',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $fee) {
            return back()->with([
                'status' => 'error',
                'message' => 'Insufficient funds. Submission fee is ₦' . number_format($fee, 2)
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            $transactionRef = 'LIC' . strtoupper(Str::random(10));

            // Find or create the service and field to get IDs
            $service = \App\Models\Service::where('name', 'Government License and Permit Processing')->first();
            $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
                ->where('field_name', 'License Registration')
                ->first();

            // Create Transaction
            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $fee,
                'type' => 'debit',
                'status' => 'completed',
                'description' => 'License Request Fee: ' . $request->license_type,
                'service_type' => 'License Registration',
                'performed_by' => $user->first_name . ' ' . $user->surname,
            ]);

            // Create AgentService Record
            AgentService::create([
                'reference' => $transactionRef,
                'user_id' => $user->id,
                'service_id' => $service->id,
                'service_field_id' => $serviceField->id ?? null,
                'transaction_id' => $transaction->id,
                'service_name' => 'Government License and Permit Processing',
                'bank' => $request->category,
                'service_type' => 'license_request',
                'field_name' => $request->license_type,
                'description' => $request->description,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'amount' => $fee,
                'status' => 'pending',
                'submission_date' => now(),
                'performed_by' => $user->first_name . ' ' . $user->surname,
            ]);

            // Deduct from wallet
            $wallet->decrement('balance', $fee);

            DB::commit();

            return redirect()->route('license.index')->with([
                'status' => 'success',
                'message' => 'License request submitted successfully. Our team will contact you soon.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->withInput();
        }
    }
    public function batchCheck()
    {
        try {
            $pendingCount = AgentService::where('user_id', Auth::id())
                ->where('service_type', 'license_request')
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            return response()->json([
                'success' => true,
                'message' => "Batch check complete. {$pendingCount} requests are currently being processed by our team.",
                'checked' => $pendingCount
            ]);
        } catch (\Exception $e) {
            Log::error('Batch Check Error (License): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Batch check failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
