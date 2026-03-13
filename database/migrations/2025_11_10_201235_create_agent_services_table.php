<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_services', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('service_field_id')->nullable()->constrained('service_fields')->nullOnDelete();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->string('service_type', 50)->default('not_selected');
            $table->string('field_code', 20)->nullable();
            $table->string('ticket_id', 8)->nullable();
            $table->string('batch_id', 7)->nullable();
            $table->string('request_id', 7)->nullable();
            $table->string('our_id', 7)->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('request_email')->nullable();
            $table->string('email_auth')->nullable();
            $table->string('other_bank')->nullable();
            $table->string('bvn', 50)->nullable();
            $table->string('nin', 20)->nullable();
            $table->string('number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
            $table->string('email')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            $table->string('field_name')->nullable();
            $table->string('service_name')->nullable();
            $table->string('service_field_name')->nullable();
            $table->string('bank')->nullable();
            $table->text('description')->nullable();
            $table->json('modification_data')->nullable();
            $table->string('affidavit')->nullable(); 
            $table->string('affidavit_file_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('passport_url')->nullable();
            $table->string('nin_slip_url')->nullable();
            $table->string('cac_file')->nullable();
            $table->string('memart_file')->nullable();
            $table->string('status_report_file')->nullable();
            $table->string('tin_file')->nullable();
            $table->text('field')->nullable(); 
            $table->string('performed_by', 150)->nullable();
            $table->string('approved_by', 150)->nullable();
            $table->string('completed_by', 150)->nullable();
            $table->dateTime('submission_date');

            $table->enum('status', [
                'pending',
                'processing',
                'in-progress',
                'resolved',
                'successful',
                'rejected',
                'failed',
                'query',
                'remark'
            ])->default('pending');

            $table->text('comment')->nullable();

            // Company-related fields
            $table->string('company_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('city')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('country')->nullable();
            $table->string('cac_certificate')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_services');
    }
};