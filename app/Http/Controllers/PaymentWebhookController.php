<?php

namespace App\Http\Controllers;

use App\Helpers\signatureHelper;
use App\Jobs\ProcessVatCharge;
use App\Mail\PaymentNotifyMail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PaymentWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log raw body first (helps debugging if payload parsing fails)
        Log::info('Fintava RAW Webhook Body: '.$request->getContent());

        $payload = $request->all();
        Log::info('Fintava webhook hit:', ['payload' => $payload]);

        // Fintava usually provides a token or signature in headers
        // For now, we will log headers and proceed if payload is valid
        if (!$this->verifyFintavaWebhook($request)) {
            Log::warning('Invalid Fintava webhook signature or token received', [
                'headers' => $request->headers->all(),
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            $this->processFintavaTransaction($payload);
            return response()->json(['status' => 'success', 'message' => 'Processed'], 200);
        } catch (\Throwable $e) {
            Log::error('Error processing Fintava webhook: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    private function verifyFintavaWebhook(Request $request)
    {
        // Many systems use a secret token in the header or environment variable
        // Check if FINTAVA_TOKEN matches if provided in headers (placeholder)
        // For now, we accept if it has the required data fields
        $payload = $request->all();
        return isset($payload['accountNumber']) || isset($payload['data']['accountNumber']);
    }

    private function processFintavaTransaction($payload)
    {
        // Fintava payload structure often wraps data in a 'data' object or 'payload'
        $data = $payload['data'] ?? $payload;
        
        Log::info('[FINTAVA Webhook Processing]:', ['data' => $data]);

        $virtualAccountNo = $data['accountNumber'] ?? null;
        $orderNo          = $data['reference'] ?? $data['id'] ?? null;
        $amountPaid       = $data['amount'] ?? 0;
        $payerBankName    = $data['senderBank'] ?? $data['bankName'] ?? 'Fintava';
        $payerAccountName = $data['senderName'] ?? $data['accountName'] ?? 'Fintava User';
        $orderStatus      = $data['status'] ?? 'success';

        $service_description = 'Your wallet has been credited with ₦' . number_format($amountPaid, 2);

        if (!$virtualAccountNo || !$orderNo) {
            Log::warning('Fintava Webhook missing accountNumber or reference', ['data' => $data]);
            return;
        }

        $virtualAccount = VirtualAccount::where('accountNo', $virtualAccountNo)->first();

        if ($virtualAccount) {
            $this->createTransactionForReservedAccount(
                $virtualAccount->user_id,
                $orderNo,
                $amountPaid,
                $payerBankName,
                $payerAccountName,
                $service_description,
                $orderStatus,
                $data
            );
        } else {
            Log::warning('Virtual account not found for Fintava accountNumber: '.$virtualAccountNo, ['data' => $data]);
        }
    }


    private function handlePayout($payload)
    {
        $orderNo = $payload['merchantOrderNo'] ?? $payload['orderNo'] ?? null;
        $status = $payload['orderStatus'] ?? null; // usually 1 for success

        if (!$orderNo) return;

        $transaction = Transaction::where('transaction_ref', $orderNo)->first();
        if ($transaction) {
            $newStatus = ($status == 1) ? 'completed' : (($status == 2) ? 'failed' : 'pending');
            $transaction->update([
                'status' => $newStatus,
                'metadata' => array_merge($transaction->metadata ?? [], ['webhook_update' => $payload])
            ]);
            Log::info("Payout transaction {$orderNo} updated to {$newStatus}");
        }
    }

    private function createTransactionForReservedAccount($userId, $orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $payload)
    {
        $transaction = Transaction::where('transaction_ref', $orderNo)->first();

        if ($transaction) {
            $this->updateTransaction($orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $userId, $payload);
        } else {
            // Only credit wallet if status is successful (Fintava usually 'success' or 'completed' or status code)
            if (in_array(strtolower($orderStatus), ['success', 'completed', '1', 'paid'])) {
                $this->insertTransaction($userId, $orderNo, $amountPaid, $payerAccountName, $payerBankName, $service_description, $payload);
                $this->updateWalletBalance($userId, $amountPaid);

                // Check for 10,000 threshold logic
                if ($amountPaid >= 10000) {
                    $chargeAmount = 50;
                    $chargeDesc = 'transaction lavy charge';
                    $chargeRef = 'CHG-' . strtoupper(Str::random(10));
                    
                    $this->debitWallet($userId, $chargeAmount, $chargeDesc, $chargeRef, $orderNo);

                    // Schedule VAT Charge (15 Naira) after 1 minute
                    ProcessVatCharge::dispatch($userId)->delay(now()->addMinute());
                }

                $this->sendNotificationAndEmail($userId, $amountPaid, $orderNo, $payerBankName, 'Topup');
            } else {
                Log::info("Transaction {$orderNo} skipped due to status: {$orderStatus}");
            }
        }
    }

    private function updateTransaction($orderNo, $amountPaid, $payerBankName, $payerAccountName, $service_description, $orderStatus, $userId, $payload)
    {
        $status = (in_array(strtolower($orderStatus), ['success', 'completed', '1', 'paid'])) ? 'completed' : 'pending';

        $user = User::find($userId);
        $performedBy = $user ? $user->first_name . ' ' . $user->last_name : 'System';

        Transaction::where('transaction_ref', $orderNo)
            ->update([
                'description'         => $service_description,
                'amount'              => $amountPaid,
                'payer_name'          => $payerAccountName,
                'status'              => $status,
                'performed_by'        => $performedBy,
                'metadata'            => $payload,
                'updated_at'          => Carbon::now(),
            ]);

        Log::info('Transaction updated for '.$orderNo);
    }

    private function insertTransaction($userId, $orderNo, $amountPaid, $payerAccountName, $payerBankName, $service_description, $payload)
    {
        $user = User::find($userId);
        $performedBy = $user ? $user->first_name . ' ' . $user->last_name : 'System';

        Transaction::create([
            'user_id'        => $userId,
            'payer_name'     => $payerAccountName,
            'transaction_ref'=> $orderNo,
            'type'           => 'credit',
            'description'    => $service_description,
            'amount'         => $amountPaid,
            'status'         => 'completed',
            'performed_by'   => $performedBy,
            'metadata'       => $payload,
        ]);

        Log::info('New transaction inserted for '.$orderNo);
    }

    private function updateWalletBalance($userId, $amountPaid)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        if ($wallet) {
            $wallet->increment('balance', $amountPaid);
            $wallet->increment('available_balance', $amountPaid);
            
            Log::info('Wallet updated for user '.$userId.' with amount '.$amountPaid);
        } else {
            Log::warning('Wallet not found for user ID: '.$userId);
        }
    }

    private function debitWallet($userId, $amount, $desc, $ref, $relatedRef)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        if ($wallet) {
            $wallet->decrement('balance', $amount);
            $wallet->decrement('available_balance', $amount);

            Transaction::create([
                'user_id' => $userId,
                'transaction_ref' => $ref,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $desc,
                'status' => 'completed',
                'performed_by' => 'System',
                'metadata' => ['related_transaction' => $relatedRef, 'type' => 'levy_charge'],
            ]);
        }
    }

    private function sendNotificationAndEmail($userId, $amountPaid, $orderNo, $bankName, $type)
    {
        try {
            $user = User::query()->find($userId);
            if (!$user || !$user->email) {
                Log::warning('Skip email: No user or email found for ID '.$userId);
                return;
            }

            $mail_data = [
                'type'     => $type,
                'amount'   => number_format($amountPaid, 2),
                'ref'      => $orderNo,
                'bankName' => $bankName,
            ];

            // Use queue() instead of send() to avoid blocking or failing the webhook response
            Mail::to($user->email)->queue(new PaymentNotifyMail($mail_data));
            
            Log::info('Payment notification queued for '.$user->email);
        } catch (\Throwable $e) {
            // Log the error but do not throw, so the transaction remains "completed" in the database
            Log::error('Non-blocking error in sendNotificationAndEmail: '.$e->getMessage());
        }
    }
}
