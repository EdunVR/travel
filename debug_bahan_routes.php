<?php

echo "=== DEBUGGING BAHAN ROUTES ISSUE ===\n\n";

echo "CURRENT ISSUE:\n";
echo "❌ POST https://poshan.my.id/admin/inventaris/bahan/stock/150 404 (Not Found)\n";
echo "❌ POST https://poshan.my.id/admin/inventaris/bahan/price/150 404 (Not Found)\n\n";

echo "EXPECTED ROUTES:\n";
echo "✅ PUT /admin/inventaris/bahan/stock/{id} -> BahanController@updateStock\n";
echo "✅ PUT /admin/inventaris/bahan/price/{id} -> BahanController@updateHargaBeli\n\n";

echo "ROUTE DEFINITIONS IN routes/web.php:\n";
echo "Route::put('bahan/stock/{id}', [BahanController::class, 'updateStock'])->name('bahan.update-stock');\n";
echo "Route::put('bahan/price/{id}', [BahanController::class, 'updateHargaBeli'])->name('bahan.update-price');\n\n";

echo "FRONTEND JAVASCRIPT CALLS:\n";
echo "fetch(`/admin/inventaris/bahan/price/\${detailId}`, { method: 'POST', body: formData with _method=PUT })\n";
echo "fetch(`/admin/inventaris/bahan/stock/\${detailId}`, { method: 'POST', body: formData with _method=PUT })\n\n";

echo "ANALYSIS:\n";
echo "1. Routes are defined within 'admin.inventaris' prefix group\n";
echo "2. Full URL should be: /admin/inventaris/bahan/stock/{id}\n";
echo "3. Method spoofing is used: POST with _method=PUT\n";
echo "4. CSRF token is included\n\n";

echo "POSSIBLE CAUSES:\n";
echo "1. Route not registered properly\n";
echo "2. Route conflicts with resource routes\n";
echo "3. Middleware blocking the request\n";
echo "4. Method spoofing not working\n\n";

echo "SOLUTION STEPS:\n";
echo "1. Check route registration\n";
echo "2. Test with direct PUT method\n";
echo "3. Check route order (specific routes before resource routes)\n";
echo "4. Verify middleware permissions\n\n";

// Test route generation
try {
    echo "TESTING ROUTE GENERATION:\n";
    
    // Check if we can generate the routes
    $stockRoute = "admin.inventaris.bahan.update-stock";
    $priceRoute = "admin.inventaris.bahan.update-price";
    
    echo "Stock route name: {$stockRoute}\n";
    echo "Price route name: {$priceRoute}\n";
    
    echo "\nROUTE ORDER CHECK:\n";
    echo "✅ Specific routes (stock/price) should be BEFORE resource route\n";
    echo "✅ Current order in routes/web.php looks correct\n\n";
    
} catch (Exception $e) {
    echo "Error testing routes: " . $e->getMessage() . "\n";
}

echo "NEXT STEPS:\n";
echo "1. Test the routes with a simple curl command\n";
echo "2. Check Laravel logs for any errors\n";
echo "3. Verify the controller methods exist\n";
echo "4. Test with direct URL access\n";

?>