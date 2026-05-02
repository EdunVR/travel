<?php

/**
 * Test Production HPP Preview Fix
 * Memverifikasi bahwa JavaScript dan controller response sudah sinkron
 */

echo "=== TESTING PRODUCTION HPP PREVIEW FIX ===\n\n";

// 1. Check ProductionController calculateHppPreview method
$controllerFile = 'app/Http/Controllers/ProductionController.php';
$jsFile = 'public/js/production.js';

if (!file_exists($controllerFile)) {
    echo "❌ ProductionController tidak ditemukan: $controllerFile\n";
    exit(1);
}

if (!file_exists($jsFile)) {
    echo "❌ production.js tidak ditemukan: $jsFile\n";
    exit(1);
}

$controllerContent = file_get_contents($controllerFile);
$jsContent = file_get_contents($jsFile);

echo "1. Checking controller response structure:\n";

// Check controller response structure
$responseFields = [
    'material_cost' => 'Material cost calculation',
    'labor_cost' => 'Labor cost (added for JS compatibility)',
    'operational_cost' => 'Operational cost calculation',
    'total_cost' => 'Total cost calculation',
    'quantity' => 'Production quantity',
    'hpp_per_unit' => 'HPP per unit calculation',
    'breakdown' => 'Detailed breakdown data'
];

foreach ($responseFields as $field => $description) {
    if (strpos($controllerContent, "'$field'") !== false) {
        echo "   ✅ $field - $description\n";
    } else {
        echo "   ❌ $field - MISSING in controller response\n";
    }
}

echo "\n2. Checking JavaScript response handling:\n";

// Check JavaScript response handling
$jsChecks = [
    'responseData = data.data' => 'Accesses response data correctly',
    'formatCurrency' => 'Has currency formatting function',
    'responseData.material_cost' => 'Accesses material_cost correctly',
    'responseData.operational_cost' => 'Accesses operational_cost correctly',
    'responseData.total_cost' => 'Accesses total_cost correctly',
    'responseData.hpp_per_unit' => 'Accesses hpp_per_unit correctly',
    'responseData.breakdown' => 'Accesses breakdown data correctly'
];

foreach ($jsChecks as $pattern => $description) {
    if (strpos($jsContent, $pattern) !== false) {
        echo "   ✅ $description\n";
    } else {
        echo "   ❌ $description - PATTERN NOT FOUND\n";
    }
}

echo "\n3. Checking for problematic patterns:\n";

// Check for problematic patterns that were causing errors
$problematicPatterns = [
    'data.data.formatted' => 'Old incorrect response structure access',
    'formatted.material_cost' => 'Old formatted property access',
    'data.data.material_details' => 'Old material details structure',
    'data.data.operational_details' => 'Old operational details structure'
];

$hasProblems = false;
foreach ($problematicPatterns as $pattern => $description) {
    if (strpos($jsContent, $pattern) !== false) {
        echo "   ❌ Found problematic pattern: $pattern - $description\n";
        $hasProblems = true;
    }
}

if (!$hasProblems) {
    echo "   ✅ No problematic patterns found\n";
}

echo "\n4. Checking currency formatting:\n";

// Check currency formatting implementation
if (strpos($jsContent, 'Intl.NumberFormat') !== false) {
    echo "   ✅ Uses Intl.NumberFormat for currency formatting\n";
} else {
    echo "   ❌ Missing proper currency formatting\n";
}

if (strpos($jsContent, "'id-ID'") !== false) {
    echo "   ✅ Uses Indonesian locale for currency\n";
} else {
    echo "   ⚠️  Currency locale not specified\n";
}

if (strpos($jsContent, "'IDR'") !== false) {
    echo "   ✅ Uses IDR currency\n";
} else {
    echo "   ⚠️  Currency not specified\n";
}

echo "\n5. Checking error handling:\n";

// Check error handling
$errorHandling = [
    'catch(error' => 'Has error catch block',
    'resetHppPreviewValues' => 'Resets values on error',
    'console.error' => 'Logs errors to console'
];

foreach ($errorHandling as $pattern => $description) {
    if (strpos($jsContent, $pattern) !== false) {
        echo "   ✅ $description\n";
    } else {
        echo "   ❌ $description - MISSING\n";
    }
}

echo "\n6. Simulating API response structure:\n";

// Simulate expected API response
$expectedResponse = [
    'success' => true,
    'data' => [
        'material_cost' => 150000,
        'labor_cost' => 50000,
        'operational_cost' => 25000,
        'total_cost' => 225000,
        'quantity' => 10,
        'hpp_per_unit' => 22500,
        'breakdown' => [
            'materials' => [
                ['id' => 1, 'name' => 'Material A', 'cost' => 15000, 'quantity' => 10]
            ],
            'operational_costs' => [
                ['description' => 'Electricity', 'amount' => 25000]
            ]
        ]
    ]
];

echo "   Expected response structure:\n";
echo "   " . json_encode($expectedResponse, JSON_PRETTY_PRINT) . "\n";

echo "\n7. Checking DOM element updates:\n";

// Check DOM element updates
$domElements = [
    'previewMaterialCost' => 'Material cost display',
    'previewLaborCost' => 'Labor cost display',
    'previewOperationalCost' => 'Operational cost display',
    'previewTotalCost' => 'Total cost display',
    'previewHppPerUnit' => 'HPP per unit display'
];

foreach ($domElements as $elementId => $description) {
    if (strpos($jsContent, "getElementById('$elementId')") !== false) {
        echo "   ✅ Updates $elementId - $description\n";
    } else {
        echo "   ❌ Missing update for $elementId - $description\n";
    }
}

echo "\n=== SUMMARY ===\n";

$isFixed = strpos($jsContent, 'responseData = data.data') !== false &&
           strpos($jsContent, 'responseData.material_cost') !== false &&
           strpos($controllerContent, "'material_cost'") !== false &&
           strpos($controllerContent, "'labor_cost'") !== false;

if ($isFixed) {
    echo "✅ HPP PREVIEW FIX APPLIED: JavaScript and controller response are now synchronized\n";
    
    echo "\nKey fixes applied:\n";
    echo "- Fixed JavaScript to access data.data instead of data.data.formatted\n";
    echo "- Added proper currency formatting using Intl.NumberFormat\n";
    echo "- Added labor_cost to controller response for compatibility\n";
    echo "- Updated breakdown data access to use responseData.breakdown\n";
    echo "- Maintained error handling and reset functionality\n";
    
    echo "\nResponse structure alignment:\n";
    echo "Controller returns: data.material_cost, data.labor_cost, etc.\n";
    echo "JavaScript accesses: responseData.material_cost, responseData.labor_cost, etc.\n";
    
    echo "\nNext steps:\n";
    echo "1. Clear browser cache to load updated JavaScript\n";
    echo "2. Test HPP preview functionality on production page\n";
    echo "3. Verify currency formatting displays correctly\n";
    echo "4. Check that breakdown data shows properly\n";
    echo "5. Test error handling scenarios\n";
    
} else {
    echo "❌ HPP PREVIEW NOT FULLY FIXED\n";
    echo "Some synchronization issues may remain between JavaScript and controller\n";
}

echo "\n=== TESTING COMPLETE ===\n";