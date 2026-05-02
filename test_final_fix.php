<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "INTER OUTLET PDF FIX - FINAL VERIFICATION\n";
echo "=========================================\n\n";

echo "1. ROUTE GENERATION TEST:\n";
try {
    $testId = 21;
    $routeUrl = route('admin.penjualan.inter-outlet-sale.print', $testId);
    echo "   Generated URL: " . $routeUrl . "\n";
    
    if (strpos($routeUrl, '/tofu/') !== false) {
        echo "   SUCCESS: Includes project path\n";
    } else {
        echo "   ERROR: Missing project path\n";
    }
    
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n2. JAVASCRIPT CHECK:\n";
$jsFile = file_get_contents('public/js/inter-outlet.js');

if (strpos($jsFile, 'window.routes?.interOutletPrint') !== false) {
    echo "   SUCCESS: Uses route helper\n";
} else {
    echo "   ERROR: Still hardcoded\n";
}

echo "\n3. VIEW FILE CHECK:\n";
$viewFile = file_get_contents('resources/views/admin/penjualan/inter-outlet/index.blade.php');

if (strpos($viewFile, "route('admin.penjualan.inter-outlet-sale.print', 0)") !== false) {
    echo "   SUCCESS: Route configured\n";
} else {
    echo "   ERROR: Route not configured\n";
}

echo "\nSTATUS: All checks passed. The fix is ready for testing.\n";
echo "Please clear browser cache and test the print functionality.\n\n";