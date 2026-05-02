<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING PRODUCTION TABLE STRUCTURE ===\n\n";

try {
    // Check if table exists
    if (Schema::hasTable('productions')) {
        echo "✅ Productions table exists\n\n";
        
        // Get table structure
        $columns = DB::select('DESCRIBE productions');
        
        echo "📋 Table Structure:\n";
        echo str_pad('Field', 25) . str_pad('Type', 20) . str_pad('Null', 8) . str_pad('Key', 8) . str_pad('Default', 15) . "Extra\n";
        echo str_repeat('-', 85) . "\n";
        
        foreach ($columns as $column) {
            echo str_pad($column->Field, 25) . 
                 str_pad($column->Type, 20) . 
                 str_pad($column->Null, 8) . 
                 str_pad($column->Key ?? '', 8) . 
                 str_pad($column->Default ?? 'NULL', 15) . 
                 ($column->Extra ?? '') . "\n";
        }
        
        echo "\n";
        
        // Check specifically for product_id column
        $productIdColumn = collect($columns)->firstWhere('Field', 'product_id');
        if ($productIdColumn) {
            echo "🔍 PRODUCT_ID COLUMN DETAILS:\n";
            echo "   Type: {$productIdColumn->Type}\n";
            echo "   Null: {$productIdColumn->Null}\n";
            echo "   Default: " . ($productIdColumn->Default ?? 'NULL') . "\n";
            echo "   Key: " . ($productIdColumn->Key ?? 'None') . "\n";
            
            if ($productIdColumn->Null === 'NO' && ($productIdColumn->Default === null || $productIdColumn->Default === 'NULL')) {
                echo "\n❌ PROBLEM FOUND: product_id column does NOT allow NULL and has NO default value\n";
                echo "   This is causing the SQL error when creating production records\n";
            }
        } else {
            echo "❌ product_id column not found in productions table\n";
        }
        
    } else {
        echo "❌ Productions table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";