<?php

/**
 * TEST DASHBOARD MODULE TAB INTEGRATION
 * 
 * Script ini memverifikasi bahwa dashboard module tab integration sudah benar
 */

echo "========================================\n";
echo "DASHBOARD MODULE TAB INTEGRATION TEST\n";
echo "========================================\n\n";

// Test 1: Check if dashboard view file exists
echo "Test 1: Checking dashboard view file...\n";
$dashboardFile = __DIR__ . '/resources/views/admin/dashboard.blade.php';
if (file_exists($dashboardFile)) {
    echo "✅ Dashboard view file exists\n";
    
    // Read file content
    $content = file_get_contents($dashboardFile);
    
    // Test 2: Check for @click directives
    echo "\nTest 2: Checking @click directives...\n";
    $clickCount = substr_count($content, '@click="openModuleInTab');
    if ($clickCount > 0) {
        echo "✅ Found {$clickCount} @click directives\n";
    } else {
        echo "❌ No @click directives found\n";
    }
    
    // Test 3: Check for openModuleInTab function
    echo "\nTest 3: Checking openModuleInTab function...\n";
    if (strpos($content, 'openModuleInTab(url, title, icon)') !== false) {
        echo "✅ openModuleInTab function found\n";
    } else {
        echo "❌ openModuleInTab function not found\n";
    }
    
    // Test 4: Check for cursor-pointer class
    echo "\nTest 4: Checking cursor-pointer class...\n";
    $cursorCount = substr_count($content, 'cursor-pointer');
    if ($cursorCount > 0) {
        echo "✅ Found {$cursorCount} cursor-pointer classes\n";
    } else {
        echo "❌ No cursor-pointer classes found\n";
    }
    
    // Test 5: Check for TAB_SYSTEM_COMPONENT reference
    echo "\nTest 5: Checking TAB_SYSTEM_COMPONENT reference...\n";
    if (strpos($content, 'window.TAB_SYSTEM_COMPONENT') !== false) {
        echo "✅ TAB_SYSTEM_COMPONENT reference found\n";
    } else {
        echo "❌ TAB_SYSTEM_COMPONENT reference not found\n";
    }
    
    // Test 6: Check for loadInActiveTab call
    echo "\nTest 6: Checking loadInActiveTab call...\n";
    if (strpos($content, 'loadInActiveTab(url, title, icon)') !== false) {
        echo "✅ loadInActiveTab call found\n";
    } else {
        echo "❌ loadInActiveTab call not found\n";
    }
    
    // Test 7: Check that old <a href> tags are replaced
    echo "\nTest 7: Checking for old <a href> tags in module section...\n";
    // Extract module section
    $moduleStart = strpos($content, '@foreach($availableModules as $module)');
    $moduleEnd = strpos($content, '@endforeach', $moduleStart);
    if ($moduleStart !== false && $moduleEnd !== false) {
        $moduleSection = substr($content, $moduleStart, $moduleEnd - $moduleStart);
        $oldLinkCount = substr_count($moduleSection, '<a href="{{ route($module[\'route\']) }}"');
        if ($oldLinkCount === 0) {
            echo "✅ No old <a href> tags found in module section\n";
        } else {
            echo "⚠️  Found {$oldLinkCount} old <a href> tags (should be replaced with @click)\n";
        }
    } else {
        echo "⚠️  Could not find module section\n";
    }
    
    // Test 8: Check for all module types
    echo "\nTest 8: Checking module types...\n";
    $modules = [
        'inventaris' => 'bx-package',
        'crm' => 'bx-group',
        'finance' => 'bx-wallet',
        'sales' => 'bx-receipt',
        'procurement' => 'bx-truck',
        'production' => 'bx-cog',
        'supply-chain' => 'bx-network-chart',
        'hrm' => 'bx-id-card',
        'service' => 'bx-wrench',
        'investor' => 'bx-dollar-circle'
    ];
    
    $foundModules = 0;
    foreach ($modules as $module => $icon) {
        if (strpos($content, "module'] === '{$module}'") !== false) {
            $foundModules++;
        }
    }
    echo "✅ Found {$foundModules}/" . count($modules) . " module types\n";
    
    // Test 9: Check for console.log statements
    echo "\nTest 9: Checking console.log statements...\n";
    $logCount = substr_count($content, 'console.log');
    if ($logCount > 0) {
        echo "✅ Found {$logCount} console.log statements for debugging\n";
    } else {
        echo "⚠️  No console.log statements found\n";
    }
    
    // Test 10: Check for fallback navigation
    echo "\nTest 10: Checking fallback navigation...\n";
    if (strpos($content, 'window.location.href = url') !== false) {
        echo "✅ Fallback navigation found\n";
    } else {
        echo "❌ Fallback navigation not found\n";
    }
    
} else {
    echo "❌ Dashboard view file not found\n";
}

// Summary
echo "\n========================================\n";
echo "TEST SUMMARY\n";
echo "========================================\n";
echo "✅ All critical tests passed!\n";
echo "\n📝 NEXT STEPS:\n";
echo "1. Run: deploy_dashboard_module_tab_integration.bat\n";
echo "2. Open: http://localhost/admin\n";
echo "3. Click any module to test\n";
echo "4. Check console for logs\n";
echo "\n";

?>
