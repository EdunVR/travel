<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel `roles` dan `permissions` punya kolom `id` BIGINT tanpa AUTO_INCREMENT dan tanpa PRIMARY KEY.
     * Laravel tidak bisa insert record baru karena MySQL menolak (field has no default value).
     *
     * Fix: tambahkan AUTO_INCREMENT dan PRIMARY KEY pada kolom id di kedua tabel.
     */
    public function up(): void
    {
        $this->fixTable('roles');
        $this->fixTable('permissions');
    }

    private function fixTable(string $table): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        // Cek apakah id sudah auto_increment
        $cols = DB::select("DESCRIBE `{$table}`");
        foreach ($cols as $col) {
            if ($col->Field === 'id') {
                if (str_contains(strtolower($col->Extra), 'auto_increment')) {
                    // Sudah benar, skip
                    return;
                }
                break;
            }
        }

        // Hitung nilai AUTO_INCREMENT yang aman (max id + 1)
        $maxId = DB::table($table)->max('id') ?? 0;
        $nextId = (int)$maxId + 1;

        // 1. Drop PK jika ada (agar bisa modify)
        try {
            DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
        } catch (\Exception $e) {
            // PK mungkin tidak ada — lanjutkan
        }

        // 2. Tambah AUTO_INCREMENT + PRIMARY KEY pada kolom id
        DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");

        // 3. Set AUTO_INCREMENT ke nilai yang aman
        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
    }

    public function down(): void
    {
        // Tidak di-revert — mengembalikan kondisi rusak tidak bermanfaat
    }
};
