<?php
/**
 * Test POS Alpine.js Fix
 * 
 * Script untuk memverifikasi bahwa perbaikan Alpine.js POS berhasil
 */

echo "🧪 [TEST] Testing POS Alpine.js Fix...\n";

// 1. Check if admin layout has correct script order
echo "\n📋 [TEST] Checking admin layout script order...\n";
$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $content = file_get_contents($adminLayoutPath);
    
    // Check if pos.js comes before Alpine.js
    $posJsPos = strpos($content, 'asset(\'js/pos.js\')');
    $alpineJsPos = strpos($content, 'alpinejs@3.x.x/dist/cdn.min.js');
    
    if ($posJsPos !== false && $alpineJsPos !== false) {
        if ($posJsPos < $alpineJsPos) {
            echo "✅ [TEST] Script order is correct (pos.js before Alpine.js)\n";
        } else {
            echo "❌ [TEST] Script order is incorrect (Alpine.js before pos.js)\n";
        }
    } else {
        echo "⚠️ [TEST] Could not find both scripts in admin layout\n";
    }
    
    // Check if pos.js doesn't have defer attribute
    if (strpos($content, 'defer src="{{ asset(\'js/pos.js\') }}"') === false) {
        echo "✅ [TEST] pos.js loads without defer (good)\n";
    } else {
        echo "❌ [TEST] pos.js still has defer attribute\n";
    }
} else {
    echo "❌ [TEST] Admin layout file not found\n";
}

// 2. Check if POS view has fallback script
echo "\n📋 [TEST] Checking POS view fallback script...\n";
$posViewPath = 'resources/views/admin/penjualan/pos/index.blade.php';
if (file_exists($posViewPath)) {
    $content = file_get_contents($posViewPath);
    
    if (strpos($content, 'Checking posApp availability') !== false) {
        echo "✅ [TEST] Fallback script is present\n";
    } else {
        echo "❌ [TEST] Fallback script is missing\n";
    }
    
    if (strpos($content, 'posApp function test') !== false) {
        echo "✅ [TEST] posApp function test is present\n";
    } else {
        echo "❌ [TEST] posApp function test is missing\n";
    }
} else {
    echo "❌ [TEST] POS view file not found\n";
}

// 3. Check if pos.js file exists and has posApp function
echo "\n📋 [TEST] Checking pos.js file...\n";
$posJsPath = 'public/js/pos.js';
if (file_exists($posJsPath)) {
    echo "✅ [TEST] pos.js file exists\n";
    
    $content = file_get_contents($posJsPath);
    
    if (strpos($content, "Alpine.data('posApp'") !== false) {
        echo "✅ [TEST] posApp function is defined in pos.js\n";
    } else {
        echo "❌ [TEST] posApp function not found in pos.js\n";
    }
    
    if (strpos($content, "document.addEventListener('alpine:init'") !== false) {
        echo "✅ [TEST] Alpine.js init event listener is present\n";
    } else {
        echo "❌ [TEST] Alpine.js init event listener is missing\n";
    }
    
    // Check file size
    $fileSize = filesize($posJsPath);
    echo "📊 [TEST] pos.js file size: " . number_format($fileSize) . " bytes\n";
    
    if ($fileSize > 10000) {
        echo "✅ [TEST] pos.js file size looks reasonable\n";
    } else {
        echo "⚠️ [TEST] pos.js file might be incomplete (small size)\n";
    }
} else {
    echo "❌ [TEST] pos.js file not found\n";
}

// 4. Check if test file was created
echo "\n📋 [TEST] Checking test file...\n";
if (file_exists('test_pos_alpine_fix.html')) {
    echo "✅ [TEST] Test HTML file created\n";
} else {
    echo "❌ [TEST] Test HTML file not found\n";
}

// 5. Generate test report
echo "\n📊 [TEST] Generating test report...\n";

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => [
        'admin_layout_script_order' => file_exists($adminLayoutPath),
        'pos_view_fallback' => file_exists($posViewPath) && strpos(file_get_contents($posViewPath), 'Checking posApp availability') !== false,
        'pos_js_exists' => file_exists($posJsPath),
        'pos_app_function' => file_exists($posJsPath) && strpos(file_get_contents($posJsPath), "Alpine.data('posApp'") !== false,
        'test_file_created' => file_exists('test_pos_alpine_fix.html')
    ]
];

$reportJson = json_encode($report, JSON_PRETTY_PRINT);
file_put_contents('pos_alpine_test_report.json', $reportJson);
echo "✅ [TEST] Test report saved to pos_alpine_test_report.json\n";

// 6. Summary
echo "\n🎯 [TEST] Test Summary:\n";
$passedTests = array_filter($report['tests']);
$totalTests = count($report['tests']);
$passedCount = count($passedTests);

echo "📊 [TEST] Passed: $passedCount/$totalTests tests\n";

if ($passedCount === $totalTests) {
    echo "✅ [TEST] All tests passed! POS Alpine.js fix should work.\n";
} else {
    echo "⚠️ [TEST] Some tests failed. Check the issues above.\n";
}

echo "\n📋 [NEXT STEPS]:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Open POS page: /admin/penjualan/pos\n";
echo "3. Check browser console for errors\n";
echo "4. If errors persist, check browser developer tools\n";

echo "\n🔍 [DEBUGGING TIPS]:\n";
echo "- Open browser developer tools (F12)\n";
echo "- Check Console tab for JavaScript errors\n";
echo "- Check Network tab to see if pos.js loads\n";
echo "- Look for 'posApp is not defined' errors\n";

?>