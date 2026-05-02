<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIXING PRODUCTION PRODUCT_ID ISSUE ===\n\n";

try {
    echo "🔧 Making product_id column nullable in productions table...\n";
    
    // Make product_id nullable since we're using multi-product approach
    DB::statement('ALTER TABLE productions MODIFY COLUMN product_id bigint(20) unsigned NULL');
    
    echo "✅ Successfully made product_id column nullable\n\n";
    
    // Verify the change
    $columns = DB::select('DESCRIBE productions');
    $productIdColumn = collect($columns)->firstWhere('Field', 'product_id');
    
    if ($productIdColumn) {
        echo "🔍 VERIFICATION - PRODUCT_ID COLUMN AFTER FIX:\n";
        echo "   Type: {$productIdColumn->Type}\n";
        echo "   Null: {$productIdColumn->Null}\n";
        echo "   Default: " . ($productIdColumn->Default ?? 'NULL') . "\n";
        
        if ($productIdColumn->Null === 'YES') {
            echo "\n✅ SUCCESS: product_id column now allows NULL values\n";
            echo "   This will fix the SQL error when creating production records\n";
        } else {
            echo "\n❌ FAILED: product_id column still does not allow NULL\n";
        }
    }
    
    echo "\n📝 EXPLANATION:\n";
    echo "   - The production system is designed for multi-product manufacturing\n";
    echo "   - Individual products are stored in hpp_produk table linked to production_id\n";
    echo "   - The product_id column in productions table is legacy and should be nullable\n";
    echo "   - This fix allows creating production records without specifying a single product_id\n";
    
} catch (Exception $e) {
    echo "❌ Error fixing product_id issue: " . $e->getMessage() . "\n";
    echo "\n🔧 MANUAL FIX REQUIRED:\n";
    echo "   Run this SQL command manually in your database:\n";
    echo "   ALTER TABLE productions MODIFY COLUMN product_id bigint(20) unsigned NULL;\n";
}

echo "\n=== FIX COMPLETE ===\n";