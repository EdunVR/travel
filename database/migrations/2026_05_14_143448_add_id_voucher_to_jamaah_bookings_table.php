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
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('id_voucher')->nullable()->after('id_invoice');
            $table->foreign('id_voucher')->references('id')->on('affiliate_vouchers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropForeign(['id_voucher']);
            $table->dropColumn('id_voucher');
        });
    }
};
