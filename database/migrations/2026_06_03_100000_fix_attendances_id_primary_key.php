<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel attendances ada
        if (!Schema::hasTable('attendances')) {
            return;
        }

        // Cek apakah PRIMARY KEY sudah ada
        $indexes = DB::select("SHOW INDEXES FROM attendances WHERE Key_name = 'PRIMARY'");
        if (count($indexes) > 0) {
            // Sudah ada PRIMARY KEY, pastikan AUTO_INCREMENT juga ada
            $col = DB::select("SHOW COLUMNS FROM attendances WHERE Field = 'id'");
            if (!empty($col) && strpos($col[0]->Extra, 'auto_increment') === false) {
                DB::statement("ALTER TABLE attendances MODIFY COLUMN id bigint(20) unsigned NOT NULL AUTO_INCREMENT");
            }
            return;
        }

        // Tidak ada PRIMARY KEY — tambahkan sekaligus AUTO_INCREMENT
        DB::statement("ALTER TABLE attendances MODIFY COLUMN id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)");
    }

    public function down(): void
    {
        // Tidak di-rollback karena berbahaya (menghapus primary key)
    }
};
