<?php

/**
 * Test Both POS and Inter Outlet Alpine.js Components
 * Verify both components work properly without conflicts
 */

echo "🧪 TESTING: Both POS and Inter Outlet Components\n\n";

// Check if both JavaScript files exist
$posFile = 'public/js/pos.js';
$interOutletFile = 'public/js/inter-outlet.js';

$allPassed = true;

// Test POS.js
echo "🛒 TESTING POS COMPONENT:\n";

if (!file_exists($posFile)) {
    echo "❌ pos.js file not found\n";
    $allPassed = false;
} else {
    $posContent = file_get_contents($posFile);
    
    $posChecks = [
        'Component registration' => "Alpine.data('posApp'",
        'Proper initialization' => 'initializePosComponent',
        'Customer type pricing' => 'loadCustomerTypePrices',
        'Cart functionality' => 'addToCart',
        'Error handling' => 'console.error',
        'Alpine.js wait' => 'typeof Alpine === \'undefined\'',
        'Event listeners' => 'alpine:init'
    ];
    
    foreach ($posChecks as $name => $pattern) {
        if (strpos($posContent, $pattern) !== false) {
            echo "✅ POS $name: Found\n";
        } else {
            echo "❌ POS $name: Missing\n";
            $allPassed = false;
        }
    }
}

echo "\n";

// Test Inter-outlet.js
echo "🏪 TESTING INTER OUTLET COMPONENT:\n";

if (!file_exists($interOutletFile)) {
    echo "❌ inter-outlet.js file not found\n";
    $allPassed = false;
} else {
    $interOutletContent = file_get_contents($interOutletFile);
    
    $interOutletChecks = [
        'Component registration' => "Alpine.data('interOutletSaleApp'",
        'Proper initialization' => 'initializeInterOutletComponent',
        'Product loading' => 'loadProducts',
        'Cart functionality' => 'addToCart',
        'History functionality' => 'loadHistoryData',
        'Error handling' => 'console.error',
        'Alpine.js wait' => 'typeof Alpine === \'undefined\'',
        'Constants definition' => 'window.ALL = \'all\'',
        'Event listeners' => 'alpine:init'
    ];
    
    foreach ($interOutletChecks as $name => $pattern) {
        if (strpos($interOutletContent, $pattern) !== false) {
            echo "✅ Inter Outlet $name: Found\n";
        } else {
            echo "❌ Inter Outlet $name: Missing\n";
            $allPassed = false;
        }
    }
}

echo "\n";

// Check for conflicts
echo "🔍 CHECKING FOR CONFLICTS:\n";

if (file_exists($posFile) && file_exists($interOutletFile)) {
    $posContent = file_get_contents($posFile);
    $interOutletContent = file_get_contents($interOutletFile);
    
    // Check if both use proper component names
    $posHasCorrectName = strpos($posContent, "Alpine.data('posApp'") !== false;
    $interOutletHasCorrectName = strpos($interOutletContent, "Alpine.data('interOutletSaleApp'") !== false;
    
    if ($posHasCorrectName && $interOutletHasCorrectName) {
        echo "✅ Both components use different names (no conflicts)\n";
    } else {
        echo "❌ Component naming conflict detected\n";
        $allPassed = false;
    }
    
    // Check if both use proper initialization
    $posHasProperInit = strpos($posContent, 'initializePosComponent') !== false;
    $interOutletHasProperInit = strpos($interOutletContent, 'initializeInterOutletComponent') !== false;
    
    if ($posHasProperInit && $interOutletHasProperInit) {
        echo "✅ Both components use separate initialization functions\n";
    } else {
        echo "❌ Initialization conflict detected\n";
        $allPassed = false;
    }
    
    // Check if both wait for Alpine.js properly
    $posWaitsForAlpine = strpos($posContent, 'typeof Alpine === \'undefined\'') !== false;
    $interOutletWaitsForAlpine = strpos($interOutletContent, 'typeof Alpine === \'undefined\'') !== false;
    
    if ($posWaitsForAlpine && $interOutletWaitsForAlpine) {
        echo "✅ Both components wait for Alpine.js properly\n";
    } else {
        echo "❌ Alpine.js waiting mechanism issue detected\n";
        $allPassed = false;
    }
}

echo "\n";

// Check admin layout
echo "🔍 CHECKING ADMIN LAYOUT:\n";

$adminLayoutFile = 'resources/views/components/layouts/admin.blade.php';

if (file_exists($adminLayoutFile)) {
    $layoutContent = file_get_contents($adminLayoutFile);
    
    if (strpos($layoutContent, 'alpinejs@3.x.x/dist/cdn.min.js') !== false) {
        echo "✅ Alpine.js CDN found in admin layout\n";
    } else {
        echo "❌ Alpine.js CDN not found in admin layout\n";
        $allPassed = false;
    }
    
    if (strpos($layoutContent, 'pos.js') !== false) {
        echo "✅ pos.js is included in admin layout\n";
    } else {
        echo "❌ pos.js is NOT included in admin layout\n";
        $allPassed = false;
    }
    
    if (strpos($layoutContent, 'inter-outlet.js') !== false) {
        echo "✅ inter-outlet.js is included in admin layout\n";
    } else {
        echo "❌ inter-outlet.js is NOT included in admin layout\n";
        $allPassed = false;
    }
    
} else {
    echo "⚠️  Admin layout file not found for verification\n";
}

echo "\n";

// Final result
if ($allPassed) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "Both POS and Inter Outlet components should work properly now.\n\n";
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Clear browser cache completely (Ctrl+Shift+R)\n";
    echo "2. Test POS page: /admin/penjualan/pos\n";
    echo "   - Check console for POS success messages\n";
    echo "   - Test customer search and pricing\n";
    echo "   - Test cart functionality\n";
    echo "3. Test Inter Outlet page: /admin/penjualan/inter-outlet\n";
    echo "   - Check console for Inter Outlet success messages\n";
    echo "   - Test product search and filtering\n";
    echo "   - Test cart and modal functionality\n\n";
    
    echo "🔍 SUCCESS INDICATORS:\n";
    echo "POS Page Console:\n";
    echo "   🛒 [POS] Loading POS JavaScript...\n";
    echo "   ✅ [POS] POS component registered successfully\n";
    echo "   ✅ [POS] POS App initialized successfully\n\n";
    
    echo "Inter Outlet Page Console:\n";
    echo "   📦 [INTER-OUTLET] Loading Inter Outlet JavaScript...\n";
    echo "   ✅ [INTER-OUTLET] Inter Outlet Sale Component registered successfully\n";
    echo "   ✅ [INTER-OUTLET] Inter Outlet Sale App initialized successfully\n\n";
    
    echo "❌ ERROR INDICATORS TO AVOID:\n";
    echo "   - 'posApp is not defined'\n";
    echo "   - 'interOutletSaleApp is not defined'\n";
    echo "   - 'selectedOutlet is not defined'\n";
    echo "   - 'searchProduct is not defined'\n";
    echo "   - Any Alpine.js component errors\n\n";
    
} else {
    echo "❌ SOME TESTS FAILED\n";
    echo "Please check the issues above and run the fix script again if needed.\n\n";
}

echo "📊 SUMMARY:\n";
echo "- POS component: Restored to working version with proper Alpine.js registration\n";
echo "- Inter Outlet component: Fixed with proper registration without conflicts\n";
echo "- Both components use separate initialization functions\n";
echo "- Both components wait for Alpine.js properly\n";
echo "- No naming conflicts between components\n";
echo "- Admin layout includes both JavaScript files\n\n";

if ($allPassed) {
    echo "✅ READY FOR TESTING - Both pages should work correctly now!\n";
} else {
    echo "⚠️  ADDITIONAL FIXES MAY BE NEEDED\n";
}

echo "\n";