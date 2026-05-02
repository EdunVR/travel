<?php

/**
 * Test Operational Costs Edit Loading
 * Tests if operational costs are properly loaded during edit mode and included in HPP preview
 */

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== TESTING OPERATIONAL COSTS EDIT LOADING ===\n\n";

// Test with a production that has operational costs
$testProductionId = 38; // Use existing production ID

echo "1. TESTING PRODUCTION ID: $testProductionId\n";

try {
    // Get production data using the edit method
    $controller = new \App\Http\Controllers\ProductionController();
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    
    // Call the edit method
    $response = $controller->edit($testProductionId);
    $responseData = json_decode($response->getContent(), true);
    
    if (!$responseData['success']) {
        echo "   ❌ Failed to load production: " . $responseData['message'] . "\n";
        exit;
    }
    
    $production = $responseData['data'];
    
    echo "   ✅ Production loaded: {$production['production_code']}\n";
    echo "   ✅ Operational costs count: " . count($production['operational_costs']) . "\n";
    
    // Check operational costs structure
    echo "\n2. OPERATIONAL COSTS STRUCTURE\n";
    
    if (empty($production['operational_costs'])) {
        echo "   ❌ No operational costs found in edit data\n";
        
        // Check if production has operational costs in database
        $productionModel = \App\Models\Production::with('operationalCosts')->find($testProductionId);
        if ($productionModel && $productionModel->operationalCosts->count() > 0) {
            echo "   ⚠️  Production has operational costs in database but not in edit response\n";
            echo "   Database operational costs:\n";
            foreach ($productionModel->operationalCosts as $index => $cost) {
                echo "      Cost " . ($index + 1) . ":\n";
                echo "         cost_type: {$cost->cost_type}\n";
                echo "         amount: {$cost->amount}\n";
                echo "         description: " . ($cost->description ?? 'null') . "\n";
            }
        }
    } else {
        echo "   ✅ Operational costs found in edit data:\n";
        foreach ($production['operational_costs'] as $index => $cost) {
            echo "      Cost " . ($index + 1) . ":\n";
            echo "         cost_type: " . ($cost['cost_type'] ?? 'null') . "\n";
            echo "         amount: " . ($cost['amount'] ?? 'null') . "\n";
            echo "         description: " . ($cost['description'] ?? 'null') . "\n";
        }
    }
    
    // Test HPP calculation with operational costs
    echo "\n3. TESTING HPP CALCULATION WITH OPERATIONAL COSTS\n";
    
    // Simulate HPP preview request with the loaded data
    $hppRequest = new \Illuminate\Http\Request();
    $hppRequest->merge([
        'materials' => $production['materials'],
        'labor_costs' => $production['labor_costs'],
        'operational_costs' => $production['operational_costs'],
        'products' => $production['products']
    ]);
    
    $hppResponse = $controller->calculateHppPreview($hppRequest);
    $hppData = json_decode($hppResponse->getContent(), true);
    
    if ($hppData['success']) {
        echo "   ✅ HPP calculation successful:\n";
        echo "      Material Cost: Rp " . number_format($hppData['data']['material_cost'], 0, ',', '.') . "\n";
        echo "      Labor Cost: Rp " . number_format($hppData['data']['labor_cost'], 0, ',', '.') . "\n";
        echo "      Operational Cost: Rp " . number_format($hppData['data']['operational_cost'], 0, ',', '.') . "\n";
        echo "      Total Cost: Rp " . number_format($hppData['data']['total_cost'], 0, ',', '.') . "\n";
        echo "      HPP per Unit: Rp " . number_format($hppData['data']['hpp_per_unit'], 0, ',', '.') . "\n";
        
        if ($hppData['data']['operational_cost'] == 0 && !empty($production['operational_costs'])) {
            echo "   ⚠️  WARNING: Operational cost is 0 despite having operational costs data\n";
            echo "   This indicates the operational costs are not being processed correctly in HPP calculation\n";
        }
    } else {
        echo "   ❌ HPP calculation failed: " . ($hppData['message'] ?? 'Unknown error') . "\n";
    }
    
    // Check the operational costs format expected by HPP calculation
    echo "\n4. OPERATIONAL COSTS FORMAT ANALYSIS\n";
    
    echo "   Expected format for HPP calculation:\n";
    echo "   - Array of objects with 'cost_type' or 'description' and 'amount' fields\n";
    echo "   - Amount should be numeric and > 0\n";
    
    echo "\n   Current format from edit response:\n";
    if (!empty($production['operational_costs'])) {
        foreach ($production['operational_costs'] as $index => $cost) {
            $hasValidType = !empty($cost['cost_type']) || !empty($cost['description']);
            $hasValidAmount = !empty($cost['amount']) && is_numeric($cost['amount']) && $cost['amount'] > 0;
            
            echo "      Cost " . ($index + 1) . ":\n";
            echo "         Has valid type: " . ($hasValidType ? 'YES' : 'NO') . "\n";
            echo "         Has valid amount: " . ($hasValidAmount ? 'YES' : 'NO') . "\n";
            echo "         Will be included in HPP: " . ($hasValidType && $hasValidAmount ? 'YES' : 'NO') . "\n";
        }
    }
    
    echo "\n5. RECOMMENDATIONS\n";
    
    if (empty($production['operational_costs'])) {
        echo "   🔧 Fix needed: Operational costs are not being loaded in edit method\n";
        echo "   - Check ProductionController->edit() method\n";
        echo "   - Ensure operational costs are properly mapped in response\n";
    } elseif ($hppData['success'] && $hppData['data']['operational_cost'] == 0) {
        echo "   🔧 Fix needed: Operational costs are loaded but not calculated in HPP\n";
        echo "   - Check calculateHppPreview() method\n";
        echo "   - Ensure operational costs array is properly processed\n";
        echo "   - Check frontend JavaScript for operational costs collection\n";
    } else {
        echo "   ✅ Operational costs appear to be working correctly\n";
    }
    
    echo "\n🎯 OPERATIONAL COSTS EDIT LOADING TEST COMPLETE!\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

?>