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
            $table->unsignedBigInteger('id_flight')->nullable()->after('hotel_name');
            $table->unsignedBigInteger('id_hotel')->nullable()->after('id_flight');
            $table->unsignedBigInteger('id_hotel_room_type')->nullable()->after('id_hotel');
            
            // Foreign keys
            $table->foreign('id_flight')->references('id')->on('flights')->onDelete('set null');
            $table->foreign('id_hotel')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('id_hotel_room_type')->references('id')->on('hotel_room_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropForeign(['id_flight']);
            $table->dropForeign(['id_hotel']);
            $table->dropForeign(['id_hotel_room_type']);
            $table->dropColumn(['id_flight', 'id_hotel', 'id_hotel_room_type']);
        });
    }
};
