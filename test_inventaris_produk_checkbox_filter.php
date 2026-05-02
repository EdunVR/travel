<?php

/**
 * Test script for Inventaris Produk Checkbox Filter Implementation
 * 
 * This script tests the new checkbox-based outlet filtering system
 * for the inventaris produk page.
 */

echo "=== INVENTARIS PRODUK CHECKBOX FILTER TEST ===\n\n";

// Test 1: Check if the view file has been updated
echo "1. Testing view file updates...\n";
$viewFile = 'resources/views/admin/inventaris/produk/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for checkbox UI elements
    $hasCheckboxUI = strpos($content, 'showOutletDropdown') !== false;
    $hasSelectAllButton = strpos($content, 'selectAllOutlets()') !== false;
    $hasClearAllButton = strpos($content, 'clearAllOutlets()') !== false;
    $hasCheckboxInput = strpos($content, 'x-model="selectedOutlets"') !== false;
    
    echo "   ✓ Checkbox UI elements: " . ($hasCheckboxUI ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Select All button: " . ($hasSelectAllButton ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Clear All button: " . ($hasClearAllButton ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Checkbox input: " . ($hasCheckboxInput ? "FOUND" : "MISSING") . "\n";
    
    // Check for JavaScript functions
    $hasGetSelectedText = strpos($content, 'getSelectedOutletsText()') !== false;
    $hasOnSelectionChange = strpos($content, 'onOutletSelectionChange()') !== false;
    $hasSelectedOutletsArray = strpos($content, 'selectedOutlets: []') !== false;
    
    echo "   ✓ getSelectedOutletsText function: " . ($hasGetSelectedText ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ onOutletSelectionChange function: " . ($hasOnSelectionChange ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ selectedOutlets array: " . ($hasSelectedOutletsArray ? "FOUND" : "MISSING") . "\n";
    
    // Check if old dropdown is removed
    $hasOldDropdown = strpos($content, 'Semua Outlet') !== false;
    echo "   ✓ Old dropdown removed: " . ($hasOldDropdown ? "STILL EXISTS (needs cleanup)" : "REMOVED") . "\n";
    
} else {
    echo "   ✗ View file not found: $viewFile\n";
}

echo "\n";

// Test 2: Check controller updates
echo "2. Testing controller updates...\n";
$controllerFile = 'app/Http/Controllers/ProdukController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for new outlet_ids parameter support
    $hasOutletIdsSupport = strpos($content, 'outlet_ids') !== false;
    $hasArrayCheck = strpos($content, 'is_array($request->outlet_ids)') !== false;
    $hasWhereInClause = strpos($content, 'whereIn(\'nama_outlet\'') !== false;
    
    echo "   ✓ outlet_ids parameter support: " . ($hasOutletIdsSupport ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ Array validation: " . ($hasArrayCheck ? "FOUND" : "MISSING") . "\n";
    echo "   ✓ whereIn clause: " . ($hasWhereInClause ? "FOUND" : "MISSING") . "\n";
    
    // Check if HasOutletFilter trait is used
    $hasOutletFilterTrait = strpos($content, 'use HasOutletFilter') !== false;
    echo "   ✓ HasOutletFilter trait: " . ($hasOutletFilterTrait ? "FOUND" : "MISSING") . "\n";
    
} else {
    echo "   ✗ Controller file not found: $controllerFile\n";
}

echo "\n";

// Test 3: Implementation completeness check
echo "3. Implementation completeness check...\n";

$implementationScore = 0;
$totalChecks = 10;

// View file checks (6 points)
if (isset($hasCheckboxUI) && $hasCheckboxUI) $implementationScore++;
if (isset($hasSelectAllButton) && $hasSelectAllButton) $implementationScore++;
if (isset($hasClearAllButton) && $hasClearAllButton) $implementationScore++;
if (isset($hasCheckboxInput) && $hasCheckboxInput) $implementationScore++;
if (isset($hasGetSelectedText) && $hasGetSelectedText) $implementationScore++;
if (isset($hasOnSelectionChange) && $hasOnSelectionChange) $implementationScore++;

// Controller checks (4 points)
if (isset($hasOutletIdsSupport) && $hasOutletIdsSupport) $implementationScore++;
if (isset($hasArrayCheck) && $hasArrayCheck) $implementationScore++;
if (isset($hasWhereInClause) && $hasWhereInClause) $implementationScore++;
if (isset($hasOutletFilterTrait) && $hasOutletFilterTrait) $implementationScore++;

$percentage = ($implementationScore / $totalChecks) * 100;

echo "   Implementation Score: $implementationScore/$totalChecks ($percentage%)\n";

if ($percentage >= 90) {
    echo "   Status: ✅ EXCELLENT - Ready for testing\n";
} elseif ($percentage >= 70) {
    echo "   Status: ⚠️  GOOD - Minor issues to fix\n";
} elseif ($percentage >= 50) {
    echo "   Status: ⚠️  PARTIAL - Significant work needed\n";
} else {
    echo "   Status: ❌ INCOMPLETE - Major implementation missing\n";
}

echo "\n";

// Test 4: Next steps
echo "4. Next steps:\n";
echo "   1. Test the page in browser: /admin/inventaris/produk\n";
echo "   2. Verify checkbox functionality works\n";
echo "   3. Test multi-outlet selection and data filtering\n";
echo "   4. Check for any JavaScript errors in console\n";
echo "   5. Verify outlet access control still works\n";
echo "   6. Test with different user roles (super_admin vs regular user)\n";

echo "\n=== TEST COMPLETE ===\n";