<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HppProduk;
use App\Models\Produk;

echo "=== HPP DATA INTEGRITY TEST ===\n\n";

try {
    // Test 1: Check for HPP records with null IDs
    echo "1. Checking for HPP records with null IDs...\n";
    $nullIdCount = HppProduk::whereNull('id')->count();
    echo "   Records with null ID: {$nullIdCount}\n";
    
    if ($nullIdCount > 0) {
        echo "   ❌ Found HPP records with null IDs!\n";
        $nullRecords = HppProduk::whereNull('id')->limit(5)->get();
        foreach ($nullRecords as $record) {
            echo "   - Product ID: {$record->id_produk}, Stock: {$record->stok}, HPP: {$record->hpp}\n";
        }
    } else {
        echo "   ✅ No HPP records with null IDs found\n";
    }
    
    // Test 2: Check for HPP records with empty/zero IDs
    echo "\n2. Checking for HPP records with empty/zero IDs...\n";
    $emptyIdCount = HppProduk::where('id', '')->orWhere('id', 0)->count();
    echo "   Records with empty/zero ID: {$emptyIdCount}\n";
    
    if ($emptyIdCount > 0) {
        echo "   ❌ Found HPP records with empty/zero IDs!\n";
    } else {
        echo "   ✅ No HPP records with empty/zero IDs found\n";
    }
    
    // Test 3: Check HPP table structure
    echo "\n3. Checking HPP table structure...\n";
    $columns = \DB::select("DESCRIBE hpp_produk");
    echo "   Table columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) - Key: {$column->Key}, Null: {$column->Null}, Default: {$column->Default}\n";
    }
    
    // Test 4: Sample HPP data
    echo "\n4. Sample HPP data (first 5 records)...\n";
    $sampleData = HppProduk::limit(5)->get();
    foreach ($sampleData as $hpp) {
        echo "   ID: {$hpp->id}, Product: {$hpp->id_produk}, Stock: {$hpp->stok}, HPP: {$hpp->hpp}\n";
    }
    
    // Test 5: Check for products with HPP data
    echo "\n5. Checking products with HPP data...\n";
    $productsWithHpp = Produk::whereHas('hppProduk')->count();
    $totalProducts = Produk::count();
    echo "   Products with HPP data: {$productsWithHpp} / {$totalProducts}\n";
    
    // Test 6: Test the getHppData method simulation
    echo "\n6. Testing HPP data mapping (simulating controller method)...\n";
    $testProductId = HppProduk::first()->id_produk ?? null;
    
    if ($testProductId) {
        echo "   Testing with product ID: {$testProductId}\n";
        
        $hppData = HppProduk::where('id_produk', $testProductId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($hpp, $index) use ($testProductId) {
                return [
                    'id' => $hpp->id,
                    'type' => $hpp->stok > 0 ? 'in' : 'out',
                    'quantity' => abs($hpp->stok),
                    'hpp_per_unit' => $hpp->hpp,
                    'total_value' => abs($hpp->stok) * $hpp->hpp,
                    'notes' => $hpp->keterangan ?? '',
                    'created_at' => $hpp->created_at
                ];
            });
        
        echo "   Mapped HPP data:\n";
        foreach ($hppData as $data) {
            echo "   - ID: " . ($data['id'] ?? 'NULL') . ", Type: {$data['type']}, Qty: {$data['quantity']}\n";
            
            if (!$data['id']) {
                echo "     ❌ Found HPP record with null/empty ID!\n";
            }
        }
    } else {
        echo "   ⚠️ No HPP data found to test with\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}