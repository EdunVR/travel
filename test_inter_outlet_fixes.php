<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Test Inter Outlet Fixes
echo "=== TESTING INTER OUTLET FIXES ===\n\n";

try {
    // Test 1: Check routes
    echo "1. Testing routes...\n";
    $routes = [
        'admin.penjualan.inter-outlet.history',
        'admin.penjualan.inter-outlet.history.data',
        'admin.penjualan.inter-outlet.coa-modal-data',
        'admin.penjualan.inter-outlet.price-products',
        'admin.penjualan.inter-outlet.update-price',
        'admin.penjualan.inter-outlet.bulk-update-prices'
    ];
    
    foreach ($routes as $routeName) {
        try {
            $url = route($routeName);
            echo "   ✓ Route '{$routeName}' exists\n";
        } catch (Exception $e) {
            echo "   ✗ Route '{$routeName}' not found\n";
        }
    }
    
    // Test 2: Check database tables
    echo "\n2. Checking database tables...\n";
    $tables = [
        'inter_outlet_sales',
        'inter_outlet_sale_items',
        'setting_coa_inter_outlet_sales',
        'outlets',
        'produk'
    ];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   ✓ Table '{$table}' exists with {$count} records\n";
        } catch (Exception $e) {
            echo "   ✗ Table '{$table}' not found or error: " . $e->getMessage() . "\n";
        }
    }
    
    // Test 3: Check markup_percent column
    echo "\n3. Checking markup_percent column...\n";
    try {
        $columns = DB::select("SHOW COLUMNS FROM produk");
        $hasMarkupColumn = false;
        
        foreach ($columns as $column) {
            if ($column->Field === 'markup_percent') {
                $hasMarkupColumn = true;
                echo "   ✓ markup_percent column exists\n";
                break;
            }
        }
        
        if (!$hasMarkupColumn) {
            echo "   ⚠ markup_percent column not found - migration needed\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Error checking markup_percent column: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Check controller methods
    echo "\n4. Checking controller methods...\n";
    $controller = new App\Http\Controllers\InterOutletSaleController(new App\Services\JournalEntryService());
    
    $methods = [
        'index',
        'getProducts',
        'getOutlets',
        'store',
        'history',
        'historyData',
        'getCoaModalData',
        'getPriceProducts',
        'updatePrice',
        'bulkUpdatePrices'
    ];
    
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   ✓ Method '{$method}' exists\n";
        } else {
            echo "   ✗ Method '{$method}' not found\n";
        }
    }
    
    // Test 5: Check file structure
    echo "\n5. Checking file structure...\n";
    $files = [
        'resources/views/admin/penjualan/inter-outlet/index.blade.php',
        'resources/views/admin/penjualan/inter-outlet/history.blade.php',
        'resources/views/admin/penjualan/inter-outlet/coa-settings.blade.php',
        'resources/views/admin/penjualan/inter-outlet/print.blade.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "   ✓ File '{$file}' exists\n";
        } else {
            echo "   ✗ File '{$file}' not found\n";
        }
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\nMANUAL TESTING STEPS:\n";
    echo "1. Go to: http://localhost/MORRA/admin/penjualan/inter-outlet\n";
    echo "2. Click 'Riwayat' - should open modal with working table\n";
    echo "3. Click 'Setting COA' - should load without errors\n";
    echo "4. Click 'Setting Harga' - should open with searchable product list\n";
    echo "5. Test search functionality in price settings modal\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}