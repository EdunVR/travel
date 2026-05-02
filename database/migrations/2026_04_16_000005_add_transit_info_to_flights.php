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
        Schema::table('flights', function (Blueprint $table) {
            // Add transit_info column to store transit details as JSON
            // Format: [{"airport": "Dubai (DXB)", "arrival_time": "10:30", "departure_time": "14:00", "duration_minutes": 210}]
            $table->json('transit_info')->nullable()->after('arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn('transit_info');
        });
    }
};
