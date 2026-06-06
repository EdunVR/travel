<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix personal_access_tokens.id — tambah AUTO_INCREMENT jika belum ada.
     * 
     * Error: Field 'id' doesn't have a default value
     * Penyebab: Tabel sudah ada di Hostinger tapi dibuat tanpa AUTO_INCREMENT
     */
    public function up(): void
    {
        // Cek apakah kolom id sudah AUTO_INCREMENT
        $columns = DB::select("
            SELECT EXTRA 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'personal_access_tokens' 
              AND COLUMN_NAME = 'id'
        ");

        if (!empty($columns)) {
            $extra = strtolower($columns[0]->EXTRA ?? '');
            
            if (strpos($extra, 'auto_increment') === false) {
                // Belum AUTO_INCREMENT — fix sekarang
                DB::statement("
                    ALTER TABLE `personal_access_tokens` 
                    MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
                ");
            }
            // Kalau sudah AUTO_INCREMENT, skip
        }
    }

    public function down(): void
    {
        // Tidak di-revert karena ini fix — bukan feature
    }
};
