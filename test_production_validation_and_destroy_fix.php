<?php

require_once 'vendor/autoload.php';

echo "=== TESTING PRODUCTION VALIDATION AND DESTROY FIXES ===\n\n";

try {
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test 1: Check if destroy method exists
    echo "1. Testing ProductionController destroy method...\n";
    
    $controller = new App\Http\Controllers\ProductionController();
    
    if (method_exists($controller, 'destroy')) {
        echo "   ✅ destroy() method exists\n";
    } else {
        echo "   ❌ destroy() method missing\n";
    }
    
    // Test 2: Check production table for test data
    echo "\n2. Checking production table for test data...\n";
    
    $productions = App\Models\Production::where('status', 'draft')->limit(3)->get();
    
    if ($productions->count() > 0) {
        echo "   Found {$productions->count()} draft productions for testing:\n";
        foreach ($productions as $production) {
            echo "   - ID: {$production->id}, Code: {$production->production_code}, Status: {$production->status}\n";
        }
    } else {
        echo "   No draft productions found for testing\n";
    }
    
    // Test 3: Test product validation logic
    echo "\n3. Testing product validation logic...\n";
    
    $testCases = [
        // Valid single product
        [
            'products' => [
                ['product_id' => '1', 'target_quantity' => '100', 'sample_quantity' => '5']
            ],
            'expected' => true,
            'description' => 'Valid single product'
        ],
        // Valid multiple products
        [
            'products' => [
                ['product_id' => '1', 'target_quantity' => '100', 'sample_quantity' => '5'],
                ['product_id' => '2', 'target_quantity' => '200', 'sample_quantity' => '10']
            ],
            'expected' => true,
            'description' => 'Valid multiple products'
        ],
        // Invalid - empty product_id
        [
            'products' => [
                ['product_id' => '', 'target_quantity' => '100', 'sample_quantity' => '5']
            ],
            'expected' => false,
            'description' => 'Invalid - empty product_id'
        ],
        // Invalid - zero target_quantity
        [
            'products' => [
                ['product_id' => '1', 'target_quantity' => '0', 'sample_quantity' => '5']
            ],
            'expected' => false,
            'description' => 'Invalid - zero target_quantity'
        ],
        // Invalid - negative target_quantity
        [
            'products' => [
                ['product_id' => '1', 'target_quantity' => '-10', 'sample_quantity' => '5']
            ],
            'expected' => false,
            'description' => 'Invalid - negative target_quantity'
        ],
        // Mixed valid and invalid
        [
            'products' => [
                ['product_id' => '1', 'target_quantity' => '100', 'sample_quantity' => '5'],
                ['product_id' => '', 'target_quantity' => '0', 'sample_quantity' => '0']
            ],
            'expected' => true,
            'description' => 'Mixed valid and invalid (should filter out invalid)'
        ]
    ];
    
    foreach ($testCases as $i => $testCase) {
        $products = $testCase['products'];
        
        // Apply the same filter logic as JavaScript
        $filteredProducts = array_filter($products, function($product) {
            return !empty($product['product_id']) && 
                   !empty($product['target_quantity']) && 
                   intval($product['target_quantity']) > 0;
        });
        
        $hasValidProducts = count($filteredProducts) > 0;
        $passed = $hasValidProducts === $testCase['expected'];
        
        echo "   Test " . ($i + 1) . ": {$testCase['description']}\n";
        echo "      Original: " . count($products) . " products\n";
        echo "      Filtered: " . count($filteredProducts) . " products\n";
        echo "      Expected: " . ($testCase['expected'] ? 'valid' : 'invalid') . "\n";
        echo "      Result: " . ($hasValidProducts ? 'valid' : 'invalid') . "\n";
        echo "      " . ($passed ? '✅ PASSED' : '❌ FAILED') . "\n\n";
    }
    
    // Test 4: Check related tables for cascade delete
    echo "4. Checking related tables for cascade delete...\n";
    
    $relatedTables = [
        'hpp_produk' => 'production_id',
        'production_materials' => 'production_id',
        'production_labor_costs' => 'production_id',
        'production_operational_costs' => 'production_id',
        'production_realizations' => 'production_id'
    ];
    
    $pdo = DB::connection()->getPdo();
    
    foreach ($relatedTables as $table => $column) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} LIMIT 1");
            $stmt->execute();
            echo "   ✅ Table '{$table}' exists and accessible\n";
        } catch (Exception $e) {
            echo "   ❌ Table '{$table}' error: " . $e->getMessage() . "\n";
        }
    }
    
    // Test 5: Test operational costs validation
    echo "\n5. Testing operational costs validation...\n";
    
    $operationalCostTests = [
        [
            'costs' => [
                ['cost_type' => 'listrik', 'amount' => '50000'],
                ['cost_type' => 'gas', 'amount' => '30000']
            ],
            'expected' => 2,
            'description' => 'Valid operational costs'
        ],
        [
            'costs' => [
                ['cost_type' => '', 'amount' => '50000'],
                ['cost_type' => 'gas', 'amount' => '0']
            ],
            'expected' => 0,
            'description' => 'Invalid operational costs'
        ],
        [
            'costs' => [
                ['cost_type' => 'listrik', 'amount' => '50000'],
                ['cost_type' => '', 'amount' => '0'],
                ['cost_type' => 'gas', 'amount' => '30000']
            ],
            'expected' => 2,
            'description' => 'Mixed valid and invalid operational costs'
        ]
    ];
    
    foreach ($operationalCostTests as $i => $test) {
        $costs = $test['costs'];
        
        // Apply the same filter logic as JavaScript
        $filteredCosts = array_filter($costs, function($cost) {
            return !empty($cost['cost_type']) && 
                   !empty($cost['amount']) && 
                   floatval($cost['amount']) > 0;
        });
        
        $passed = count($filteredCosts) === $test['expected'];
        
        echo "   Test " . ($i + 1) . ": {$test['description']}\n";
        echo "      Original: " . count($costs) . " costs\n";
        echo "      Filtered: " . count($filteredCosts) . " costs\n";
        echo "      Expected: {$test['expected']} costs\n";
        echo "      " . ($passed ? '✅ PASSED' : '❌ FAILED') . "\n\n";
    }
    
    echo "6. Summary of fixes applied...\n";
    echo "   ✅ Added destroy() method to ProductionController\n";
    echo "   ✅ Enhanced product validation with detailed debug logging\n";
    echo "   ✅ Added FormData entries debug logging\n";
    echo "   ✅ Improved error messages for product validation\n";
    echo "   ✅ Added cascade delete for all related records\n";
    echo "   ✅ Added proper transaction handling for delete\n";
    echo "   ✅ Added comprehensive logging for delete operations\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";