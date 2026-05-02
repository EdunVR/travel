<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_bookings', 'room_type')) {
                $table->string('room_type')->nullable()->after('room_count'); // quad, triple, double
            }
            if (!Schema::hasColumn('hotel_bookings', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('room_type');
            }
            if (!Schema::hasColumn('hotel_bookings', 'seller_phone')) {
                $table->string('seller_phone')->nullable()->after('seller_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn(['room_type', 'seller_name', 'seller_phone']);
        });
    }
};
