<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('piutang')) {
            return;
        }
        
        // First, remove any duplicate piutang records
        $this->removeDuplicatePiutang();
        
        // Check if unique constraint already exists
        $constraintExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'piutang' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME = 'unique_piutang_penjualan'
        ");
        
        // Only add constraint if it doesn't exist
        if (empty($constraintExists)) {
            Schema::table('piutang', function (Blueprint $table) {
                $table->unique('id_penjualan', 'unique_piutang_penjualan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->dropUnique('unique_piutang_penjualan');
        });
    }

    /**
     * Remove duplicate piutang records before adding unique constraint
     */
    private function removeDuplicatePiutang(): void
    {
        // Find duplicates based on id_penjualan
        $duplicates = DB::select("
            SELECT id_penjualan, COUNT(*) as count 
            FROM piutang 
            WHERE id_penjualan IS NOT NULL 
            GROUP BY id_penjualan 
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $duplicate) {
            // Keep the first record (smallest id_piutang), delete the rest
            DB::statement("
                DELETE FROM piutang 
                WHERE id_penjualan = ? 
                AND id_piutang NOT IN (
                    SELECT min_id FROM (
                        SELECT MIN(id_piutang) as min_id 
                        FROM piutang 
                        WHERE id_penjualan = ?
                    ) as temp
                )
            ", [$duplicate->id_penjualan, $duplicate->id_penjualan]);
            
            echo "Removed " . ($duplicate->count - 1) . " duplicate piutang records for id_penjualan: " . $duplicate->id_penjualan . "\n";
        }
    }
};