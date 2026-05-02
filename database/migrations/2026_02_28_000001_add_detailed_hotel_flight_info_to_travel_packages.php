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
            // Hotel Mekkah
            $table->unsignedBigInteger('id_hotel_makkah')->nullable()->after('id_hotel_room_type');
            $table->unsignedBigInteger('id_hotel_room_type_makkah')->nullable()->after('id_hotel_makkah');
            $table->date('makkah_check_in')->nullable()->after('id_hotel_room_type_makkah');
            $table->date('makkah_check_out')->nullable()->after('makkah_check_in');
            
            // Hotel Madinah
            $table->unsignedBigInteger('id_hotel_madinah')->nullable()->after('makkah_check_out');
            $table->unsignedBigInteger('id_hotel_room_type_madinah')->nullable()->after('id_hotel_madinah');
            $table->date('madinah_check_in')->nullable()->after('id_hotel_room_type_madinah');
            $table->date('madinah_check_out')->nullable()->after('madinah_check_in');
            
            // Flight Information
            $table->unsignedBigInteger('id_flight_departure')->nullable()->after('madinah_check_out');
            $table->dateTime('departure_datetime')->nullable()->after('id_flight_departure');
            $table->unsignedBigInteger('id_flight_return')->nullable()->after('departure_datetime');
            $table->dateTime('return_datetime')->nullable()->after('id_flight_return');
            
            // Package Photos (multiple photos support)
            $table->json('package_photos')->nullable()->after('image_path');
            
            // Foreign keys
            $table->foreign('id_hotel_makkah')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('id_hotel_room_type_makkah')->references('id')->on('hotel_room_types')->onDelete('set null');
            $table->foreign('id_hotel_madinah')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('id_hotel_room_type_madinah')->references('id')->on('hotel_room_types')->onDelete('set null');
            $table->foreign('id_flight_departure')->references('id')->on('flights')->onDelete('set null');
            $table->foreign('id_flight_return')->references('id')->on('flights')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropForeign(['id_hotel_makkah']);
            $table->dropForeign(['id_hotel_room_type_makkah']);
            $table->dropForeign(['id_hotel_madinah']);
            $table->dropForeign(['id_hotel_room_type_madinah']);
            $table->dropForeign(['id_flight_departure']);
            $table->dropForeign(['id_flight_return']);
            
            $table->dropColumn([
                'id_hotel_makkah',
                'id_hotel_room_type_makkah',
                'makkah_check_in',
                'makkah_check_out',
                'id_hotel_madinah',
                'id_hotel_room_type_madinah',
                'madinah_check_in',
                'madinah_check_out',
                'id_flight_departure',
                'departure_datetime',
                'id_flight_return',
                'return_datetime',
                'package_photos'
            ]);
        });
    }
};
