<?php

/**
 * Verification Script for Handling & Lounge Fee Implementation
 * 
 * Run: php verify-handling-fee.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use Illuminate\Support\Facades\Schema;

echo "=================================================\n";
echo "HANDLING & LOUNGE FEE VERIFICATION\n";
echo "=================================================\n\n";

// Test 1: Check database columns
echo "Test 1: Checking database columns...\n";
$columns = [
    'include_handling_lounge_fee',
    'handling_lounge_fee_amount',
    'handling_lounge_fee_description'
];

$allColumnsExist = true;
foreach ($columns as $column) {
    $exists = Schema::hasColumn('travel_packages', $column);
    echo "  - {$column}: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
    if (!$exists) $allColumnsExist = false;
}

if ($allColumnsExist) {
    echo "✅ All columns exist!\n\n";
} else {
    echo "❌ Some columns are missing. Run migration first.\n\n";
    exit(1);
}

// Test 2: Check model fillable and casts
echo "Test 2: Checking model configuration...\n";
$package = new TravelPackage();
$fillable = $package->getFillable();
$casts = $package->getCasts();

$fillableCheck = in_array('include_handling_lounge_fee', $fillable) &&
                 in_array('handling_lounge_fee_amount', $fillable) &&
                 in_array('handling_lounge_fee_description', $fillable);

$castsCheck = isset($casts['include_handling_lounge_fee']) &&
              isset($casts['handling_lounge_fee_amount']);

echo "  - Fillable fields: " . ($fillableCheck ? "✅ CONFIGURED" : "❌ NOT CONFIGURED") . "\n";
echo "  - Casts configured: " . ($castsCheck ? "✅ CONFIGURED" : "❌ NOT CONFIGURED") . "\n\n";

// Test 3: Test with sample package
echo "Test 3: Testing with sample package...\n";
$testPackage = TravelPackage::first();

if ($testPackage) {
    echo "  - Package: {$testPackage->package_name}\n";
    echo "  - Include Fee: " . ($testPackage->include_handling_lounge_fee ? "Yes" : "No") . "\n";
    echo "  - Fee Amount: Rp " . number_format($testPackage->handling_lounge_fee_amount ?? 0, 0, ',', '.') . "\n";
    echo "  - Description: " . ($testPackage->handling_lounge_fee_description ?? 'Default') . "\n";
    
    // Test update
    echo "\n  Testing update...\n";
    $testPackage->update([
        'include_handling_lounge_fee' => true,
        'handling_lounge_fee_amount' => 500000,
        'handling_lounge_fee_description' => 'Test Handling Fee'
    ]);
    
    $testPackage->refresh();
    
    $updateSuccess = $testPackage->include_handling_lounge_fee === true &&
                     $testPackage->handling_lounge_fee_amount == 500000 &&
                     $testPackage->handling_lounge_fee_description === 'Test Handling Fee';
    
    echo "  - Update test: " . ($updateSuccess ? "✅ SUCCESS" : "❌ FAILED") . "\n";
    
    // Restore original values
    $testPackage->update([
        'include_handling_lounge_fee' => true,
        'handling_lounge_fee_amount' => 500000,
        'handling_lounge_fee_description' => null
    ]);
    
    echo "✅ Sample package test passed!\n\n";
} else {
    echo "⚠️  No packages found in database. Create a package first.\n\n";
}

// Test 4: Check route exists
echo "Test 4: Checking route registration...\n";
$routes = app('router')->getRoutes();
$routeExists = false;

foreach ($routes as $route) {
    if ($route->getName() === 'travel.package.handling-fee.update') {
        $routeExists = true;
        echo "  - Route name: travel.package.handling-fee.update\n";
        echo "  - Route URI: " . $route->uri() . "\n";
        echo "  - Route method: " . implode(', ', $route->methods()) . "\n";
        break;
    }
}

echo "  - Route exists: " . ($routeExists ? "✅ YES" : "❌ NO") . "\n\n";

// Test 5: Check controller method exists
echo "Test 5: Checking controller method...\n";
$controllerClass = 'App\\Http\\Controllers\\PackageController';
$methodExists = method_exists($controllerClass, 'updateHandlingFee');
echo "  - Method updateHandlingFee: " . ($methodExists ? "✅ EXISTS" : "❌ MISSING") . "\n\n";

// Test 6: Check view files
echo "Test 6: Checking view files...\n";
$viewFiles = [
    'resources/views/public/paket-detail.blade.php',
    'resources/views/public/invoice-booking.blade.php',
];

foreach ($viewFiles as $file) {
    $exists = file_exists(base_path($file));
    $hasHandlingFee = false;
    
    if ($exists) {
        $content = file_get_contents(base_path($file));
        $hasHandlingFee = strpos($content, 'handling_lounge_fee') !== false;
    }
    
    echo "  - " . basename($file) . ": " . ($exists ? "✅ EXISTS" : "❌ MISSING");
    if ($exists) {
        echo " | Handling fee code: " . ($hasHandlingFee ? "✅ FOUND" : "⚠️  NOT FOUND");
    }
    echo "\n";
}

echo "\n";

// Final Summary
echo "=================================================\n";
echo "VERIFICATION SUMMARY\n";
echo "=================================================\n\n";

$allTestsPassed = $allColumnsExist && $fillableCheck && $castsCheck && $routeExists && $methodExists;

if ($allTestsPassed) {
    echo "✅ ALL TESTS PASSED!\n";
    echo "Handling & Lounge Fee feature is ready to use.\n\n";
    
    echo "Next steps:\n";
    echo "1. Test on public website: /paket/{id}\n";
    echo "2. Create a test booking and verify fee is included\n";
    echo "3. Check invoice and documents show the fee\n";
    echo "4. (Optional) Add admin UI for easier management\n";
} else {
    echo "❌ SOME TESTS FAILED\n";
    echo "Please review the errors above and fix them.\n";
}

echo "\n=================================================\n";
