<?php

/**
 * Test Purchase Order Checkbox Filter Implementation
 * 
 * This script tests the complete checkbox filter implementation for Purchase Order page
 */

echo "=== PURCHASE ORDER CHECKBOX FILTER TEST ===\n\n";

// Test 1: Check Frontend HTML Structure
echo "1. Testing Frontend HTML Structure...\n";
$viewFile = 'resources/views/admin/pembelian/purchase-order/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for checkbox filter UI
    $hasCheckboxFilter = strpos($content, 'showOutletDropdown') !== false;
    $hasSelectAll = strpos($content, 'selectAllOutlets()') !== false;
    $hasClearAll = strpos($content, 'clearAllOutlets()') !== false;
    $hasSelectedOutlets = strpos($content, 'selectedOutlets') !== false;
    
    echo "   ✓ Checkbox Filter UI: " . ($hasCheckboxFilter ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Select All Function: " . ($hasSelectAll ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Clear All Function: " . ($hasClearAll ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Selected Outlets Array: " . ($hasSelectedOutlets ? "FOUND" : "MISSING") . "\n";
    
    if ($hasCheckboxFilter && $hasSelectAll && $hasClearAll && $hasSelectedOutlets) {
        echo "   ✅ Frontend HTML Structure: COMPLETE\n";
    } else {
        echo "   ❌ Frontend HTML Structure: INCOMPLETE\n";
    }
} else {
    echo "   ❌ View file not found: $viewFile\n";
}

echo "\n";

// Test 2: Check JavaScript Functions
echo "2. Testing JavaScript Functions...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for required JavaScript functions
    $hasGetSelectedText = strpos($content, 'getSelectedOutletsText()') !== false;
    $hasSelectAllMethod = strpos($content, 'selectAllOutlets()') !== false;
    $hasClearAllMethod = strpos($content, 'clearAllOutlets()') !== false;
    $hasOnChangeMethod = strpos($content, 'onOutletSelectionChange()') !== false;
    $hasInitialization = strpos($content, 'this.selectedOutlets = this.outlets.map') !== false;
    
    echo "   ✓ getSelectedOutletsText(): " . ($hasGetSelectedText ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ selectAllOutlets(): " . ($hasSelectAllMethod ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ clearAllOutlets(): " . ($hasClearAllMethod ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ onOutletSelectionChange(): " . ($hasOnChangeMethod ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Default Select All: " . ($hasInitialization ? "FOUND" : "MISSING") . "\n";
    
    if ($hasGetSelectedText && $hasSelectAllMethod && $hasClearAllMethod && $hasOnChangeMethod && $hasInitialization) {
        echo "   ✅ JavaScript Functions: COMPLETE\n";
    } else {
        echo "   ❌ JavaScript Functions: INCOMPLETE\n";
    }
}

echo "\n";

// Test 3: Check Backend Controller Updates
echo "3. Testing Backend Controller Updates...\n";
$controllerFile = 'app/Http/Controllers/PurchaseManagementController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for multiple outlet support
    $hasOutletIds = strpos($content, 'outlet_ids') !== false;
    $hasWhereIn = strpos($content, 'whereIn(\'id_outlet\', $outletIds)') !== false;
    $hasArrayCheck = strpos($content, 'is_array($outletIds)') !== false;
    
    echo "   ✓ outlet_ids Parameter: " . ($hasOutletIds ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ whereIn Query: " . ($hasWhereIn ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Array Validation: " . ($hasArrayCheck ? "FOUND" : "MISSING") . "\n";
    
    if ($hasOutletIds && $hasWhereIn && $hasArrayCheck) {
        echo "   ✅ Backend Controller: UPDATED\n";
    } else {
        echo "   ❌ Backend Controller: NEEDS UPDATE\n";
    }
} else {
    echo "   ❌ Controller file not found: $controllerFile\n";
}

echo "\n";

// Test 4: Check Data Loading Methods
echo "4. Testing Data Loading Methods...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for updated data loading methods
    $hasLoadPurchaseOrders = strpos($content, 'loadPurchaseOrders()') !== false;
    $hasLoadStats = strpos($content, 'loadStats()') !== false;
    $hasLoadSuppliers = strpos($content, 'loadSuppliers()') !== false;
    $hasOutletIdsParam = strpos($content, 'outlet_ids[]') !== false;
    
    echo "   ✓ loadPurchaseOrders(): " . ($hasLoadPurchaseOrders ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ loadStats(): " . ($hasLoadStats ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ loadSuppliers(): " . ($hasLoadSuppliers ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ outlet_ids[] Parameter: " . ($hasOutletIdsParam ? "FOUND" : "MISSING") . "\n";
    
    if ($hasLoadPurchaseOrders && $hasLoadStats && $hasLoadSuppliers && $hasOutletIdsParam) {
        echo "   ✅ Data Loading Methods: UPDATED\n";
    } else {
        echo "   ❌ Data Loading Methods: NEEDS UPDATE\n";
    }
}

echo "\n";

// Test 5: Check Route Compatibility
echo "5. Testing Route Compatibility...\n";
$routeFile = 'routes/web.php';

if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    // Check for required routes
    $hasPurchaseOrderData = strpos($content, 'purchase-order.data') !== false;
    $hasPurchaseOrderCounts = strpos($content, 'purchase-order.counts') !== false;
    $hasSupplierRoute = strpos($content, 'suppliers') !== false;
    
    echo "   ✓ purchase-order.data Route: " . ($hasPurchaseOrderData ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ purchase-order.counts Route: " . ($hasPurchaseOrderCounts ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Suppliers Route: " . ($hasSupplierRoute ? "FOUND" : "MISSING") . "\n";
    
    if ($hasPurchaseOrderData && $hasPurchaseOrderCounts && $hasSupplierRoute) {
        echo "   ✅ Routes: COMPATIBLE\n";
    } else {
        echo "   ❌ Routes: CHECK REQUIRED\n";
    }
} else {
    echo "   ❌ Route file not found: $routeFile\n";
}

echo "\n";

// Test 6: Implementation Summary
echo "6. Implementation Summary...\n";

$frontendComplete = $hasCheckboxFilter && $hasSelectAll && $hasClearAll && $hasSelectedOutlets;
$jsComplete = $hasGetSelectedText && $hasSelectAllMethod && $hasClearAllMethod && $hasOnChangeMethod && $hasInitialization;
$backendComplete = $hasOutletIds && $hasWhereIn && $hasArrayCheck;
$dataLoadingComplete = $hasLoadPurchaseOrders && $hasLoadStats && $hasLoadSuppliers && $hasOutletIdsParam;
$routesComplete = $hasPurchaseOrderData && $hasPurchaseOrderCounts && $hasSupplierRoute;

$totalTests = 5;
$passedTests = 0;

if ($frontendComplete) $passedTests++;
if ($jsComplete) $passedTests++;
if ($backendComplete) $passedTests++;
if ($dataLoadingComplete) $passedTests++;
if ($routesComplete) $passedTests++;

$percentage = ($passedTests / $totalTests) * 100;

echo "   📊 Implementation Progress: $passedTests/$totalTests tests passed ($percentage%)\n";

if ($percentage == 100) {
    echo "   🎉 IMPLEMENTATION COMPLETE! Ready for testing.\n";
} elseif ($percentage >= 80) {
    echo "   ⚠️  MOSTLY COMPLETE. Minor fixes needed.\n";
} elseif ($percentage >= 60) {
    echo "   🔧 PARTIALLY COMPLETE. More work required.\n";
} else {
    echo "   ❌ INCOMPLETE. Significant work needed.\n";
}

echo "\n";

// Test 7: Next Steps
echo "7. Next Steps...\n";

if ($percentage == 100) {
    echo "   1. ✅ Clear browser cache (Ctrl+F5)\n";
    echo "   2. ✅ Test checkbox functionality in browser\n";
    echo "   3. ✅ Verify data filtering works correctly\n";
    echo "   4. ✅ Test select all/clear all functions\n";
    echo "   5. ✅ Verify no JavaScript errors in console\n";
} else {
    echo "   1. 🔧 Complete missing implementation parts\n";
    echo "   2. 🔧 Update backend controller methods\n";
    echo "   3. 🔧 Fix JavaScript function definitions\n";
    echo "   4. 🔧 Test implementation after fixes\n";
}

echo "\n=== TEST COMPLETE ===\n";

?>