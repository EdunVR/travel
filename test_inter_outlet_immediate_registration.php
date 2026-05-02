<?php

/**
 * Test Inter Outlet Immediate Registration Fix
 * This script tests if the Alpine.js component registration is working
 */

echo "🧪 TESTING: Inter Outlet Immediate Registration Fix\n\n";

// Check if the JavaScript file exists and has the immediate registration code
$jsFile = 'public/js/inter-outlet.js';

if (!file_exists($jsFile)) {
    echo "❌ ERROR: inter-outlet.js file not found\n";
    exit(1);
}

$jsContent = file_get_contents($jsFile);

// Check for key immediate registration elements
$checks = [
    'IMMEDIATE REGISTRATION' => 'Inter Outlet Sale Application - IMMEDIATE REGISTRATION',
    'registerInterOutletComponent function' => 'function registerInterOutletComponent()',
    'Alpine.data registration' => "Alpine.data('interOutletSaleApp'",
    'Immediate check' => 'if (typeof Alpine !== \'undefined\')',
    'Multiple registration attempts' => 'let attempts = 0',
    'Global error handler' => 'window.addEventListener(\'error\'',
    'Constants definition' => 'window.ALL = \'all\''
];

$allPassed = true;

foreach ($checks as $name => $pattern) {
    if (strpos($jsContent, $pattern) !== false) {
        echo "✅ $name: Found\n";
    } else {
        echo "❌ $name: Missing\n";
        $allPassed = false;
    }
}

echo "\n";

if ($allPassed) {
    echo "✅ ALL CHECKS PASSED\n";
    echo "The immediate registration fix has been properly applied.\n\n";
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Clear browser cache completely (Ctrl+Shift+R or Ctrl+F5)\n";
    echo "2. Open Developer Tools (F12)\n";
    echo "3. Go to: /admin/penjualan/inter-outlet\n";
    echo "4. Check Console for these messages:\n";
    echo "   📦 [INTER-OUTLET] Starting immediate registration...\n";
    echo "   🎯 [INTER-OUTLET] Alpine.js already available, registering immediately\n";
    echo "   🏪 [INTER-OUTLET] Registering component immediately...\n";
    echo "   ✅ [INTER-OUTLET] Component registered successfully\n";
    echo "5. Verify NO 'interOutletSaleApp is not defined' errors\n\n";
    
    echo "🔍 TROUBLESHOOTING:\n";
    echo "If you still see 'interOutletSaleApp is not defined':\n";
    echo "- Check if Alpine.js is loading properly in admin layout\n";
    echo "- Look for any JavaScript errors that might prevent Alpine.js from loading\n";
    echo "- Verify the script loading order in the admin layout\n";
    echo "- Check browser console for any CDN loading failures\n\n";
    
} else {
    echo "❌ SOME CHECKS FAILED\n";
    echo "The immediate registration fix may not have been applied correctly.\n";
    echo "Please run the fix script again: php fix_inter_outlet_immediate_registration.php\n\n";
}

// Additional check: Verify admin layout has proper Alpine.js loading
$adminLayoutFile = 'resources/views/components/layouts/admin.blade.php';

if (file_exists($adminLayoutFile)) {
    $layoutContent = file_get_contents($adminLayoutFile);
    
    echo "🔍 CHECKING ADMIN LAYOUT:\n";
    
    if (strpos($layoutContent, 'alpinejs@3.x.x/dist/cdn.min.js') !== false) {
        echo "✅ Alpine.js CDN found in admin layout\n";
    } else {
        echo "❌ Alpine.js CDN not found in admin layout\n";
        $allPassed = false;
    }
    
    if (strpos($layoutContent, 'defer') !== false) {
        echo "✅ Defer attribute found (good for loading order)\n";
    } else {
        echo "⚠️  No defer attribute found (may cause loading issues)\n";
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

if ($allPassed) {
    echo "🎉 READY TO TEST!\n";
    echo "The fix should resolve the 'interOutletSaleApp is not defined' error.\n";
} else {
    echo "⚠️  ADDITIONAL FIXES MAY BE NEEDED\n";
    echo "Please address the issues above before testing.\n";
}

echo "\n";