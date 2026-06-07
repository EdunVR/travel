<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom GPS untuk clock-out (lokasi berbeda dari clock-in).
     * Juga memastikan kolom clock-in GPS (dari migration sebelumnya) ada,
     * sehingga migration ini idempotent di semua kondisi database.
     *
     * Tidak pakai ->after() untuk kolom clock-out karena kolom referensi
     * (location_address) mungkin belum ada di database production.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // ── Pastikan kolom clock-in GPS ada (dari migration sebelumnya) ──
            if (!Schema::hasColumn('attendances', 'source')) {
                $table->string('source', 20)->default('fingerprint')->nullable()
                      ->comment('fingerprint | rfid | online | manual');
            }
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'location_address')) {
                $table->string('location_address')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'device_info')) {
                $table->string('device_info')->nullable();
            }

            // ── Kolom clock-out GPS (tujuan utama migration ini) ──
            // Tidak pakai ->after() karena posisi referensi mungkin tidak ada
            if (!Schema::hasColumn('attendances', 'clock_out_latitude')) {
                $table->decimal('clock_out_latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_longitude')) {
                $table->decimal('clock_out_longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_address')) {
                $table->string('clock_out_address')->nullable();
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
