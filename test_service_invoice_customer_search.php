<?php

/**
 * Test script for Service Invoice customer search functionality
 */

echo "🔍 Testing Service Invoice Customer Search\n";
echo "=" . str_repeat("=", 45) . "\n\n";

// Test 1: Check if the JavaScript files exist
echo "1. Checking JavaScript files...\n";

$jsFiles = [
    'public/js/service-invoice-autocomplete-fixed.js' => 'Customer autocomplete script',
    'public/js/service-invoice.js' => 'Main service invoice script'
];

foreach ($jsFiles as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description found\n";
    } else {
        echo "   ❌ $description NOT FOUND\n";
    }
}

echo "\n";

// Test 2: Check customer search implementation
echo "2. Checking customer search implementation...\n";

$autocompleteFile = 'public/js/service-invoice-autocomplete-fixed.js';
if (file_exists($autocompleteFile)) {
    $content = file_get_contents($autocompleteFile);
    
    $checks = [
        'customer.id_member' => 'Uses correct customer ID field',
        'searchCustomersAutocomplete' => 'Has customer search function',
        'selectCustomerAutocomplete' => 'Has customer selection function',
        'outlet_id' => 'Includes outlet filtering',
        'window.serviceRoutes?.searchCustomers' => 'Uses Laravel routes',
        'data.customers && data.customers.length > 0' => 'Handles API response correctly'
    ];
    
    foreach ($checks as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ Autocomplete file not found\n";
}

echo "\n";

// Test 3: Check view file implementation
echo "3. Checking view file implementation...\n";

$viewFile = 'resources/views/admin/service/invoice/index.blade.php';
if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    
    $checks = [
        'id="customer-search"' => 'Customer search input exists',
        'id="customer-dropdown"' => 'Customer dropdown container exists',
        'id="id_member"' => 'Hidden customer ID field exists',
        'window.serviceRoutes' => 'Routes passed to JavaScript',
        'admin.service.search-customers' => 'Search customers route defined'
    ];
    
    foreach ($checks as $pattern => $description) {
        if (strpos($viewContent, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// Test 4: Check for potential issues
echo "4. Checking for potential issues...\n";

if (file_exists($autocompleteFile)) {
    $content = file_get_contents($autocompleteFile);
    
    // Check for potential issues similar to mesin.js
    $issues = [
        'customerLocked' => 'Customer lock mechanism (prevents clearing selection)',
        'debounce' => 'Search debouncing (prevents too many requests)',
        'clearTimeout' => 'Timeout clearing (prevents race conditions)'
    ];
    
    foreach ($issues as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ Has $description\n";
        } else {
            echo "   ⚠️  Missing $description\n";
        }
    }
}

echo "\n";

// Test 5: Compare with fixed mesin.js implementation
echo "5. Comparing with fixed mesin.js implementation...\n";

$mesinFile = 'public/js/mesin.js';
if (file_exists($mesinFile) && file_exists($autocompleteFile)) {
    $mesinContent = file_get_contents($mesinFile);
    $autocompleteContent = file_get_contents($autocompleteFile);
    
    $mesinFeatures = [
        'customerLocked' => 'Customer lock mechanism',
        'checkCustomerSelection' => 'Customer selection validation',
        'Customer already selected and locked, skipping search' => 'Search prevention when locked',
        'No customers found, but customer is locked - keeping selection' => 'Selection preservation'
    ];
    
    echo "   📊 Features comparison:\n";
    foreach ($mesinFeatures as $feature => $description) {
        $inMesin = strpos($mesinContent, $feature) !== false;
        $inAutocomplete = strpos($autocompleteContent, $feature) !== false;
        
        if ($inMesin && !$inAutocomplete) {
            echo "     ⚠️  $description - Available in mesin.js but missing in service invoice\n";
        } elseif ($inMesin && $inAutocomplete) {
            echo "     ✅ $description - Available in both\n";
        }
    }
} else {
    echo "   ❌ Cannot compare - files not found\n";
}

echo "\n";

// Summary and recommendations
echo "📋 ANALYSIS SUMMARY:\n";
echo "=" . str_repeat("=", 20) . "\n";
echo "The Service Invoice customer search appears to have a basic implementation\n";
echo "but may be missing some advanced features that were added to mesin.js:\n\n";

echo "🔧 POTENTIAL IMPROVEMENTS NEEDED:\n";
echo "1. Customer lock mechanism to prevent clearing selected customers\n";
echo "2. Better search debouncing and race condition handling\n";
echo "3. Selection preservation when subsequent searches fail\n";
echo "4. Enhanced error handling and user feedback\n";
echo "5. Debug logging for troubleshooting\n";

echo "\n🧪 TESTING STEPS:\n";
echo "1. Open Service Invoice page\n";
echo "2. Try typing customer name in search field\n";
echo "3. Check if suggestions appear\n";
echo "4. Select a customer and see if it stays selected\n";
echo "5. Check browser console for any errors\n";

echo "\n✨ Analysis completed!\n";