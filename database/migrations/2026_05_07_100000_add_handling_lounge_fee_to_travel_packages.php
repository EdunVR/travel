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
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->boolean('include_handling_lounge_fee')->default(true)->after('id_outlet');
            $table->decimal('handling_lounge_fee_amount', 15, 2)->default(500000)->after('include_handling_lounge_fee');
            $table->text('handling_lounge_fee_description')->nullable()->after('handling_lounge_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropColumn([
                'include_handling_lounge_fee',
                'handling_lounge_fee_amount',
                'handling_lounge_fee_description'
            ]);
        });
    }
};
