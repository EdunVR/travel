<?php
/**
 * Test Sparepart JavaScript Loading Order
 * This script tests if the sparepartData function is properly loaded before Alpine.js initializes
 */

echo "🧪 Testing Sparepart JavaScript Loading Order\n";
echo "============================================\n\n";

// Test 1: Check if sparepart.js file exists and is readable
echo "1. Checking sparepart.js file...\n";
$sparepartJsPath = 'public/js/sparepart.js';
if (file_exists($sparepartJsPath)) {
    echo "   ✅ sparepart.js exists\n";
    
    $content = file_get_contents($sparepartJsPath);
    if (strpos($content, 'window.sparepartData = function sparepartData()') !== false) {
        echo "   ✅ sparepartData function is defined globally\n";
    } else {
        echo "   ❌ sparepartData function not found or not global\n";
    }
    
    if (strpos($content, 'console.log(\'✅ sparepartData function defined globally\')') !== false ||
        strpos($content, 'console.log("✅ sparepartData function defined globally")') !== false) {
        echo "   ✅ Global assignment logging is present\n";
    } else {
        echo "   ❌ Global assignment logging missing\n";
    }
} else {
    echo "   ❌ sparepart.js file not found\n";
}

echo "\n";

// Test 2: Check emergency fix
echo "2. Checking emergency fix...\n";
$emergencyFixPath = 'public/js/sparepart-emergency-fix.js';
if (file_exists($emergencyFixPath)) {
    echo "   ✅ sparepart-emergency-fix.js exists\n";
    
    $content = file_get_contents($emergencyFixPath);
    if (strpos($content, 'typeof sparepartData === \'undefined\' && typeof window.sparepartData === \'undefined\'') !== false ||
        strpos($content, 'typeof sparepartData === "undefined" &&') !== false) {
        echo "   ✅ Emergency fix has proper condition check\n";
    } else {
        echo "   ❌ Emergency fix condition check missing or incorrect\n";
    }
} else {
    echo "   ❌ sparepart-emergency-fix.js file not found\n";
}

echo "\n";

// Test 3: Check view file structure
echo "3. Checking view file structure...\n";
$viewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($viewPath)) {
    echo "   ✅ Sparepart view file exists\n";
    
    $content = file_get_contents($viewPath);
    
    // Check if scripts are loaded before @push
    if (strpos($content, '<script src="{{ asset(\'js/sparepart.js\') }}') !== false) {
        $scriptPos = strpos($content, '<script src="{{ asset(\'js/sparepart.js\') }}');
        $pushPos = strpos($content, '@push(\'scripts\')');
        
        if ($scriptPos < $pushPos) {
            echo "   ✅ sparepart.js is loaded before @push('scripts')\n";
        } else {
            echo "   ❌ sparepart.js is loaded after @push('scripts') - this could cause timing issues\n";
        }
    } else {
        echo "   ❌ sparepart.js script tag not found in view\n";
    }
    
    // Check if routes are defined before script loading
    if (strpos($content, 'window.sparepartRoutes = {') !== false) {
        echo "   ✅ sparepartRoutes are defined in view\n";
    } else {
        echo "   ❌ sparepartRoutes not found in view\n";
    }
    
    // Check Alpine.js data attribute
    if (strpos($content, 'x-data="sparepartData()"') !== false) {
        echo "   ✅ Alpine.js x-data attribute uses sparepartData()\n";
    } else {
        echo "   ❌ Alpine.js x-data attribute not found or incorrect\n";
    }
} else {
    echo "   ❌ Sparepart view file not found\n";
}

echo "\n";

// Test 4: Check admin layout Alpine.js loading
echo "4. Checking admin layout Alpine.js loading...\n";
$layoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($layoutPath)) {
    echo "   ✅ Admin layout exists\n";
    
    $content = file_get_contents($layoutPath);
    
    if (strpos($content, 'defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"') !== false) {
        echo "   ✅ Alpine.js is loaded with defer attribute\n";
    } else {
        echo "   ❌ Alpine.js defer loading not found\n";
    }
    
    if (strpos($content, 'window.alpineStarted = true') !== false) {
        echo "   ✅ Alpine.js start control is present\n";
    } else {
        echo "   ❌ Alpine.js start control missing\n";
    }
} else {
    echo "   ❌ Admin layout file not found\n";
}

echo "\n";

// Summary
echo "📋 SUMMARY\n";
echo "==========\n";
echo "The JavaScript loading order should be:\n";
echo "1. jQuery and DataTables (in head)\n";
echo "2. Alpine.js plugins (in head with defer)\n";
echo "3. sparepartRoutes definition (in view, before Alpine.js)\n";
echo "4. sparepart.js (in view, before Alpine.js)\n";
echo "5. sparepart-emergency-fix.js (in view, before Alpine.js)\n";
echo "6. Alpine.js main library (in head with defer - loads last)\n";
echo "7. DOMContentLoaded handlers (in @push scripts)\n\n";

echo "🔧 NEXT STEPS:\n";
echo "1. Clear browser cache completely (Ctrl+F5)\n";
echo "2. Test in incognito/private browsing mode\n";
echo "3. Check browser console for JavaScript errors\n";
echo "4. Verify that sparepartData function is available when Alpine.js initializes\n\n";

echo "✅ Test completed!\n";
?>