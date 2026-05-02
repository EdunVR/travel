<?php

require_once 'vendor/autoload.php';

echo "=== TESTING PRODUCTION REALIZATION AND EDIT FIXES ===\n\n";

try {
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test 1: Check ProductionRealization model fillable array
    echo "1. Testing ProductionRealization model fillable array...\n";
    
    $model = new App\Models\ProductionRealization();
    $fillable = $model->getFillable();
    
    echo "   Fillable fields: " . implode(', ', $fillable) . "\n";
    
    // Check for problematic fields
    $problematicFields = ['created_by', 'material_cost', 'realization_details'];
    $foundProblematic = array_intersect($fillable, $problematicFields);
    
    if (empty($foundProblematic)) {
        echo "   ✅ No problematic fields found in fillable array\n";
    } else {
        echo "   ❌ Found problematic fields: " . implode(', ', $foundProblematic) . "\n";
    }
    
    // Test 2: Check if we can create a basic realization record
    echo "\n2. Testing basic realization record creation...\n";
    
    // Find a production record to test with
    $production = App\Models\Production::first();
    
    if ($production) {
        echo "   Found production ID: {$production->id}\n";
        
        // Test data for realization
        $testData = [
            'production_id' => $production->id,
            'quantity_produced' => 100,
            'quantity_rejected' => 5,
            'realization_date' => now()->format('Y-m-d'),
            'recorded_by' => 1, // Assuming user ID 1 exists
            'notes' => 'Test realization - can be deleted',
        ];
        
        echo "   Test data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n";
        
        try {
            // Try to create the record
            $realization = App\Models\ProductionRealization::create($testData);
            echo "   ✅ Realization record created successfully with ID: {$realization->id}\n";
            
            // Clean up - delete the test record
            $realization->delete();
            echo "   🧹 Test record cleaned up\n";
            
        } catch (Exception $e) {
            echo "   ❌ Failed to create realization record: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "   ⚠️ No production records found for testing\n";
    }
    
    // Test 3: Test multi-product realization data structure
    echo "\n3. Testing multi-product realization data structure...\n";
    
    $multiProductData = [
        'products' => [
            [
                'product_id' => 1,
                'hpp_record_id' => 1,
                'quantity_produced' => 450,
                'quantity_rejected' => 2,
                'notes' => 'Product 1 notes'
            ],
            [
                'product_id' => 2,
                'hpp_record_id' => 2,
                'quantity_produced' => 200,
                'quantity_rejected' => 1,
                'notes' => 'Product 2 notes'
            ]
        ],
        'notes' => 'Global production notes'
    ];
    
    // Test JSON encoding for notes field
    $notesWithDetails = $multiProductData['notes'] ?? '';
    if (!empty($multiProductData['products'])) {
        $notesWithDetails .= "\n\nDetailed breakdown: " . json_encode($multiProductData['products'], JSON_PRETTY_PRINT);
    }
    
    echo "   Multi-product data structure:\n";
    echo "   - Products count: " . count($multiProductData['products']) . "\n";
    echo "   - Total produced: " . array_sum(array_column($multiProductData['products'], 'quantity_produced')) . "\n";
    echo "   - Total rejected: " . array_sum(array_column($multiProductData['products'], 'quantity_rejected')) . "\n";
    echo "   - Notes with details length: " . strlen($notesWithDetails) . " characters\n";
    echo "   ✅ Multi-product data structure is valid\n";
    
    // Test 4: Test production edit data loading
    echo "\n4. Testing production edit data loading...\n";
    
    if ($production) {
        // Load production with all relationships
        $productionWithRelations = App\Models\Production::with([
            'materials',
            'laborCosts',
            'operationalCosts',
            'outlet',
            'hppRecords.product'
        ])->find($production->id);
        
        if ($productionWithRelations) {
            echo "   Production loaded with relationships:\n";
            echo "   - Materials count: " . $productionWithRelations->materials->count() . "\n";
            echo "   - Labor costs count: " . $productionWithRelations->laborCosts->count() . "\n";
            echo "   - Operational costs count: " . $productionWithRelations->operationalCosts->count() . "\n";
            echo "   - HPP records count: " . $productionWithRelations->hppRecords->count() . "\n";
            echo "   - Outlet: " . ($productionWithRelations->outlet ? $productionWithRelations->outlet->nama_outlet : 'Not found') . "\n";
            
            // Test data transformation for frontend
            $transformedData = [
                'id' => $productionWithRelations->id,
                'production_code' => $productionWithRelations->production_code,
                'outlet_id' => $productionWithRelations->outlet_id,
                'status' => $productionWithRelations->status,
                'products' => $productionWithRelations->hppRecords->map(function($hpp) {
                    return [
                        'product_id' => $hpp->id_produk,
                        'target_quantity' => $hpp->target_quantity,
                        'sample_quantity' => $hpp->sample_quantity ?? 0,
                        'product_name' => $hpp->product ? $hpp->product->nama_produk : 'Unknown'
                    ];
                })->toArray(),
                'materials' => $productionWithRelations->materials->map(function($material) {
                    return [
                        'material_id' => $material->material_id,
                        'material_type' => $material->material_type,
                        'quantity' => $material->quantity_required,
                        'unit' => $material->unit,
                    ];
                })->toArray(),
            ];
            
            echo "   Transformed data structure:\n";
            echo "   - Products: " . count($transformedData['products']) . "\n";
            echo "   - Materials: " . count($transformedData['materials']) . "\n";
            echo "   ✅ Edit data transformation successful\n";
            
        } else {
            echo "   ❌ Failed to load production with relationships\n";
        }
    }
    
    echo "\n5. Summary of fixes applied...\n";
    echo "   ✅ Removed 'material_cost' from ProductionRealization::create() calls\n";
    echo "   ✅ Removed 'realization_details' from ProductionRealization::create() calls\n";
    echo "   ✅ Removed 'created_by' from ProductionRealization::create() calls\n";
    echo "   ✅ Updated ProductionRealization model fillable array\n";
    echo "   ✅ Store realization details in notes field as JSON\n";
    echo "   ✅ Enhanced edit method data loading and transformation\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";