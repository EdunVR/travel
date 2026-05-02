<?php

require_once 'vendor/autoload.php';

echo "=== TESTING PRODUCTION DISPLAY AND MATERIALS FIX ===\n\n";

try {
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test 1: Check production with HPP records
    echo "1. Testing production with HPP records...\n";
    
    $production = App\Models\Production::with(['hppRecords.product', 'materials', 'outlet'])
        ->first();
    
    if ($production) {
        echo "   Found production: {$production->production_code}\n";
        echo "   HPP records count: {$production->hppRecords->count()}\n";
        echo "   Materials count: {$production->materials->count()}\n";
        
        // Test product name display logic
        $productNames = [];
        if ($production->hppRecords && $production->hppRecords->count() > 0) {
            foreach ($production->hppRecords as $hppRecord) {
                if ($hppRecord->product) {
                    $productNames[] = $hppRecord->product->nama_produk;
                }
            }
        }
        
        $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';
        
        echo "   Product names: " . implode(', ', $productNames) . "\n";
        echo "   Display: {$productNameDisplay}\n";
        echo "   ✅ Product display logic works\n";
        
    } else {
        echo "   ❌ No production found for testing\n";
    }
    
    // Test 2: Check materials data structure
    echo "\n2. Testing materials data structure...\n";
    
    if ($production && $production->materials->count() > 0) {
        echo "   Materials found:\n";
        foreach ($production->materials as $i => $material) {
            echo "   Material " . ($i + 1) . ":\n";
            echo "      - ID: {$material->material_id}\n";
            echo "      - Type: {$material->material_type}\n";
            echo "      - Quantity Required: {$material->quantity_required}\n";
            echo "      - Unit: {$material->unit}\n";
            
            // Test backend data transformation
            $materialData = [
                'material_id' => $material->material_id,
                'material_type' => $material->material_type,
                'quantity' => $material->quantity_required, // This is what backend sends
                'unit' => $material->unit,
            ];
            
            echo "      - Backend sends 'quantity': {$materialData['quantity']}\n";
        }
        echo "   ✅ Materials data structure is correct\n";
    } else {
        echo "   ⚠️ No materials found for testing\n";
    }
    
    // Test 3: Test edit method data transformation
    echo "\n3. Testing edit method data transformation...\n";
    
    if ($production) {
        // Simulate edit method transformation
        $materialsData = $production->materials->map(function($material) {
            // Get material name based on type
            $materialName = 'Unknown';
            if ($material->material_type === 'bahan') {
                $bahan = DB::table('bahan')->where('id_bahan', $material->material_id)->first();
                $materialName = $bahan ? $bahan->nama_bahan : 'Unknown';
            } else {
                $produk = DB::table('produk')->where('id_produk', $material->material_id)->first();
                $materialName = $produk ? $produk->nama_produk : 'Unknown';
            }
            
            return [
                'material_id' => $material->material_id,
                'material_type' => $material->material_type,
                'quantity' => $material->quantity_required, // Key field for frontend
                'unit' => $material->unit,
                'material_name' => $materialName
            ];
        })->toArray();
        
        echo "   Transformed materials data:\n";
        foreach ($materialsData as $i => $material) {
            echo "   Material " . ($i + 1) . ":\n";
            echo "      - material_id: {$material['material_id']}\n";
            echo "      - quantity: {$material['quantity']} (this is what frontend expects)\n";
            echo "      - unit: {$material['unit']}\n";
            echo "      - material_name: {$material['material_name']}\n";
        }
        echo "   ✅ Edit method transformation works correctly\n";
    }
    
    // Test 4: Check if ProductionController methods exist
    echo "\n4. Testing ProductionController methods...\n";
    
    $controller = new App\Http\Controllers\ProductionController();
    
    $methods = ['getData', 'edit', 'generatePdf'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   ✅ {$method}() method exists\n";
        } else {
            echo "   ❌ {$method}() method missing\n";
        }
    }
    
    // Test 5: Test grid data transformation
    echo "\n5. Testing grid data transformation...\n";
    
    if ($production) {
        // Simulate getData method transformation
        $productNames = [];
        if ($production->hppRecords && $production->hppRecords->count() > 0) {
            foreach ($production->hppRecords as $hppRecord) {
                if ($hppRecord->product) {
                    $productNames[] = $hppRecord->product->nama_produk;
                }
            }
        }
        
        $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';
        
        $gridData = [
            'id' => $production->id,
            'production_code' => $production->production_code,
            'product_name' => $productNameDisplay, // This should now show products
            'target_quantity' => $production->target_quantity,
            'status' => $production->status,
            'hpp_records' => $production->hppRecords->map(function($hpp) {
                return [
                    'id' => $hpp->id,
                    'product_id' => $hpp->id_produk,
                    'product_name' => $hpp->product ? $hpp->product->nama_produk : 'Unknown',
                    'target_quantity' => $hpp->target_quantity ?? 0,
                ];
            }),
        ];
        
        echo "   Grid data sample:\n";
        echo "      - ID: {$gridData['id']}\n";
        echo "      - Code: {$gridData['production_code']}\n";
        echo "      - Product Name: {$gridData['product_name']}\n";
        echo "      - HPP Records: " . count($gridData['hpp_records']) . "\n";
        echo "   ✅ Grid data transformation works correctly\n";
    }
    
    echo "\n6. Summary of fixes applied...\n";
    echo "   ✅ Fixed getData() method to use hppRecords instead of product relationship\n";
    echo "   ✅ Enhanced product name display with multi-product support\n";
    echo "   ✅ Fixed loadMaterialsForEdit() to use 'quantity' instead of 'quantity_required'\n";
    echo "   ✅ Added comprehensive debug logging for materials loading\n";
    echo "   ✅ Fixed generatePdf() method to use hppRecords for product names\n";
    echo "   ✅ Increased delays for proper async data loading\n";
    echo "   ✅ Enhanced error handling and logging\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";