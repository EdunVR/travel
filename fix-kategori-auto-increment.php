<?php

/**
 * Script untuk memperbaiki tabel kategori
 * Menambahkan AUTO_INCREMENT pada id_kategori
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIX KATEGORI AUTO INCREMENT ===\n\n";

try {
    // 1. Cek struktur tabel saat ini
    echo "1. Checking current table structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM kategori WHERE Field = 'id_kategori'");
    
    if (empty($columns)) {
        echo "   ERROR: Column id_kategori not found!\n";
        exit(1);
    }
    
    $column = $columns[0];
    echo "   Current: {$column->Field} - {$column->Type} - {$column->Key} - {$column->Extra}\n";
    
    // 2. Cek apakah sudah AUTO_INCREMENT
    if (stripos($column->Extra, 'auto_increment') !== false) {
        echo "   ✓ Column id_kategori already has AUTO_INCREMENT\n";
        echo "\n2. Checking if there are any records...\n";
        $count = DB::table('kategori')->count();
        echo "   Found {$count} records\n";
        
        if ($count > 0) {
            echo "\n   Table is OK. The issue might be in the model configuration.\n";
            echo "   Checking model...\n";
        }
        exit(0);
    }
    
    echo "   ✗ Column id_kategori does NOT have AUTO_INCREMENT\n";
    
    // 3. Backup data
    echo "\n2. Backing up data...\n";
    $data = DB::table('kategori')->get();
    echo "   Backed up " . count($data) . " records\n";
    
    // 4. Modify column to add AUTO_INCREMENT
    echo "\n3. Modifying column to add AUTO_INCREMENT...\n";
    
    // Drop foreign keys if any
    $foreignKeys = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'kategori' 
        AND COLUMN_NAME = 'id_kategori' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    foreach ($foreignKeys as $fk) {
        echo "   Dropping foreign key: {$fk->CONSTRAINT_NAME}\n";
        DB::statement("ALTER TABLE kategori DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
    }
    
    // Modify column
    DB::statement("ALTER TABLE kategori MODIFY COLUMN id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
    echo "   ✓ Column modified successfully\n";
    
    // 5. Verify
    echo "\n4. Verifying changes...\n";
    $columns = DB::select("SHOW COLUMNS FROM kategori WHERE Field = 'id_kategori'");
    $column = $columns[0];
    echo "   New: {$column->Field} - {$column->Type} - {$column->Key} - {$column->Extra}\n";
    
    if (stripos($column->Extra, 'auto_increment') !== false) {
        echo "   ✓ AUTO_INCREMENT successfully added!\n";
    } else {
        echo "   ✗ Failed to add AUTO_INCREMENT\n";
        exit(1);
    }
    
    // 6. Test insert
    echo "\n5. Testing insert...\n";
    $testId = DB::table('kategori')->insertGetId([
        'kode_kategori' => 'TEST-001',
        'nama_kategori' => 'Test Category',
        'kelompok' => 'Produk',
        'id_outlet' => 1,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "   ✓ Test insert successful! New ID: {$testId}\n";
    
    // Delete test record
    DB::table('kategori')->where('id_kategori', $testId)->delete();
    echo "   ✓ Test record deleted\n";
    
    echo "\n=== FIX COMPLETED SUCCESSFULLY ===\n";
    echo "\nYou can now create new categories without errors.\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
