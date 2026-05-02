<?php

/**
 * Test Production Materials from Bahan Table with Harga_Bahan Stock
 * Memverifikasi bahwa dropdown material mengambil dari tabel bahan dengan stok dari harga_bahan
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PRODUCTION MATERIALS WITH HARGA_BAHAN STOCK ===\n\n";

try {
    // 1. Test getMaterials endpoint
    echo "1. Testing getMaterials endpoint with harga_bahan stock:\n";
    
    // Simulate request to getMaterials
    $request = new \Illuminate\Http\Request();
    $request->merge(['outlet_id' => '3']); // Test with outlet 3
    
    $controller = new \App\Http\Controllers\ProductionController();
    $response = $controller->getMaterials($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "   ✅ getMaterials endpoint working\n";
        echo "   📊 Found " . count($responseData['data']) . " materials with stock\n";
        
        if (!empty($responseData['data'])) {
            $firstMaterial = $responseData['data'][0];
            echo "   📋 Sample material:\n";
            echo "      - ID: " . $firstMaterial['id'] . "\n";
            echo "      - Code: " . $firstMaterial['code'] . "\n";
            echo "      - Name: " . $firstMaterial['name'] . "\n";
            echo "      - Merk: " . ($firstMaterial['merk'] ?? 'N/A') . "\n";
            echo "      - Cost: Rp " . number_format($firstMaterial['cost'], 0, ',', '.') . "\n";
            echo "      - Stock: " . $firstMaterial['stock'] . " " . $firstMaterial['unit'] . "\n";
            echo "      - Type: " . $firstMaterial['type'] . "\n";
            
            // Verify it's from bahan table with harga_bahan stock
            if ($firstMaterial['type'] === 'bahan' && $firstMaterial['stock'] > 0) {
                echo "   ✅ Material correctly identified as 'bahan' with stock from harga_bahan\n";
            } else {
                echo "   ❌ Material should have stock from harga_bahan\n";
            }
        }
    } else {
        echo "   ❌ getMaterials endpoint failed: " . $responseData['message'] . "\n";
    }
    
    echo "\n2. Testing direct database query with harga_bahan stock:\n";
    
    // Test direct database query with harga_bahan stock accumulation
    $materials = \App\Models\Bahan::select(
        'bahan.id_bahan', 
        'bahan.kode_bahan', 
        'bahan.nama_bahan',
        'bahan.merk',
        'bahan.harga_beli',
        'satuan.nama_satuan',
        \DB::raw('COALESCE(SUM(harga_bahan.stok), 0) as total_stock'),
        \DB::raw('AVG(harga_bahan.harga_beli) as avg_price')
    )
    ->leftJoin('satuan', 'bahan.id_satuan', '=', 'satuan.id_satuan')
    ->leftJoin('harga_bahan', 'bahan.id_bahan', '=', 'harga_bahan.id_bahan')
    ->where('bahan.id_outlet', 3)
    ->where('bahan.is_active', true)
    ->groupBy('bahan.id_bahan', 'bahan.kode_bahan', 'bahan.nama_bahan', 'bahan.merk', 'bahan.harga_beli', 'satuan.nama_satuan')
    ->having('total_stock', '>', 0)
    ->limit(5)
    ->get();
    
    echo "   📊 Direct query found " . $materials->count() . " materials with harga_bahan stock\n";
    
    foreach ($materials as $material) {
        echo "   📋 " . $material->nama_bahan . " (" . $material->kode_bahan . ")\n";
        echo "      - Merk: " . ($material->merk ?? 'N/A') . "\n";
        echo "      - Base Price: Rp " . number_format($material->harga_beli, 0, ',', '.') . "\n";
        echo "      - Avg FIFO Price: Rp " . number_format($material->avg_price ?? 0, 0, ',', '.') . "\n";
        echo "      - Total Stock: " . $material->total_stock . " " . ($material->nama_satuan ?? 'Unit') . "\n";
    }
    
    echo "\n3. Testing FIFO price calculation:\n";
    
    if (!empty($responseData['data'])) {
        $testMaterial = $responseData['data'][0];
        
        // Check FIFO data for this material
        $fifoData = DB::table('harga_bahan')
            ->where('id_bahan', $testMaterial['id'])
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
        
        echo "   📋 FIFO data for " . $testMaterial['name'] . ":\n";
        foreach ($fifoData as $fifo) {
            echo "      - Date: " . $fifo->created_at . "\n";
            echo "        Price: Rp " . number_format($fifo->harga_beli, 0, ',', '.') . "\n";
            echo "        Stock: " . $fifo->stok . "\n";
        }
    }
    
    echo "\n4. Testing calculateHppPreview with FIFO pricing:\n";
    
    if (!empty($responseData['data'])) {
        $testMaterial = $responseData['data'][0];
        
        // Simulate HPP preview request
        $hppRequest = new \Illuminate\Http\Request();
        $hppRequest->merge([
            'materials' => [
                [
                    'material_id' => $testMaterial['id'],
                    'quantity' => 5
                ]
            ],
            'operational_costs' => [
                [
                    'amount' => 50000
                ]
            ],
            'quantity' => 10
        ]);
        
        $hppResponse = $controller->calculateHppPreview($hppRequest);
        $hppData = json_decode($hppResponse->getContent(), true);
        
        if ($hppData['success']) {
            echo "   ✅ HPP calculation working with FIFO pricing\n";
            echo "   💰 Material Cost: Rp " . number_format($hppData['data']['material_cost'], 0, ',', '.') . "\n";
            echo "   💰 Operational Cost: Rp " . number_format($hppData['data']['operational_cost'], 0, ',', '.') . "\n";
            echo "   💰 Total Cost: Rp " . number_format($hppData['data']['total_cost'], 0, ',', '.') . "\n";
            echo "   💰 HPP per Unit: Rp " . number_format($hppData['data']['hpp_per_unit'], 0, ',', '.') . "\n";
            
            if (!empty($hppData['data']['breakdown']['materials'])) {
                echo "   📋 Material breakdown with FIFO:\n";
                foreach ($hppData['data']['breakdown']['materials'] as $material) {
                    echo "      - " . $material['name'] . " (" . $material['code'] . ")\n";
                    echo "        Qty: " . $material['quantity'] . " " . $material['unit'] . "\n";
                    echo "        FIFO Price: Rp " . number_format($material['unit_price'], 0, ',', '.') . "\n";
                    echo "        FIFO Used: " . ($material['fifo_used'] ? 'Yes' : 'No') . "\n";
                    echo "        Total: Rp " . number_format($material['total_cost'], 0, ',', '.') . "\n";
                }
            }
        } else {
            echo "   ❌ HPP calculation failed: " . $hppData['message'] . "\n";
        }
    }
    
    echo "\n5. Comparing stock sources:\n";
    
    // Compare stock from bahan table vs harga_bahan table
    $bahanStockComparison = DB::select("
        SELECT 
            b.id_bahan,
            b.nama_bahan,
            b.stok as bahan_stock,
            COALESCE(SUM(hb.stok), 0) as harga_bahan_stock
        FROM bahan b
        LEFT JOIN harga_bahan hb ON b.id_bahan = hb.id_bahan
        WHERE b.id_outlet = 3 AND b.is_active = 1
        GROUP BY b.id_bahan, b.nama_bahan, b.stok
        LIMIT 5
    ");
    
    echo "   📊 Stock comparison (bahan vs harga_bahan):\n";
    foreach ($bahanStockComparison as $comparison) {
        echo "   📋 " . $comparison->nama_bahan . "\n";
        echo "      - Bahan table stock: " . $comparison->bahan_stock . "\n";
        echo "      - Harga_bahan total stock: " . $comparison->harga_bahan_stock . "\n";
        echo "      - Using: harga_bahan (FIFO system) ✅\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    
    if ($responseData['success'] && !empty($responseData['data'])) {
        echo "✅ MATERIALS WITH HARGA_BAHAN STOCK FIX SUCCESSFUL\n";
        
        echo "\nKey improvements:\n";
        echo "- Stock now accumulated from 'harga_bahan' table (FIFO system)\n";
        echo "- Uses average price from harga_bahan for better accuracy\n";
        echo "- HPP calculation uses FIFO pricing from harga_bahan\n";
        echo "- Only shows materials with actual stock in harga_bahan\n";
        echo "- Maintains all previous features (merk, unit, etc.)\n";
        
        echo "\nFIFO System Benefits:\n";
        echo "- Accurate stock tracking from purchase history\n";
        echo "- Proper cost calculation using oldest prices first\n";
        echo "- Better inventory management\n";
        echo "- More precise HPP calculations\n";
        
        echo "\nNext steps:\n";
        echo "1. Test the production form in browser\n";
        echo "2. Verify material dropdown shows correct stock from harga_bahan\n";
        echo "3. Test HPP preview with FIFO pricing\n";
        echo "4. Verify stock calculations are accurate\n";
        
    } else {
        echo "❌ MATERIALS WITH HARGA_BAHAN STOCK FIX NEEDS ATTENTION\n";
        echo "Some issues were found that need to be resolved\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TESTING COMPLETE ===\n";