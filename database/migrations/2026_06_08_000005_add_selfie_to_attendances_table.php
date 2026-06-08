<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom selfie foto masuk dan keluar ke tabel attendances.
     * Foto disimpan sebagai path relatif ke storage/app/public/attendance_selfies/
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'selfie_in')) {
                $table->string('selfie_in')->nullable()->after('device_info')
                      ->comment('Path foto selfie saat clock in');
            }
            if (!Schema::hasColumn('attendances', 'selfie_out')) {
                $table->string('selfie_out')->nullable()->after('selfie_in')
                      ->comment('Path foto selfie saat clock out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumnIfExists('selfie_in');
            $table->dropColumnIfExists('selfie_out');
        });
    }
};
