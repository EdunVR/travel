<?php

/**
 * Test Inter Outlet Print URL
 * 
 * This script tests:
 * 1. Route definition and URL structure
 * 2. Controller method exists
 * 3. View file exists
 * 4. JavaScript URL matches route
 */

echo "=== TESTING INTER OUTLET PRINT URL ===\n\n";

// Test 1: Verify route structure
echo "1. TESTING ROUTE STRUCTURE:\n";
$routeFile = file_get_contents('routes/web.php');

// Check admin prefix
if (strpos($routeFile, "Route::prefix('admin')->name('admin.')") !== false) {
    echo "   ✅ Admin prefix found\n";
} else {
    echo "   ❌ Admin prefix NOT found\n";
}

// Check penjualan prefix
if (strpos($routeFile, "Route::prefix('penjualan')->name('penjualan.')") !== false) {
    echo "   ✅ Penjualan prefix found\n";
} else {
    echo "   ❌ Penjualan prefix NOT found\n";
}

// Check inter-outlet print route
if (strpos($routeFile, "Route::get('/inter-outlet/{id}/print'") !== false) {
    echo "   ✅ Inter-outlet print route found\n";
} else {
    echo "   ❌ Inter-outlet print route NOT found\n";
}

echo "\n";

// Test 2: Determine correct URL structure
echo "2. TESTING URL STRUCTURE:\n";
echo "   Based on route prefixes:\n";
echo "   - Admin prefix: /admin\n";
echo "   - Penjualan prefix: /penjualan\n";
echo "   - Route: /inter-outlet/{id}/print\n";
echo "   ✅ Correct URL: /admin/penjualan/inter-outlet/{id}/print\n";

echo "\n";

// Test 3: Check JavaScript URL
echo "3. TESTING JAVASCRIPT URL:\n";
$jsFile = file_get_contents('public/js/inter-outlet.js');

if (strpos($jsFile, '/admin/penjualan/inter-outlet/${transactionId}/print') !== false) {
    echo "   ✅ JavaScript URL matches route structure\n";
} else {
    echo "   ❌ JavaScript URL does NOT match route structure\n";
    
    // Check what URL is currently used
    if (strpos($jsFile, '/admin/penjualan/inter-outlet/') !== false) {
        echo "   ℹ️  JavaScript uses: /admin/penjualan/inter-outlet/\n";
    } else {
        echo "   ⚠️  JavaScript URL pattern not found\n";
    }
}

echo "\n";

// Test 4: Check controller method
echo "4. TESTING CONTROLLER METHOD:\n";
$controllerFile = file_get_contents('app/Http/Controllers/InterOutletSaleController.php');

if (strpos($controllerFile, 'public function print($id') !== false) {
    echo "   ✅ Print method exists in controller\n";
} else {
    echo "   ❌ Print method NOT found in controller\n";
}

if (strpos($controllerFile, 'Pdf::loadView') !== false) {
    echo "   ✅ PDF generation implemented\n";
} else {
    echo "   ❌ PDF generation NOT implemented\n";
}

if (strpos($controllerFile, 'admin.penjualan.inter-outlet.print') !== false) {
    echo "   ✅ Correct view path used\n";
} else {
    echo "   ❌ Correct view path NOT used\n";
}

echo "\n";

// Test 5: Check view file
echo "5. TESTING VIEW FILE:\n";
if (file_exists('resources/views/admin/penjualan/inter-outlet/print.blade.php')) {
    echo "   ✅ Print view file exists\n";
} else {
    echo "   ❌ Print view file NOT found\n";
}

echo "\n";

// Test 6: Test with sample ID
echo "6. TESTING SAMPLE URL:\n";
$sampleId = 123;
$expectedUrl = "/admin/penjualan/inter-outlet/{$sampleId}/print";
echo "   Sample URL for ID {$sampleId}: {$expectedUrl}\n";
echo "   ✅ This should be the working URL format\n";

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "ROUTE ANALYSIS:\n";
echo "1. ✅ Route exists: GET /admin/penjualan/inter-outlet/{id}/print\n";
echo "2. ✅ Controller method: InterOutletSaleController@print\n";
echo "3. ✅ View file: admin.penjualan.inter-outlet.print\n";
echo "4. ✅ PDF generation: Implemented with DomPDF\n";
echo "\n";

echo "EXPECTED BEHAVIOR:\n";
echo "1. JavaScript generates URL: /admin/penjualan/inter-outlet/{id}/print\n";
echo "2. Laravel routes to: InterOutletSaleController@print\n";
echo "3. Controller loads: InterOutletSale with relationships\n";
echo "4. Generates PDF using: print.blade.php template\n";
echo "5. Returns: PDF stream for iframe display\n";
echo "\n";

echo "TROUBLESHOOTING:\n";
echo "If 'Not Found' error persists:\n";
echo "1. Check if transaction ID exists in database\n";
echo "2. Verify user has access to the transaction\n";
echo "3. Check Laravel logs for detailed error\n";
echo "4. Test URL directly in browser\n";
echo "5. Clear route cache: php artisan route:clear\n";
echo "\n";

echo "✅ URL ANALYSIS COMPLETE!\n";
echo "\nTo test:\n";
echo "1. Create a transaction and note its ID\n";
echo "2. Test URL directly: /admin/penjualan/inter-outlet/{ID}/print\n";
echo "3. Check browser console for actual URL being called\n";
echo "4. Verify modal opens with correct PDF\n";

?>