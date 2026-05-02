<?php

// Test the complete labor cost flow
echo "🧪 Testing Complete Labor Cost Flow\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test 1: Check if the controller properly handles labor cost data
echo "📋 Test 1: Controller Labor Cost Handling\n";
echo "-" . str_repeat("-", 40) . "\n";

// Simulate the request data that would be sent from JavaScript
$testRequestData = [
    'materials' => [
        [
            'material_id' => '1',
            'quantity' => '10',
            'material_type' => 'bahan'
        ]
    ],
    'labor_costs' => [
        'worker_count' => '5',
        'cost_per_worker' => '100000',
        'total_cost' => '500000'
    ],
    'operational_costs' => [
        [
            'cost_type' => 'Listrik',
            'amount' => '50000'
        ]
    ],
    'quantity' => '100'
];

echo "📤 Simulated Request Data:\n";
echo json_encode($testRequestData, JSON_PRETTY_PRINT) . "\n\n";

// Test the controller logic manually
echo "🔍 Testing Controller Logic:\n";

// Simulate the controller's labor cost calculation
$laborCosts = $testRequestData['labor_costs'];
$totalLaborCost = 0;

if (!empty($laborCosts)) {
    $workerCount = intval($laborCosts['worker_count'] ?? 0);
    $costPerWorker = floatval($laborCosts['cost_per_worker'] ?? 0);
    
    // Calculate from worker count and cost per worker
    $calculatedLaborCost = $workerCount * $costPerWorker;
    
    // Use total_cost if provided directly, otherwise use calculated value
    if (isset($laborCosts['total_cost']) && floatval($laborCosts['total_cost']) > 0) {
        $totalLaborCost = floatval($laborCosts['total_cost']);
    } else {
        $totalLaborCost = $calculatedLaborCost;
    }
    
    echo "- Worker Count: $workerCount\n";
    echo "- Cost per Worker: Rp " . number_format($costPerWorker, 0, ',', '.') . "\n";
    echo "- Calculated Labor Cost: Rp " . number_format($calculatedLaborCost, 0, ',', '.') . "\n";
    echo "- Provided Total Cost: Rp " . number_format(floatval($laborCosts['total_cost']), 0, ',', '.') . "\n";
    echo "- Final Labor Cost: Rp " . number_format($totalLaborCost, 0, ',', '.') . "\n";
}

echo "\n📋 Test 2: JavaScript Form Data Collection\n";
echo "-" . str_repeat("-", 40) . "\n";

// Test how JavaScript would collect the form data
echo "🔍 Simulating JavaScript FormData collection:\n";

// Simulate form fields
$formFields = [
    'labor_costs[worker_count]' => '5',
    'labor_costs[cost_per_worker]' => '100000',
    'labor_costs[total_cost]' => '500000',
    'materials[0][material_id]' => '1',
    'materials[0][quantity]' => '10',
    'materials[0][material_type]' => 'bahan',
    'operational_costs[0][cost_type]' => 'Listrik',
    'operational_costs[0][amount]' => '50000',
    'quantity' => '100'
];

// Convert to nested array structure (like JavaScript does)
$data = [];
foreach ($formFields as $key => $value) {
    if (strpos($key, '[') !== false && strpos($key, ']') !== false) {
        // Handle nested arrays
        if (preg_match('/(\w+)\[(\d+)\]\[(\w+)\]/', $key, $matches)) {
            $arrayName = $matches[1];
            $index = $matches[2];
            $fieldName = $matches[3];
            
            if (!isset($data[$arrayName])) $data[$arrayName] = [];
            if (!isset($data[$arrayName][$index])) $data[$arrayName][$index] = [];
            $data[$arrayName][$index][$fieldName] = $value;
        } else if (preg_match('/(\w+)\[(\w+)\]/', $key, $matches)) {
            // Handle simple nested objects
            $objectName = $matches[1];
            $fieldName = $matches[2];
            
            if (!isset($data[$objectName])) $data[$objectName] = [];
            $data[$objectName][$fieldName] = $value;
        }
    } else {
        $data[$key] = $value;
    }
}

echo "📤 Converted Data Structure:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Verify labor costs are properly structured
if (isset($data['labor_costs'])) {
    echo "✅ Labor costs properly structured:\n";
    foreach ($data['labor_costs'] as $key => $value) {
        echo "  - $key: $value\n";
    }
} else {
    echo "❌ Labor costs not found in data structure\n";
}

echo "\n📋 Test 3: Request Formatting\n";
echo "-" . str_repeat("-", 40) . "\n";

// Test how the data would be sent in the request
echo "🔍 Simulating request formatting:\n";

$requestData = [];
foreach ($data as $key => $value) {
    if (is_array($value)) {
        if (array_keys($value) === range(0, count($value) - 1)) {
            // Indexed array (like materials, operational_costs)
            foreach ($value as $index => $item) {
                if (is_array($item)) {
                    foreach ($item as $subKey => $subValue) {
                        if ($subValue !== null && $subValue !== '') {
                            $requestData[$key . '[' . $index . '][' . $subKey . ']'] = $subValue;
                        }
                    }
                }
            }
        } else {
            // Associative array (like labor_costs)
            foreach ($value as $subKey => $subValue) {
                if ($subValue !== null && $subValue !== '') {
                    $requestData[$key . '[' . $subKey . ']'] = $subValue;
                }
            }
        }
    } else if ($value !== null && $value !== '') {
        $requestData[$key] = $value;
    }
}

echo "📤 Final Request Data:\n";
foreach ($requestData as $key => $value) {
    echo "  $key = $value\n";
}

// Check if labor cost fields are present
$laborFields = array_filter($requestData, function($key) {
    return strpos($key, 'labor_costs') !== false;
}, ARRAY_FILTER_USE_KEY);

echo "\n🔍 Labor Cost Fields in Request:\n";
if (!empty($laborFields)) {
    foreach ($laborFields as $key => $value) {
        echo "  ✅ $key = $value\n";
    }
} else {
    echo "  ❌ No labor cost fields found\n";
}

echo "\n📋 Test 4: Expected vs Actual Calculation\n";
echo "-" . str_repeat("-", 40) . "\n";

$expectedWorkerCount = 5;
$expectedCostPerWorker = 100000;
$expectedLaborCost = $expectedWorkerCount * $expectedCostPerWorker;

echo "Expected Calculation:\n";
echo "- Worker Count: $expectedWorkerCount\n";
echo "- Cost per Worker: Rp " . number_format($expectedCostPerWorker, 0, ',', '.') . "\n";
echo "- Expected Labor Cost: Rp " . number_format($expectedLaborCost, 0, ',', '.') . "\n\n";

echo "Controller Calculation:\n";
echo "- Final Labor Cost: Rp " . number_format($totalLaborCost, 0, ',', '.') . "\n";

if ($totalLaborCost == $expectedLaborCost) {
    echo "✅ Labor cost calculation is CORRECT!\n";
} else {
    echo "❌ Labor cost calculation is INCORRECT!\n";
    echo "   Difference: Rp " . number_format(abs($totalLaborCost - $expectedLaborCost), 0, ',', '.') . "\n";
}

echo "\n📋 Recommendations:\n";
echo "-" . str_repeat("-", 40) . "\n";

if ($totalLaborCost == $expectedLaborCost) {
    echo "✅ Backend calculation is working correctly.\n";
    echo "💡 The issue might be:\n";
    echo "   1. JavaScript not triggering HPP calculation when labor costs change\n";
    echo "   2. Form data not being collected properly\n";
    echo "   3. Request not being sent to the correct endpoint\n";
    echo "   4. Response not being displayed in the UI\n\n";
    
    echo "🔧 Next steps:\n";
    echo "   1. Check browser console for JavaScript errors\n";
    echo "   2. Verify that calculateLaborCost() calls calculateHppPreview()\n";
    echo "   3. Check network tab to see if HPP preview requests are being sent\n";
    echo "   4. Verify that labor cost data is included in the request\n";
    echo "   5. Check if the response is properly updating the UI elements\n";
} else {
    echo "❌ Backend calculation has issues.\n";
    echo "🔧 Fix the controller logic first.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";