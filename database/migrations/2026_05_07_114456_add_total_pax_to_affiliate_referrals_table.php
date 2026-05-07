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
        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->integer('total_pax')->default(1)->after('commission_rate')->comment('Total pax (jamaah + family members) for commission calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->dropColumn('total_pax');
        });
    }
};
