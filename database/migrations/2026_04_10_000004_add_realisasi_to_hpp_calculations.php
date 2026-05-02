<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            // Nilai realisasi aktual per komponen HPP dasar (JSON)
            $table->json('component_realisasi')->nullable()->after('component_hutang_amount')
                ->comment('JSON: {flight_cost: 5000000, transportation_cost: 2000000, ...}');
        });
    }

    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn('component_realisasi');
        });
    }
};
