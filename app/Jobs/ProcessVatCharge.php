<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessVatCharge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $chargeAmount = 15;
        $chargeDesc = 'VAT charge on transaction';
        $chargeRef = 'VAT-' . strtoupper(Str::random(10));

        \DB::transaction(function () use ($chargeAmount, $chargeDesc, $chargeRef) {
            $wallet = Wallet::where('user_id', $this->userId)->lockForUpdate()->first();
            if ($wallet) {
                $wallet->decrement('balance', $chargeAmount);
                $wallet->decrement('available_balance', $chargeAmount);

                Transaction::create([
                    'user_id' => $this->userId,
                    'transaction_ref' => $chargeRef,
                    'type' => 'debit',
                    'amount' => $chargeAmount,
                    'description' => $chargeDesc,
                    'status' => 'completed',
                    'performed_by' => 'System',
                    'metadata' => ['type' => 'vat_charge'],
                ]);
            }
        });
    }
}
