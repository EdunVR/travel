<?php

require_once 'vendor/autoload.php';

echo "=== TEST SEARCH METHODS FINAL ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $controller = new App\Http\Controllers\PermintaanBarangController();
    
    // Test searchProducts
    echo "1. Testing searchProducts with updated method...\n";
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => 'to', 'outlet_id' => 3]);
    
    $response = $controller->searchProducts($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Products found: " . count($data) . "\n";
    foreach ($data as $product) {
        echo "  - {$product['nama']} (SKU: {$product['sku']}) - Satuan: " . 
             ($product['satuan'] ? $product['satuan']['nama'] : 'null') . "\n";
    }
    
    // Test searchMaterials
    echo "\n2. Testing searchMaterials with updated method...\n";
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => 'bahan', 'outlet_id' => 3]);
    
    $response = $controller->searchMaterials($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Materials found: " . count($data) . "\n";
    foreach ($data as $material) {
        echo "  - {$material['nama']} (Code: {$material['kode']}) - Satuan: " . 
             ($material['satuan'] ? $material['satuan']['nama'] : 'null') . "\n";
    }
    
    // Test with different search terms
    echo "\n3. Testing with different search terms...\n";
    
    // Search for any product
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => '', 'outlet_id' => 3]);
    
    $response = $controller->searchProducts($request);
    $data = json_decode($response->getContent(), true);
    
    echo "All products (limit 10): " . count($data) . "\n";
    if (count($data) > 0) {
        echo "  First product: {$data[0]['nama']} - Satuan: " . 
             ($data[0]['satuan'] ? $data[0]['satuan']['nama'] : 'null') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";