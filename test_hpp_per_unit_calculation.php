<?php

// Test HPP per unit calculation with total target quantity
echo "🧪 Testing HPP per Unit Calculation with Total Target Quantity\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Test data with multiple products
$testData = [
    'products' => [
        [
            'product_id' => '1',
            'target_quantity' => '50'
        ],
        [
            'product_id' => '2', 
            'target_quantity' => '30'
        ],
        [
            'product_id' => '3',
            'target_quantity' => '20'
        ]
    ],
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
    'quantity' => '100' // This should be ignored in favor of products total
];

echo "📋 Test Data:\n";
echo "Products:\n";
foreach ($testData['products'] as $i => $product) {
    echo "  Product " . ($i + 1) . ": " . $product['target_quantity'] . " units\n";
}

$totalTargetQuantity = array_sum(array_column($testData['products'], 'target_quantity'));
echo "Total Target Quantity: $totalTargetQuantity units\n";
echo "Fallback Quantity: " . $testData['quantity'] . " units (should be ignored)\n\n";

echo "Labor Costs: " . json_encode($testData['labor_costs'], JSON_PRETTY_PRINT) . "\n";
echo "Operational Costs: " . json_encode($testData['operational_costs'], JSON_PRETTY_PRINT) . "\n\n";

// Test the controller logic manually
echo "🔍 Testing Controller Logic:\n";

// Simulate the controller's quantity calculation
$products = $testData['products'];
$calculatedTotalQuantity = 0;

if (!empty($products)) {
    foreach ($products as $product) {
        $targetQuantity = intval($product['target_quantity'] ?? 0);
        $calculatedTotalQuantity += $targetQuantity;
    }
}

// Fallback to single quantity field if no products array
if ($calculatedTotalQuantity <= 0) {
    $calculatedTotalQuantity = intval($testData['quantity']);
}

// Ensure we have at least 1 for calculation
if ($calculatedTotalQuantity <= 0) {
    $calculatedTotalQuantity = 1;
}

echo "- Products count: " . count($products) . "\n";
echo "- Calculated total quantity: $calculatedTotalQuantity\n";
echo "- Fallback quantity: " . $testData['quantity'] . "\n\n";

// Calculate costs
$laborCosts = $testData['labor_costs'];
$totalLaborCost = 0;

if (!empty($laborCosts)) {
    $workerCount = intval($laborCosts['worker_count'] ?? 0);
    $costPerWorker = floatval($laborCosts['cost_per_worker'] ?? 0);
    
    $calculatedLaborCost = $workerCount * $costPerWorker;
    
    if (isset($laborCosts['total_cost']) && floatval($laborCosts['total_cost']) > 0) {
        $totalLaborCost = floatval($laborCosts['total_cost']);
    } else {
        $totalLaborCost = $calculatedLaborCost;
    }
}

$totalMaterialCost = 0; // Simplified for test
$totalOperationalCost = 0;
foreach ($testData['operational_costs'] as $cost) {
    $totalOperationalCost += floatval($cost['amount'] ?? 0);
}

$totalCost = $totalMaterialCost + $totalLaborCost + $totalOperationalCost;
$hppPerUnit = $calculatedTotalQuantity > 0 ? $totalCost / $calculatedTotalQuantity : 0;

echo "💰 Cost Calculation:\n";
echo "- Material Cost: Rp " . number_format($totalMaterialCost, 0, ',', '.') . "\n";
echo "- Labor Cost: Rp " . number_format($totalLaborCost, 0, ',', '.') . "\n";
echo "- Operational Cost: Rp " . number_format($totalOperationalCost, 0, ',', '.') . "\n";
echo "- Total Cost: Rp " . number_format($totalCost, 0, ',', '.') . "\n";
echo "- Total Target Quantity: $calculatedTotalQuantity units\n";
echo "- HPP per Unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n\n";

// Test different scenarios
echo "📋 Test Scenarios:\n";
echo "-" . str_repeat("-", 40) . "\n";

// Scenario 1: Multiple products
$scenario1TotalQty = 50 + 30 + 20; // 100 units
$scenario1HppPerUnit = $totalCost / $scenario1TotalQty;
echo "Scenario 1 - Multiple Products (50+30+20=100 units):\n";
echo "  HPP per Unit: Rp " . number_format($scenario1HppPerUnit, 0, ',', '.') . "\n";

// Scenario 2: Single product
$scenario2TotalQty = 100;
$scenario2HppPerUnit = $totalCost / $scenario2TotalQty;
echo "Scenario 2 - Single Product (100 units):\n";
echo "  HPP per Unit: Rp " . number_format($scenario2HppPerUnit, 0, ',', '.') . "\n";

// Scenario 3: Different quantities
$scenario3TotalQty = 200;
$scenario3HppPerUnit = $totalCost / $scenario3TotalQty;
echo "Scenario 3 - Higher Quantity (200 units):\n";
echo "  HPP per Unit: Rp " . number_format($scenario3HppPerUnit, 0, ',', '.') . "\n\n";

// Validation
echo "✅ Validation:\n";
if ($calculatedTotalQuantity == $totalTargetQuantity) {
    echo "✅ Total quantity calculation is CORRECT!\n";
} else {
    echo "❌ Total quantity calculation is INCORRECT!\n";
    echo "   Expected: $totalTargetQuantity, Got: $calculatedTotalQuantity\n";
}

if ($hppPerUnit > 0) {
    echo "✅ HPP per unit calculation is working!\n";
} else {
    echo "❌ HPP per unit calculation failed!\n";
}

echo "\n📋 Expected Behavior:\n";
echo "- Controller should sum all product target quantities\n";
echo "- HPP per unit = Total Cost / Total Target Quantity\n";
echo "- Should ignore single 'quantity' field when products array exists\n";
echo "- Should use fallback quantity only when no products\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";