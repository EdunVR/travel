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
            $table->decimal('custom_payment_amount', 15, 2)->nullable()->after('remaining_amount')
                ->comment('Custom payment amount set by admin for payment link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropColumn('custom_payment_amount');
        });
    }
};
