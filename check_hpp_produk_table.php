<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Check hpp_produk table structure
echo "=== CHECKING HPP_PRODUK TABLE STRUCTURE ===\n\n";

try {
    // Check if table exists
    echo "1. Checking if hpp_produk table exists...\n";
    $tables = DB::select("SHOW TABLES LIKE 'hpp_produk'");
    
    if (empty($tables)) {
        echo "   ✗ hpp_produk table does not exist\n";
        
        // Check for alternative table names
        echo "\n2. Looking for alternative HPP tables...\n";
        $allTables = DB::select("SHOW TABLES");
        $tableColumn = 'Tables_in_' . env('DB_DATABASE');
        
        foreach ($allTables as $table) {
            $tableName = $table->$tableColumn;
            if (stripos($tableName, 'hpp') !== false || stripos($tableName, 'cost') !== false) {
                echo "   Found table: {$tableName}\n";
            }
        }
        return;
    }
    
    echo "   ✓ hpp_produk table exists\n";
    
    // Check table structure
    echo "\n2. Checking table structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM hpp_produk");
    
    echo "   Columns in hpp_produk table:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    // Check sample data
    echo "\n3. Checking sample data...\n";
    $sampleData = DB::table('hpp_produk')->limit(3)->get();
    
    if ($sampleData->count() > 0) {
        echo "   Sample records found: " . $sampleData->count() . "\n";
        foreach ($sampleData as $record) {
            echo "   Record: " . json_encode($record) . "\n";
        }
    } else {
        echo "   No sample data found\n";
    }
    
    // Check for common HPP column names
    echo "\n4. Looking for price/cost columns...\n";
    $priceColumns = [];
    foreach ($columns as $column) {
        $fieldName = strtolower($column->Field);
        if (strpos($fieldName, 'harga') !== false || 
            strpos($fieldName, 'price') !== false || 
            strpos($fieldName, 'cost') !== false ||
            strpos($fieldName, 'hpp') !== false) {
            $priceColumns[] = $column->Field;
        }
    }
    
    if (!empty($priceColumns)) {
        echo "   Found price/cost columns: " . implode(', ', $priceColumns) . "\n";
    } else {
        echo "   No obvious price/cost columns found\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}