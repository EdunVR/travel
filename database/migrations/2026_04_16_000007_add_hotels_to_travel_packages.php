<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            // Add hotels JSON column to store multiple hotels
            // Format: [{"id": 1, "name": "Hotel A", "city": "Makkah", "check_in": "2024-01-01", "check_out": "2024-01-05", "nights": 4, "room_type_id": 1}, ...]
            $table->json('hotels')->nullable()->after('saudi_transports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropColumn('hotels');
        });
    }
};
