<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qris_transactions')) {
            return;
        }

        Schema::create('qris_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jamaah_booking')->nullable();
            $table->unsignedBigInteger('id_jamaah_payment')->nullable();
            $table->string('trx_number', 100)->unique(); // Client transaction number
            $table->string('qris_invoice_id', 100)->nullable(); // QRIS Invoice ID from API
            $table->bigInteger('amount'); // Amount in IDR
            $table->string('qris_content', 2000)->nullable(); // QRIS string content for QR generation
            $table->string('qris_nmid', 50)->nullable();
            $table->string('qris_request_date', 50)->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->string('payment_customer_name')->nullable(); // Customer name from payment app
            $table->string('payment_method_by')->nullable(); // Payment method (GoPay, OVO, etc.)
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('api_response_create')->nullable(); // Full API response on create
            $table->json('api_response_check')->nullable(); // Full API response on check
            $table->timestamps();

            $table->index('id_jamaah_booking');
            $table->index('qris_invoice_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qris_transactions');
    }
};
