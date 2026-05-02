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
            // Add saudi_transports column to store multiple transports for Makkah and Madinah
            // Format: {"makkah": [{"id": 1, "name": "Bus Pariwisata", "capacity": 50}], "madinah": [...]}
            $table->json('saudi_transports')->nullable()->after('id_saudi_transport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropColumn('saudi_transports');
        });
    }
};
