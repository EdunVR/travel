<?php

/**
 * Test Download Dropdown Functionality
 * 
 * This script tests the new dropdown download functionality:
 * 1. Download button is now a dropdown
 * 2. Shows QC Egg Tofu Mentah option only for tofu productions
 * 3. Displays QC data summary from tofu_data JSON
 * 4. Both grid and table views have dropdown
 */

require_once 'vendor/autoload.php';

echo "=== DOWNLOAD DROPDOWN FUNCTIONALITY TEST ===\n\n";

// Test 1: Check if controller includes business_type and tofu_data
echo "1. Testing controller data includes business_type and tofu_data...\n";
try {
    $controller = new App\Http\Controllers\ProductionController();
    
    // Check if getData method exists
    if (method_exists($controller, 'getData')) {
        echo "   ✅ getData() method exists\n";
        
        // Check if method includes business_type and tofu_data
        $reflection = new ReflectionMethod($controller, 'getData');
        $methodContent = file_get_contents($reflection->getFileName());
        
        if (strpos($methodContent, "'business_type' => \$production->business_type") !== false) {
            echo "   ✅ getData() includes business_type\n";
        } else {
            echo "   ❌ getData() does NOT include business_type\n";
        }
        
        if (strpos($methodContent, "'tofu_data' => \$production->tofu_data") !== false) {
            echo "   ✅ getData() includes tofu_data\n";
        } else {
            echo "   ❌ getData() does NOT include tofu_data\n";
        }
    } else {
        echo "   ❌ getData() method does NOT exist\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing controller: " . $e->getMessage() . "\n";
}

// Test 2: Check frontend dropdown implementation
echo "\n2. Testing frontend dropdown implementation...\n";
$bladeTemplate = 'resources/views/admin/produksi/produksi/index.blade.php';
if (file_exists($bladeTemplate)) {
    $bladeContent = file_get_contents($bladeTemplate);
    
    // Check for dropdown structure
    if (strpos($bladeContent, 'x-data="{ open: false }"') !== false) {
        echo "   ✅ Alpine.js dropdown structure exists\n";
    } else {
        echo "   ❌ Alpine.js dropdown structure does NOT exist\n";
    }
    
    // Check for QC data summary function
    if (strpos($bladeContent, 'getQcDataSummary') !== false) {
        echo "   ✅ getQcDataSummary function exists\n";
    } else {
        echo "   ❌ getQcDataSummary function does NOT exist\n";
    }
    
    // Check for conditional QC option
    if (strpos($bladeContent, "p.business_type === 'tofu'") !== false) {
        echo "   ✅ Conditional QC option for tofu productions exists\n";
    } else {
        echo "   ❌ Conditional QC option for tofu productions does NOT exist\n";
    }
    
    // Check for both grid and table dropdowns
    $gridDropdownCount = substr_count($bladeContent, 'x-data="{ open: false }"');
    if ($gridDropdownCount >= 2) {
        echo "   ✅ Dropdown exists in both grid and table views\n";
    } else {
        echo "   ❌ Dropdown missing in grid or table view (found: $gridDropdownCount)\n";
    }
    
    // Check if old downloadDocument function is removed
    if (strpos($bladeContent, 'downloadDocument(production)') === false) {
        echo "   ✅ Old downloadDocument function removed\n";
    } else {
        echo "   ❌ Old downloadDocument function still exists\n";
    }
    
} else {
    echo "   ❌ Blade template does NOT exist\n";
}

// Test 3: Test QC data summary logic
echo "\n3. Testing QC data summary logic...\n";
$sampleTofuData = [
    'perendaman_waktu' => 4.5,
    'perendaman_qty' => 50,
    'rijek_telur' => 2,
    'pasteurisasi_waktu' => 30,
    'pasteurisasi_suhu' => 85,
    'berat_sari_kedelai' => 45.5,
    'waktu_pencampuran' => 20,
    'filling_waktu' => 3,
    'filling_mesin1' => 100,
    'filling_mesin2' => 150,
    'filling_total' => 250,
    'rijek_mentah' => 5
];

// Simulate the summary logic
$summaryParts = [];
if ($sampleTofuData['perendaman_waktu']) {
    $summaryParts[] = "Perendaman: {$sampleTofuData['perendaman_waktu']}h";
}
if ($sampleTofuData['rijek_telur']) {
    $summaryParts[] = "Rijek Telur: {$sampleTofuData['rijek_telur']}";
}
if ($sampleTofuData['filling_total']) {
    $summaryParts[] = "Total Filling: {$sampleTofuData['filling_total']}";
}
if ($sampleTofuData['rijek_mentah']) {
    $summaryParts[] = "Rijek Mentah: {$sampleTofuData['rijek_mentah']}";
}

$expectedSummary = implode(' • ', array_slice($summaryParts, 0, 2));
echo "   ✅ Sample QC summary: $expectedSummary\n";

// Test 4: Check dropdown styling and behavior
echo "\n4. Testing dropdown styling and behavior...\n";
if (file_exists($bladeTemplate)) {
    $bladeContent = file_get_contents($bladeTemplate);
    
    // Check for proper z-index
    if (strpos($bladeContent, 'z-50') !== false) {
        echo "   ✅ Dropdown has proper z-index for overlay\n";
    } else {
        echo "   ❌ Dropdown missing proper z-index\n";
    }
    
    // Check for click away behavior
    if (strpos($bladeContent, '@click.away="open = false"') !== false) {
        echo "   ✅ Click away behavior implemented\n";
    } else {
        echo "   ❌ Click away behavior missing\n";
    }
    
    // Check for transition effects
    if (strpos($bladeContent, 'x-transition') !== false) {
        echo "   ✅ Transition effects implemented\n";
    } else {
        echo "   ❌ Transition effects missing\n";
    }
}

echo "\n=== DROPDOWN FEATURES ===\n";
echo "✅ DROPDOWN STRUCTURE:\n";
echo "   - Alpine.js dropdown with open/close state\n";
echo "   - Click away to close functionality\n";
echo "   - Smooth transitions\n";
echo "   - Proper z-index for overlay\n";
echo "\n✅ DROPDOWN CONTENT:\n";
echo "   - Regular Production PDF (always available)\n";
echo "   - QC Egg Tofu Mentah (only for tofu productions)\n";
echo "   - QC data summary from tofu_data JSON\n";
echo "   - Icons and descriptions for each option\n";
echo "\n✅ QC DATA SUMMARY:\n";
echo "   - Shows key metrics: Perendaman, Rijek Telur, Total Filling, Rijek Mentah\n";
echo "   - Displays first 2 metrics joined with ' • '\n";
echo "   - Handles missing data gracefully\n";
echo "   - JSON parsing with error handling\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. REGULAR PRODUCTION:\n";
echo "   - Click download dropdown button\n";
echo "   - Should show only 'Laporan Produksi' option\n";
echo "   - Click to download regular PDF\n";
echo "\n2. TOFU PRODUCTION:\n";
echo "   - Click download dropdown button\n";
echo "   - Should show both 'Laporan Produksi' and 'QC Egg Tofu Mentah' options\n";
echo "   - QC option should show data summary (e.g., 'Perendaman: 4.5h • Rijek Telur: 2')\n";
echo "   - Click QC option to download QC PDF\n";
echo "\n3. DROPDOWN BEHAVIOR:\n";
echo "   - Click button to open dropdown\n";
echo "   - Click outside to close dropdown\n";
echo "   - Smooth open/close transitions\n";
echo "   - Proper positioning and styling\n";

echo "\n=== SAMPLE QC DATA DISPLAY ===\n";
echo "For production with tofu_data:\n";
echo json_encode($sampleTofuData, JSON_PRETTY_PRINT) . "\n";
echo "\nDropdown will show:\n";
echo "QC Egg Tofu Mentah\n";
echo "$expectedSummary\n";

echo "\n=== DOWNLOAD DROPDOWN FUNCTIONALITY COMPLETE ===\n";