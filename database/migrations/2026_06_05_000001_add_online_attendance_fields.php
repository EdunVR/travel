<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk mendukung absensi online via Flutter app.
     * - source: 'fingerprint' | 'rfid' | 'online' | 'manual'
     * - latitude / longitude: koordinat GPS saat absen online
     * - location_address: alamat hasil reverse geocoding (opsional)
     * - device_info: info device Flutter (model, OS)
     * - online_token: token unik per-sesi absen (anti-replay)
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'source')) {
                $table->string('source', 20)->default('fingerprint')->after('notes')
                      ->comment('fingerprint | rfid | online | manual');
            }
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('source');
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('attendances', 'location_address')) {
                $table->string('location_address')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('attendances', 'device_info')) {
                $table->string('device_info')->nullable()->after('location_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumnIfExists('source');
            $table->dropColumnIfExists('latitude');
            $table->dropColumnIfExists('longitude');
            $table->dropColumnIfExists('location_address');
            $table->dropColumnIfExists('device_info');
        });
    }
};
