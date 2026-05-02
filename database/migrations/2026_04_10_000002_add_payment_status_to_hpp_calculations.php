<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            // Status pembayaran per komponen: lunas / hutang
            $table->json('component_payment_status')->nullable()->after('contingency')
                ->comment('JSON: {flight_cost: "lunas", transportation_cost: "hutang", ...}');
            // Nilai hutang per komponen (total, bukan per orang)
            $table->json('component_hutang_amount')->nullable()->after('component_payment_status')
                ->comment('JSON: {transportation_cost: 5000000, ...}');
        });
    }

    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn(['component_payment_status', 'component_hutang_amount']);
        });
    }
};
