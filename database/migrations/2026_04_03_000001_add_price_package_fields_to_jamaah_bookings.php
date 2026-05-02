<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('jamaah_bookings', 'price_package_name')) {
                $table->string('price_package_name')->nullable()->after('room_type');
            }
            if (!Schema::hasColumn('jamaah_bookings', 'price_variant')) {
                $table->string('price_variant')->nullable()->after('price_package_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropColumn(['price_package_name', 'price_variant']);
        });
    }
};
