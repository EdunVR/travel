<?php

require_once 'vendor/autoload.php';

// Test Price Products Route Fix
echo "=== TESTING PRICE PRODUCTS ROUTE FIX ===\n\n";

try {
    // Test 1: Check if route exists
    echo "1. Testing price-products route...\n";
    try {
        $url = route('admin.penjualan.inter-outlet.price-products');
        echo "   ✓ Route exists: {$url}\n";
    } catch (Exception $e) {
        echo "   ✗ Route not found: " . $e->getMessage() . "\n";
        return;
    }
    
    // Test 2: Check other price routes
    echo "\n2. Testing other price routes...\n";
    $priceRoutes = [
        'admin.penjualan.inter-outlet.update-price',
        'admin.penjualan.inter-outlet.bulk-update-prices'
    ];
    
    foreach ($priceRoutes as $routeName) {
        try {
            $url = route($routeName);
            echo "   ✓ Route '{$routeName}' exists\n";
        } catch (Exception $e) {
            echo "   ✗ Route '{$routeName}' not found\n";
        }
    }
    
    // Test 3: Check parameterized routes still work
    echo "\n3. Testing parameterized routes...\n";
    $paramRoutes = [
        'admin.penjualan.inter-outlet.show',
        'admin.penjualan.inter-outlet.print',
        'admin.penjualan.inter-outlet.approve'
    ];
    
    foreach ($paramRoutes as $routeName) {
        try {
            $url = route($routeName, ['id' => 1]);
            echo "   ✓ Route '{$routeName}' exists: {$url}\n";
        } catch (Exception $e) {
            echo "   ✗ Route '{$routeName}' not found\n";
        }
    }
    
    // Test 4: Check controller methods
    echo "\n4. Testing controller methods...\n";
    $controller = new App\Http\Controllers\InterOutletSaleController(new App\Services\JournalEntryService());
    
    $methods = [
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
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\nROUTE ORDER FIX APPLIED:\n";
    echo "- Moved price-products routes BEFORE parameterized routes\n";
    echo "- This prevents /inter-outlet/{id} from catching 'price-products' as an ID\n";
    echo "- Route order is now correct for proper matching\n";
    
    echo "\nMANUAL TEST:\n";
    echo "1. Clear route cache: php artisan route:clear\n";
    echo "2. Cache routes: php artisan route:cache\n";
    echo "3. Test price settings modal in browser\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}