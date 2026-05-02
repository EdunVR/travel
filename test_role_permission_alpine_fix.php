<?php
/**
 * Test Role & Permission Alpine.js Fix
 * 
 * This script tests if the role management functionality works correctly
 * after fixing the Alpine.js conflicts.
 */

echo "=== ROLE & PERMISSION ALPINE.JS FIX TEST ===\n\n";

// Check if roles.js file exists
$rolesJsPath = __DIR__ . '/public/js/roles.js';
if (file_exists($rolesJsPath)) {
    echo "✅ roles.js file exists\n";
    
    // Check if the file contains the roleManagement function
    $content = file_get_contents($rolesJsPath);
    if (strpos($content, 'window.roleManagement') !== false) {
        echo "✅ roleManagement function found in roles.js\n";
    } else {
        echo "❌ roleManagement function NOT found in roles.js\n";
    }
    
    if (strpos($content, 'jQuery handlers') !== false) {
        echo "✅ jQuery handlers found in roles.js\n";
    } else {
        echo "❌ jQuery handlers NOT found in roles.js\n";
    }
} else {
    echo "❌ roles.js file does NOT exist\n";
}

// Check roles index view
$rolesIndexPath = __DIR__ . '/resources/views/admin/user-management/roles/index.blade.php';
if (file_exists($rolesIndexPath)) {
    echo "✅ roles index view exists\n";
    
    $content = file_get_contents($rolesIndexPath);
    
    // Should NOT contain inline roleManagement function
    if (strpos($content, 'function roleManagement()') === false) {
        echo "✅ Inline roleManagement function removed from view\n";
    } else {
        echo "❌ Inline roleManagement function still exists in view\n";
    }
    
    // Should contain roles.js script reference
    if (strpos($content, 'roles.js') !== false) {
        echo "✅ roles.js script reference found in view\n";
    } else {
        echo "❌ roles.js script reference NOT found in view\n";
    }
    
    // Should contain global data variables
    if (strpos($content, 'window.rolesData') !== false) {
        echo "✅ Global data variables found in view\n";
    } else {
        echo "❌ Global data variables NOT found in view\n";
    }
} else {
    echo "❌ roles index view does NOT exist\n";
}

// Check roles modal
$rolesModalPath = __DIR__ . '/resources/views/admin/user-management/roles/modal.blade.php';
if (file_exists($rolesModalPath)) {
    echo "✅ roles modal view exists\n";
    
    $content = file_get_contents($rolesModalPath);
    
    // Should NOT contain duplicate jQuery handlers
    if (strpos($content, '$(document).on(\'click\', \'.select-module\'') === false) {
        echo "✅ Duplicate jQuery handlers removed from modal\n";
    } else {
        echo "❌ Duplicate jQuery handlers still exist in modal\n";
    }
} else {
    echo "❌ roles modal view does NOT exist\n";
}

echo "\n=== TEST SUMMARY ===\n";
echo "The fix separates Alpine.js functionality from jQuery handlers\n";
echo "to prevent conflicts similar to the sparepart issue.\n\n";

echo "MANUAL TESTING REQUIRED:\n";
echo "1. Navigate to Role & Permission page\n";
echo "2. Check browser console for Alpine.js errors\n";
echo "3. Test role creation, editing, and deletion\n";
echo "4. Verify permission checkboxes work correctly\n\n";

echo "If you see 'roleManagement is not defined' errors,\n";
echo "clear browser cache and check network tab for roles.js loading.\n";
?>