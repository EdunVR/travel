<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jamaah_payments', function (Blueprint $table) {
            $table->enum('verification_status', ['pending_verification', 'verified', 'rejected'])
                  ->default('pending_verification')
                  ->after('payment_type')
                  ->comment('Status verifikasi pembayaran oleh admin');
            
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->text('verification_notes')->nullable()->after('verified_by');
            
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_payments', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_at', 'verified_by', 'verification_notes']);
        });
    }
};
