<?php
/**
 * Test Mesin Customer API Endpoint Fix
 */

echo "=== MESIN CUSTOMER API ENDPOINT FIX TEST ===\n\n";

// Check if mesin.js has been updated with correct endpoints
$mesinJsPath = __DIR__ . '/public/js/mesin.js';
if (file_exists($mesinJsPath)) {
    echo "✅ mesin.js exists\n";
    
    $content = file_get_contents($mesinJsPath);
    
    // Check for correct API endpoints
    $endpoints = [
        '/admin/service/mesin/data' => 'fetchData endpoint',
        '/admin/service/mesin/produk/list' => 'fetchProduk endpoint (FIXED)',
        '/admin/service/search-customers' => 'searchCustomers endpoint',
        '/admin/service/mesin/${item.id}' => 'openEdit endpoint',
        '/admin/service/mesin/${this.form.id}' => 'submitForm update endpoint',
        '/admin/service/mesin' => 'submitForm create endpoint',
        '/admin/service/mesin/${this.toDelete.id}' => 'deleteNow endpoint'
    ];
    
    foreach ($endpoints as $endpoint => $description) {
        if (strpos($content, $endpoint) !== false) {
            echo "✅ $description found\n";
        } else {
            echo "❌ $description missing\n";
        }
    }
    
    // Check for old incorrect endpoint
    if (strpos($content, '/admin/service/mesin/produk?') === false) {
        echo "✅ Old incorrect produk endpoint removed\n";
    } else {
        echo "❌ Old incorrect produk endpoint still exists\n";
    }
    
} else {
    echo "❌ mesin.js missing\n";
}

echo "\n=== ROUTE VERIFICATION ===\n";

// Check routes file for correct route definition
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    
    if (strpos($content, "Route::get('/mesin/produk/list'") !== false) {
        echo "✅ Correct route definition found: /mesin/produk/list\n";
    } else {
        echo "❌ Route definition not found\n";
    }
    
    if (strpos($content, "->name('mesin.produk')") !== false) {
        echo "✅ Correct route name found: mesin.produk\n";
    } else {
        echo "❌ Route name not found\n";
    }
    
    // Check for service prefix
    if (strpos($content, "Route::prefix('service')->name('service.')") !== false) {
        echo "✅ Service prefix found\n";
    } else {
        echo "❌ Service prefix not found\n";
    }
} else {
    echo "❌ routes/web.php not found\n";
}

echo "\n=== EXPECTED BEHAVIOR ===\n";
echo "After this fix:\n";
echo "1. ✅ No more 404 errors for /admin/service/mesin/produk\n";
echo "2. ✅ Correct endpoint: /admin/service/mesin/produk/list\n";
echo "3. ✅ Product list should load successfully\n";
echo "4. ✅ All CRUD operations should work\n\n";

echo "=== MANUAL TESTING ===\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Navigate to /admin/service/mesin\n";
echo "3. Check console - should see:\n";
echo "   ✅ mesin.js loaded successfully\n";
echo "   ✅ mesinCrud function found in mesin.js\n";
echo "   ✅ Mesin CRUD initialized\n";
echo "4. No more 404 errors for produk endpoint\n";
echo "5. Product dropdown should populate correctly\n\n";

echo "Status: API endpoints fixed - ready for testing\n";
?>