<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom realisasi_pct ke tabel tasks.
     * Kolom ini menyimpan persentase realisasi task (0–100) yang diisi karyawan.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'realisasi_pct')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->decimal('realisasi_pct', 5, 2)->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumnIfExists('realisasi_pct');
        });
    }
};
