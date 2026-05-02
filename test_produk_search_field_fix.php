<?php

require_once 'vendor/autoload.php';

// Test script untuk memverifikasi fix field names di searchProducts dan searchMaterials

echo "=== TEST PRODUK SEARCH FIELD FIX ===\n\n";

// Test 1: Cek struktur tabel produk
echo "1. Checking Produk table structure...\n";
try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $columns = DB::select("DESCRIBE produk");
    echo "Produk table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    echo "✓ Produk table structure checked\n\n";
} catch (Exception $e) {
    echo "✗ Error checking Produk table: " . $e->getMessage() . "\n\n";
}

// Test 2: Cek struktur tabel bahan
echo "2. Checking Bahan table structure...\n";
try {
    $columns = DB::select("DESCRIBE bahan");
    echo "Bahan table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    echo "✓ Bahan table structure checked\n\n";
} catch (Exception $e) {
    echo "✗ Error checking Bahan table: " . $e->getMessage() . "\n\n";
}

// Test 3: Cek struktur tabel satuan
echo "3. Checking Satuan table structure...\n";
try {
    $columns = DB::select("DESCRIBE satuan");
    echo "Satuan table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    echo "✓ Satuan table structure checked\n\n";
} catch (Exception $e) {
    echo "✗ Error checking Satuan table: " . $e->getMessage() . "\n\n";
}

// Test 4: Test searchProducts method dengan field names yang benar
echo "4. Testing searchProducts method...\n";
try {
    $controller = new App\Http\Controllers\PermintaanBarangController();
    
    // Simulate request
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => 'to', 'outlet_id' => 3]);
    
    $response = $controller->searchProducts($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Search products response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    echo "✓ searchProducts method working\n\n";
} catch (Exception $e) {
    echo "✗ Error in searchProducts: " . $e->getMessage() . "\n\n";
}

// Test 5: Test searchMaterials method dengan field names yang benar
echo "5. Testing searchMaterials method...\n";
try {
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => 'mat', 'outlet_id' => 3]);
    
    $response = $controller->searchMaterials($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Search materials response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    echo "✓ searchMaterials method working\n\n";
} catch (Exception $e) {
    echo "✗ Error in searchMaterials: " . $e->getMessage() . "\n\n";
}

// Test 6: Test direct SQL query dengan field names yang benar
echo "6. Testing direct SQL queries...\n";
try {
    // Test produk query
    $produkQuery = "SELECT id_produk as id, nama_produk as nama, kode_produk as sku, id_satuan as satuan_id 
                    FROM produk 
                    WHERE nama_produk LIKE '%to%' AND id_outlet = 3 
                    LIMIT 5";
    
    $produkResults = DB::select($produkQuery);
    echo "Direct produk query results: " . count($produkResults) . " rows\n";
    
    // Test bahan query
    $bahanQuery = "SELECT id_bahan as id, nama_bahan as nama, kode_bahan as kode, id_satuan as satuan_id 
                   FROM bahan 
                   WHERE nama_bahan LIKE '%mat%' AND id_outlet = 3 
                   LIMIT 5";
    
    $bahanResults = DB::select($bahanQuery);
    echo "Direct bahan query results: " . count($bahanResults) . " rows\n";
    
    echo "✓ Direct SQL queries working\n\n";
} catch (Exception $e) {
    echo "✗ Error in direct SQL: " . $e->getMessage() . "\n\n";
}

echo "=== TEST COMPLETED ===\n";