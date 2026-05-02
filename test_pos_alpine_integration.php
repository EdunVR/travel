<?php
/**
 * Test script to verify POS Alpine.js integration
 * This script tests if the separate pos.js file is properly loaded and integrated
 */

echo "🧪 Testing POS Alpine.js Integration\n";
echo "=====================================\n\n";

// Check if pos.js file exists
$posJsPath = 'public/js/pos.js';
if (file_exists($posJsPath)) {
    echo "✅ pos.js file exists\n";
    
    // Check file size
    $fileSize = filesize($posJsPath);
    echo "📁 File size: " . number_format($fileSize) . " bytes\n";
    
    // Check if it contains Alpine.data registration
    $content = file_get_contents($posJsPath);
    if (strpos($content, "Alpine.data('posApp'") !== false) {
        echo "✅ Alpine.data registration found\n";
    } else {
        echo "❌ Alpine.data registration NOT found\n";
    }
    
    // Check if it contains customer type pricing functions
    if (strpos($content, 'loadCustomerTypePrices') !== false) {
        echo "✅ Customer type pricing functions found\n";
    } else {
        echo "❌ Customer type pricing functions NOT found\n";
    }
    
} else {
    echo "❌ pos.js file does NOT exist\n";
}

// Check admin layout for pos.js script tag
$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    echo "\n📄 Checking admin layout...\n";
    
    $layoutContent = file_get_contents($adminLayoutPath);
    if (strpos($layoutContent, 'pos.js') !== false) {
        echo "✅ pos.js script tag found in admin layout\n";
    } else {
        echo "❌ pos.js script tag NOT found in admin layout\n";
    }
} else {
    echo "❌ Admin layout file does NOT exist\n";
}

// Check POS blade template
$posTemplatePath = 'resources/views/admin/penjualan/pos/index.blade.php';
if (file_exists($posTemplatePath)) {
    echo "\n📄 Checking POS template...\n";
    
    $templateContent = file_get_contents($posTemplatePath);
    
    // Check if it has window variables setup
    if (strpos($templateContent, 'window.posInitialOutlet') !== false) {
        echo "✅ Window initialization variables found\n";
    } else {
        echo "❌ Window initialization variables NOT found\n";
    }
    
    // Check if it still has inline Alpine.js component (should be removed)
    if (strpos($templateContent, "Alpine.data('posApp'") !== false) {
        echo "⚠️  WARNING: Inline Alpine.js component still exists (should be removed)\n";
    } else {
        echo "✅ Inline Alpine.js component properly removed\n";
    }
    
} else {
    echo "❌ POS template file does NOT exist\n";
}

echo "\n🎯 Integration Status Summary:\n";
echo "==============================\n";

$checks = [
    'pos.js file exists' => file_exists($posJsPath),
    'pos.js has Alpine.data' => file_exists($posJsPath) && strpos(file_get_contents($posJsPath), "Alpine.data('posApp'") !== false,
    'Admin layout includes pos.js' => file_exists($adminLayoutPath) && strpos(file_get_contents($adminLayoutPath), 'pos.js') !== false,
    'POS template has window vars' => file_exists($posTemplatePath) && strpos(file_get_contents($posTemplatePath), 'window.posInitialOutlet') !== false,
];

$allPassed = true;
foreach ($checks as $check => $passed) {
    echo ($passed ? "✅" : "❌") . " $check\n";
    if (!$passed) $allPassed = false;
}

echo "\n" . ($allPassed ? "🎉 All checks passed! Integration should work." : "⚠️  Some checks failed. Manual fixes needed.") . "\n";

echo "\n📋 Next Steps:\n";
echo "1. Clear browser cache\n";
echo "2. Open POS page in browser\n";
echo "3. Check browser console (F12) for Alpine.js errors\n";
echo "4. Test customer type pricing functionality\n";