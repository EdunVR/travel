<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaah_payments', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('notes');
            $table->enum('payment_type', ['full', 'dp'])->default('dp')->after('bukti_transfer');
            // Make recorded_by nullable for public/system payments
            $table->unsignedBigInteger('recorded_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jamaah_payments', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'payment_type']);
            $table->unsignedBigInteger('recorded_by')->nullable(false)->change();
        });
    }
};
