<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    /**
     * Show P2P Transfer form
     */
    public function index()
    {
        $wallet = Auth::user()->wallet;
        return view('wallet.transfer', compact('wallet'));
    }

    /**
     * Verify recipient by phone number, email, or wallet number
     */
    public function verifyUser(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|string',
        ]);

        $search = $request->wallet_id;

        $user = User::where('phone_no', $search)
                    ->orWhere('email', $search)
                    ->orWhereHas('wallet', function($query) use ($search) {
                        $query->where('wallet_number', $search);
                    })
                    ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot transfer to yourself.']);
        }

        return response()->json([
            'success' => true,
            'user_name' => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
        ]);
    }

    /**
     * Verify Transaction PIN
     */
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required']);
        
        $user = Auth::user();
        
        if (Hash::check($request->pin, $user->pin)) {
            return response()->json(['valid' => true]);
        }
        
        return response()->json(['valid' => false]);
    }

    /**
     * Process P2P Transfer
     */
    public function processTransfer(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|string',
            'amount' => 'required|numeric|min:10',
            'pin' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        $sender = Auth::user();
        $senderWallet = $sender->wallet;

        // Verify PIN (Again for security)
        if (!Hash::check($request->pin, $sender->pin)) {
            return back()->with('error', 'Incorrect Transaction PIN.');
        }

        // Check Balance
        if ($senderWallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Find Recipient
        $search = $request->wallet_id;
        $recipient = User::with('wallet')
                         ->where('phone_no', $search)
                         ->orWhere('email', $search)
                         ->orWhereHas('wallet', function($query) use ($search) {
                             $query->where('wallet_number', $search);
                         })
                         ->first();

        if (!$recipient) {
            return back()->with('error', 'Recipient not found.');
        }

        if ($recipient->id === $sender->id) {
            return back()->with('error', 'You cannot transfer to yourself.');
        }

        $amount = $request->amount;
        $senderRef = 'P2P-S-' . strtoupper(Str::random(10));
        $recipientRef = 'P2P-R-' . strtoupper(Str::random(10));
        $description = $request->description ?? ("P2P Transfer to " . $recipient->first_name . " (" . ($recipient->phone_no ?? $recipient->email) . ")");

        try {
            DB::transaction(function () use ($sender, $recipient, $amount, $senderRef, $recipientRef, $description) {
                // Debit Sender
                $sender->wallet->decrement('balance', $amount);

                // Credit Recipient
                if (!$recipient->wallet) {
                     $recipient->wallet()->create([
                        'balance' => 0,
                        'available_balance' => 0,
                        'wallet_number' => $recipient->phone_no ?? Str::random(10),
                     ]);
                     $recipient->refresh();
                }
                $recipient->wallet->increment('balance', $amount);
                $recipient->wallet->increment('available_balance', $amount);

                // Log Transaction for Sender
                Transaction::create([
                    'transaction_ref' => $senderRef,
                    'payer_name'      => $sender->first_name . ' ' . $sender->last_name,
                    'referenceId'     => $senderRef,
                    'user_id'         => $sender->id,
                    'amount'          => $amount,
                    'fee'             => 0,
                    'net_amount'      => $amount,
                    'description'     => $description,
                    'type'            => 'debit',
                    'status'          => 'completed',
                    'performed_by'    => $sender->first_name . ' ' . $sender->last_name,
                    'metadata'        => json_encode(['recipient_id' => $recipient->id]),
                ]);

                // Log Transaction for Recipient
                Transaction::create([
                    'transaction_ref' => $recipientRef,
                    'payer_name'      => $sender->first_name . ' ' . $sender->last_name,
                    'referenceId'     => $recipientRef,
                    'user_id'         => $recipient->id,
                    'amount'          => $amount,
                    'fee'             => 0,
                    'net_amount'      => $amount,
                    'description'     => "P2P Receipt from " . $sender->first_name . " (" . ($sender->phone_no ?? $sender->email) . ")",
                    'type'            => 'credit',
                    'status'          => 'completed',
                    'performed_by'    => $sender->first_name . ' ' . $sender->last_name,
                    'metadata'        => json_encode(['sender_id' => $sender->id]),
                ]);
            });

            return redirect()->route('wallet')->with('success', 'Transfer of ₦' . number_format($amount, 2) . ' to ' . $recipient->first_name . ' was successful.');

        } catch (\Exception $e) {
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}

