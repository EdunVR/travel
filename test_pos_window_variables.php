<?php
/**
 * Test POS Window Variables Setup
 * Verifies that the POS template properly sets window variables
 */

echo "🧪 Testing POS Window Variables Setup\n";
echo "====================================\n\n";

// Read the POS template
$posTemplate = file_get_contents('resources/views/admin/penjualan/pos/index.blade.php');

if (!$posTemplate) {
    echo "❌ Could not read POS template\n";
    exit(1);
}

// Check for window variables
$windowVars = [
    'window.posInitialOutlet',
    'window.posCSRFToken',
    'window.posProductsRoute',
    'window.posCustomersRoute',
    'window.posCustomerTypePricesRoute',
    'window.posAccountingBooksRoute',
    'window.posChartOfAccountsRoute',
    'window.posCoaSettingsRoute',
    'window.posCoaSettingsUpdateRoute',
    'window.posStoreRoute',
    'window.posPrintRoute',
    'window.posHistoryRoute',
    'window.posDashboardRoute',
    'window.posLoginRoute'
];

$allFound = true;
foreach ($windowVars as $var) {
    if (strpos($posTemplate, $var) !== false) {
        echo "✅ Found: $var\n";
    } else {
        echo "❌ Missing: $var\n";
        $allFound = false;
    }
}

// Check for problematic inline Alpine.js code
$problematicPatterns = [
    'Alpine.data(\'posApp\'',
    'document.addEventListener(\'alpine:init\'',
    'async init()',
    'async loadProducts()',
    'async selectCustomer('
];

echo "\n📄 Checking for problematic inline Alpine.js code...\n";
$hasProblems = false;
foreach ($problematicPatterns as $pattern) {
    if (strpos($posTemplate, $pattern) !== false) {
        echo "❌ Found problematic code: $pattern\n";
        $hasProblems = true;
    }
}

if (!$hasProblems) {
    echo "✅ No problematic inline Alpine.js code found\n";
}

// Check for proper script structure
echo "\n📄 Checking script structure...\n";
if (strpos($posTemplate, 'console.log(\'✅ [POS] Initialization variables set up for separate pos.js file\');') !== false) {
    echo "✅ Proper initialization log found\n";
} else {
    echo "❌ Initialization log not found\n";
    $allFound = false;
}

// Check for JsBarcode
if (strpos($posTemplate, 'jsbarcode@3.11.5') !== false) {
    echo "✅ JsBarcode library included\n";
} else {
    echo "❌ JsBarcode library not found\n";
    $allFound = false;
}

echo "\n🎯 Summary:\n";
echo "==========\n";
if ($allFound && !$hasProblems) {
    echo "🎉 All window variables properly set!\n";
    echo "✅ No problematic inline Alpine.js code\n";
    echo "✅ Template structure is correct\n\n";
    
    echo "📋 Expected browser behavior:\n";
    echo "- Window variables should be defined\n";
    echo "- Routes should resolve properly (no 'undefined' in URLs)\n";
    echo "- POS should load products successfully\n";
    echo "- No Alpine.js console errors\n";
} else {
    echo "❌ Issues found in POS template\n";
    if (!$allFound) {
        echo "❌ Missing window variables\n";
    }
    if ($hasProblems) {
        echo "❌ Problematic inline Alpine.js code detected\n";
    }
}

echo "\n";
?>