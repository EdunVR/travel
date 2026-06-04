<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel role_permissions adalah pivot table murni.
     * Kolom `id` tidak boleh ada (tidak ada AUTO_INCREMENT),
     * primary key seharusnya composite (role_id, permission_id).
     *
     * Fix: drop kolom id, set composite PK.
     */
    public function up(): void
    {
        // Jika tabel belum ada sama sekali, buat dari scratch
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();
                $table->primary(['role_id', 'permission_id']);
            });
            return;
        }

        // Jika kolom id ada tapi bukan AUTO_INCREMENT, hapus dan perbaiki PK
        $columns = collect(DB::select('DESCRIBE role_permissions'))->keyBy('Field');

        if ($columns->has('id')) {
            // Drop existing primary key jika ada, lalu drop kolom id
            try {
                DB::statement('ALTER TABLE role_permissions DROP PRIMARY KEY');
            } catch (\Exception $e) {
                // PK mungkin belum ada — lanjutkan
            }

            DB::statement('ALTER TABLE role_permissions DROP COLUMN id');
        }

        // Pastikan composite PK terpasang
        try {
            DB::statement('ALTER TABLE role_permissions ADD PRIMARY KEY (role_id, permission_id)');
        } catch (\Exception $e) {
            // PK sudah ada — tidak masalah
        }
    }

    public function down(): void
    {
        // Kembalikan kolom id (tanpa AUTO_INCREMENT — kondisi semula yang bermasalah)
        if (Schema::hasTable('role_permissions') && !Schema::hasColumn('role_permissions', 'id')) {
            DB::statement('ALTER TABLE role_permissions DROP PRIMARY KEY');
            DB::statement('ALTER TABLE role_permissions ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        }
    }
};
