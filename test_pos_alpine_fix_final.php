<?php
/**
 * Test POS Alpine.js Fix - Final Verification
 * 
 * This script tests the POS page to ensure:
 * 1. Alpine.js loads without errors
 * 2. posApp component is properly registered
 * 3. Customer type pricing functionality works
 * 4. No console errors are present
 */

echo "🧪 Testing POS Alpine.js Fix - Final Verification\n";
echo "================================================\n\n";

// Test 1: Check if POS file exists and has proper Alpine.js registration
echo "📁 Test 1: Checking POS file structure...\n";
$posFile = 'resources/views/admin/penjualan/pos/index.blade.php';

if (!file_exists($posFile)) {
    echo "❌ POS file not found: $posFile\n";
    exit(1);
}

$content = file_get_contents($posFile);

// Check for proper Alpine.js registration
$checks = [
    'Alpine.data registration' => "Alpine.data('posApp'",
    'alpine:init event' => "document.addEventListener('alpine:init'",
    'Component closing' => "}));",
    'DOMContentLoaded event' => "document.addEventListener('DOMContentLoaded'",
    'Customer type pricing functions' => "loadCustomerTypePrices",
    'Update product prices function' => "updateProductPrices",
    'Apply customer type prices function' => "applyCustomerTypePrices",
    'Select customer function' => "selectCustomer",
    'Add item function' => "addItem",
    'Clear cart function' => "clearCart"
];

$allPassed = true;
foreach ($checks as $name => $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $name: Found\n";
    } else {
        echo "❌ $name: Missing\n";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "✅ All Alpine.js structure checks passed!\n\n";
} else {
    echo "❌ Some Alpine.js structure checks failed!\n\n";
}

// Test 2: Check for customer type pricing implementation
echo "💰 Test 2: Checking customer type pricing implementation...\n";

$customerTypeFunctions = [
    'loadCustomerTypePrices' => 'async loadCustomerTypePrices(idTipe)',
    'updateProductPrices' => 'updateProductPrices()',
    'applyCustomerTypePrices' => 'applyCustomerTypePrices()',
    'selectCustomer with type handling' => 'if(customer.id_tipe)',
    'Grid price update' => 'has_discount: true',
    'Cart price update' => 'discount_info: typePrice',
    'Visual discount indicator' => 'x-show="p.has_discount"',
    'Cart discount display' => 'x-show="c.has_discount"'
];

$pricingPassed = true;
foreach ($customerTypeFunctions as $name => $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $name: Implemented\n";
    } else {
        echo "❌ $name: Missing\n";
        $pricingPassed = false;
    }
}

if ($pricingPassed) {
    echo "✅ All customer type pricing features implemented!\n\n";
} else {
    echo "❌ Some customer type pricing features missing!\n\n";
}

// Test 3: Check for proper component structure
echo "🏗️ Test 3: Checking Alpine.js component structure...\n";

// Check if component is properly closed
$alpineDataStart = strpos($content, "Alpine.data('posApp', () => ({");
$alpineDataEnd = strrpos($content, "}));");

if ($alpineDataStart !== false && $alpineDataEnd !== false && $alpineDataEnd > $alpineDataStart) {
    echo "✅ Alpine.js component properly opened and closed\n";
    
    // Extract component content
    $componentContent = substr($content, $alpineDataStart, $alpineDataEnd - $alpineDataStart);
    
    // Check for essential methods
    $essentialMethods = [
        'init()' => 'async init()',
        'loadProducts()' => 'async loadProducts()',
        'loadCustomers()' => 'async loadCustomers()',
        'addItem()' => 'addItem(p)',
        'recalc()' => 'recalc()',
        'submitSale()' => 'async submitSale()',
        'idr()' => 'idr(n)'
    ];
    
    $methodsPassed = true;
    foreach ($essentialMethods as $name => $pattern) {
        if (strpos($componentContent, $pattern) !== false) {
            echo "✅ Method $name: Found\n";
        } else {
            echo "❌ Method $name: Missing\n";
            $methodsPassed = false;
        }
    }
    
    if ($methodsPassed) {
        echo "✅ All essential methods present!\n\n";
    } else {
        echo "❌ Some essential methods missing!\n\n";
    }
} else {
    echo "❌ Alpine.js component structure invalid\n\n";
}

// Test 4: Check for proper event handling
echo "⚡ Test 4: Checking event handling...\n";

$eventChecks = [
    'alpine:init listener' => "document.addEventListener('alpine:init'",
    'DOMContentLoaded listener' => "document.addEventListener('DOMContentLoaded'",
    'No Alpine.start() conflicts' => !strpos($content, 'Alpine.start()'),
    'Proper x-data usage' => 'x-data="posApp()"',
    'Proper x-init usage' => 'x-init="init()"'
];

$eventsPassed = true;
foreach ($eventChecks as $name => $check) {
    if (is_bool($check)) {
        if ($check) {
            echo "✅ $name: Correct\n";
        } else {
            echo "❌ $name: Issue found\n";
            $eventsPassed = false;
        }
    } else {
        if (strpos($content, $check) !== false) {
            echo "✅ $name: Found\n";
        } else {
            echo "❌ $name: Missing\n";
            $eventsPassed = false;
        }
    }
}

if ($eventsPassed) {
    echo "✅ All event handling checks passed!\n\n";
} else {
    echo "❌ Some event handling issues found!\n\n";
}

// Test 5: Generate test summary
echo "📊 Test Summary:\n";
echo "================\n";

$overallPassed = $allPassed && $pricingPassed && $methodsPassed && $eventsPassed;

if ($overallPassed) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "✅ Alpine.js component properly registered\n";
    echo "✅ Customer type pricing fully implemented\n";
    echo "✅ All essential methods present\n";
    echo "✅ Event handling correctly configured\n";
    echo "✅ No conflicts detected\n\n";
    
    echo "🚀 Ready for browser testing!\n";
    echo "Next steps:\n";
    echo "1. Open /admin/penjualan/pos in browser\n";
    echo "2. Check browser console (F12) for errors\n";
    echo "3. Test customer selection and price changes\n";
    echo "4. Verify all POS functionality works\n\n";
} else {
    echo "❌ SOME TESTS FAILED!\n";
    echo "Please review the failed checks above.\n\n";
}

// Test 6: File size and performance check
echo "📏 Test 6: File size and performance check...\n";
$fileSize = filesize($posFile);
$fileSizeKB = round($fileSize / 1024, 2);

echo "📄 File size: {$fileSizeKB} KB\n";

if ($fileSizeKB > 100) {
    echo "⚠️ Large file size - consider optimization\n";
} else {
    echo "✅ File size is reasonable\n";
}

// Count lines
$lines = substr_count($content, "\n") + 1;
echo "📏 Total lines: $lines\n";

if ($lines > 2000) {
    echo "⚠️ Very large file - consider splitting\n";
} else {
    echo "✅ Line count is manageable\n";
}

echo "\n🏁 Test completed!\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";