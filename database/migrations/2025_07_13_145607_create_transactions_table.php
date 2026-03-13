<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
         Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_ref', 100);
            $table->string('payer_name')->nullable();
            $table->string('referenceId')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('performed_by', 150)->nullable();
            $table->string('approved_by', 150)->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2)->default(0.00);
            $table->string('description')->nullable();
            $table->enum('type', ['credit', 'debit', 'refund', 'chargeback']);
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed', 'rejected', 'query'])->default('pending');
            $table->string('service_type')->nullable();
            $table->json('metadata')->nullable(); 
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('transaction_ref');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};