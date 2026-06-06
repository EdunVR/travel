<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom GPS untuk clock-out (lokasi berbeda dari clock-in)
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'clock_out_latitude')) {
                $table->decimal('clock_out_latitude', 10, 7)->nullable()->after('location_address');
            }
            if (!Schema::hasColumn('attendances', 'clock_out_longitude')) {
                $table->decimal('clock_out_longitude', 10, 7)->nullable()->after('clock_out_latitude');
            }
            if (!Schema::hasColumn('attendances', 'clock_out_address')) {
                $table->string('clock_out_address')->nullable()->after('clock_out_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumnIfExists('clock_out_latitude');
            $table->dropColumnIfExists('clock_out_longitude');
            $table->dropColumnIfExists('clock_out_address');
        });
    }
};
