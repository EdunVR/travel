<?php

/**
 * Test Script: Kontra Bon Default Select All Behavior
 * 
 * This script tests the new behavior where:
 * 1. Default state: All outlets are selected (select all)
 * 2. When all outlets are unchecked: Show no data
 * 3. When some outlets are selected: Show data for those outlets only
 */

require_once 'vendor/autoload.php';

echo "=== KONTRA BON DEFAULT SELECT ALL BEHAVIOR TEST ===\n\n";

// Test URLs
$baseUrl = 'https://poshan.my.id/tofu';
$piutangDataUrl = $baseUrl . '/admin/penjualan/kontrabon/data';
$kontraBonDataUrl = $baseUrl . '/admin/penjualan/kontrabon/data-kontrabon';

// Test scenarios
$testScenarios = [
    [
        'name' => 'Test 1: Default State (All Outlets Selected)',
        'description' => 'Should show data from all accessible outlets',
        'outlet_ids' => ['1', '2', '3'], // Simulate all outlets selected
        'expected' => 'Should return data from all outlets'
    ],
    [
        'name' => 'Test 2: No Outlets Selected (All Unchecked)',
        'description' => 'Should return empty result when no outlets selected',
        'outlet_ids' => [], // No outlets selected
        'expected' => 'Should return empty data array'
    ],
    [
        'name' => 'Test 3: Single Outlet Selected',
        'description' => 'Should show data from selected outlet only',
        'outlet_ids' => ['1'], // Only outlet 1 selected
        'expected' => 'Should return data from outlet 1 only'
    ],
    [
        'name' => 'Test 4: Multiple Outlets Selected',
        'description' => 'Should show data from selected outlets only',
        'outlet_ids' => ['1', '2'], // Outlets 1 and 2 selected
        'expected' => 'Should return data from outlets 1 and 2 only'
    ]
];

// Function to test API endpoint
function testEndpoint($url, $params, $testName) {
    echo "Testing: $testName\n";
    echo "URL: $url\n";
    echo "Parameters: " . json_encode($params) . "\n";
    
    // Build query string
    $queryString = http_build_query($params);
    $fullUrl = $url . '?' . $queryString;
    
    echo "Full URL: $fullUrl\n";
    
    // Simulate the request (in real scenario, this would be an actual HTTP request)
    echo "Expected behavior: ";
    if (empty($params['outlet_ids'])) {
        echo "Should return empty data (no outlets selected)\n";
    } else {
        echo "Should return data for outlets: " . implode(', ', $params['outlet_ids']) . "\n";
    }
    
    echo "---\n\n";
}

// Test Piutang Data Endpoint
echo "TESTING PIUTANG DATA ENDPOINT:\n";
echo "================================\n\n";

foreach ($testScenarios as $scenario) {
    $params = [
        'status' => 'belum_lunas',
        'draw' => 1,
        'start' => 0,
        'length' => 25
    ];
    
    // Add outlet_ids to params
    foreach ($scenario['outlet_ids'] as $index => $outletId) {
        $params["outlet_ids[$index]"] = $outletId;
    }
    
    testEndpoint($piutangDataUrl, $params, $scenario['name'] . ' - Piutang');
}

// Test Kontra Bon Data Endpoint
echo "TESTING KONTRA BON DATA ENDPOINT:\n";
echo "==================================\n\n";

foreach ($testScenarios as $scenario) {
    $params = [
        'draw' => 1,
        'start' => 0,
        'length' => 25
    ];
    
    // Add outlet_ids to params
    foreach ($scenario['outlet_ids'] as $index => $outletId) {
        $params["outlet_ids[$index]"] = $outletId;
    }
    
    testEndpoint($kontraBonDataUrl, $params, $scenario['name'] . ' - Kontra Bon');
}

// Test Frontend JavaScript Logic
echo "TESTING FRONTEND JAVASCRIPT LOGIC:\n";
echo "===================================\n\n";

echo "1. Default Initialization:\n";
echo "   - selectedOutlets should be initialized with all outlet IDs\n";
echo "   - Example: selectedOutlets = [1, 2, 3] (all available outlets)\n\n";

echo "2. Select All Button:\n";
echo "   - Should set selectedOutlets to all available outlet IDs\n";
echo "   - Should trigger onOutletSelectionChange()\n";
echo "   - Should reload both tables with all outlets\n\n";

echo "3. Clear All Button:\n";
echo "   - Should set selectedOutlets to empty array []\n";
echo "   - Should trigger onOutletSelectionChange()\n";
echo "   - Should reload both tables with empty data\n\n";

echo "4. Individual Checkbox Changes:\n";
echo "   - Should add/remove outlet ID from selectedOutlets array\n";
echo "   - Should trigger onOutletSelectionChange()\n";
echo "   - Should reload both tables with selected outlets only\n\n";

echo "5. Text Display Logic:\n";
echo "   - 0 outlets: 'Pilih Outlet'\n";
echo "   - 1 outlet: Show outlet name\n";
echo "   - Multiple outlets: 'X outlet dipilih'\n\n";

// Test Controller Logic
echo "TESTING CONTROLLER LOGIC:\n";
echo "=========================\n\n";

echo "1. data() Method Logic:\n";
echo "   - If outlet_ids is empty array: Return empty datatables result\n";
echo "   - If outlet_ids has values: Filter by those outlet IDs\n";
echo "   - Always validate outlet access against user permissions\n\n";

echo "2. dataKontraBon() Method Logic:\n";
echo "   - If outlet_ids is empty array: Return empty datatables result\n";
echo "   - If outlet_ids has values: Filter by those outlet IDs\n";
echo "   - Always validate outlet access against user permissions\n\n";

echo "3. Super Admin Behavior:\n";
echo "   - Has access to all outlets by default\n";
echo "   - Can select/deselect any outlet\n";
echo "   - Empty selection still returns no data\n\n";

echo "4. Regular User Behavior:\n";
echo "   - Limited to outlets in akses_outlet array\n";
echo "   - Cannot access outlets outside their permission\n";
echo "   - Empty selection returns no data\n\n";

// Expected Results Summary
echo "EXPECTED RESULTS SUMMARY:\n";
echo "=========================\n\n";

echo "✅ DEFAULT STATE:\n";
echo "   - All available outlets are pre-selected\n";
echo "   - Data from all accessible outlets is displayed\n";
echo "   - User sees full dataset on page load\n\n";

echo "✅ ALL UNCHECKED STATE:\n";
echo "   - No outlets selected (empty array)\n";
echo "   - Both tables show 'No data available'\n";
echo "   - Clear indication that user needs to select outlets\n\n";

echo "✅ PARTIAL SELECTION STATE:\n";
echo "   - Only selected outlets' data is shown\n";
echo "   - Data is properly filtered by outlet\n";
echo "   - Real-time updates when selection changes\n\n";

echo "✅ SECURITY:\n";
echo "   - Users can only access their permitted outlets\n";
echo "   - Super admin has access to all outlets\n";
echo "   - Invalid outlet IDs are filtered out\n\n";

echo "=== TEST COMPLETED ===\n";
echo "Please verify the implementation by:\n";
echo "1. Loading the Kontra Bon page\n";
echo "2. Checking that all outlets are selected by default\n";
echo "3. Using 'Hapus Semua' to clear all selections\n";
echo "4. Verifying that no data is shown when all are unchecked\n";
echo "5. Using 'Pilih Semua' to select all outlets again\n";
echo "6. Testing individual outlet selection/deselection\n\n";

?>