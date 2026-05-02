<?php

/**
 * Script untuk memperbaiki semua tabel yang missing AUTO_INCREMENT
 * Fixes: kategori, produk, dan tabel lain yang mungkin bermasalah
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX ALL AUTO INCREMENT TABLES ===\n\n";

// Daftar tabel dan primary key yang perlu dicek
$tables = [
    'kategori' => 'id_kategori',
    'produk' => 'id_produk',
    'satuan' => 'id_satuan',
    'product_images' => 'id_image',
];

$fixed = [];
$alreadyOk = [];
$errors = [];

foreach ($tables as $table => $primaryKey) {
    echo "Checking table: {$table} (PK: {$primaryKey})\n";
    echo str_repeat('-', 60) . "\n";
    
    try {
        // Check if table exists
        $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");
        if (empty($tableExists)) {
            echo "   ⚠ Table does not exist, skipping...\n\n";
            continue;
        }
        
        // Check column structure
        $columns = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$primaryKey}'");
        
        if (empty($columns)) {
            echo "   ✗ Primary key column '{$primaryKey}' not found!\n\n";
            $errors[] = "{$table}: Primary key not found";
            continue;
        }
        
        $column = $columns[0];
        echo "   Current: {$column->Type} - {$column->Key} - {$column->Extra}\n";
        
        // Check if already has AUTO_INCREMENT
        if (stripos($column->Extra, 'auto_increment') !== false) {
            echo "   ✓ Already has AUTO_INCREMENT\n\n";
            $alreadyOk[] = $table;
            continue;
        }
        
        echo "   ✗ Missing AUTO_INCREMENT, fixing...\n";
        
        // Backup count
        $count = DB::table($table)->count();
        echo "   Records: {$count}\n";
        
        // Drop foreign keys if any
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND COLUMN_NAME = '{$primaryKey}' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $fk) {
            echo "   Dropping FK: {$fk->CONSTRAINT_NAME}\n";
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }
        
        // Modify column to add AUTO_INCREMENT
        DB::statement("ALTER TABLE {$table} MODIFY COLUMN {$primaryKey} INT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
        echo "   ✓ AUTO_INCREMENT added\n";
        
        // Verify
        $columns = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$primaryKey}'");
        $column = $columns[0];
        
        if (stripos($column->Extra, 'auto_increment') !== false) {
            echo "   ✓ Verified: {$column->Type} - {$column->Key} - {$column->Extra}\n";
            $fixed[] = $table;
        } else {
            echo "   ✗ Verification failed!\n";
            $errors[] = "{$table}: Failed to add AUTO_INCREMENT";
        }
        
    } catch (\Exception $e) {
        echo "   ✗ ERROR: " . $e->getMessage() . "\n";
        $errors[] = "{$table}: " . $e->getMessage();
    }
    
    echo "\n";
}

// Summary
echo str_repeat('=', 60) . "\n";
echo "SUMMARY\n";
echo str_repeat('=', 60) . "\n\n";

if (!empty($alreadyOk)) {
    echo "✓ Already OK (" . count($alreadyOk) . "):\n";
    foreach ($alreadyOk as $table) {
        echo "  - {$table}\n";
    }
    echo "\n";
}

if (!empty($fixed)) {
    echo "✓ Fixed (" . count($fixed) . "):\n";
    foreach ($fixed as $table) {
        echo "  - {$table}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "✗ Errors (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
    echo "\n";
}

if (empty($errors)) {
    echo "=== ALL TABLES FIXED SUCCESSFULLY ===\n";
} else {
    echo "=== COMPLETED WITH ERRORS ===\n";
    exit(1);
}
