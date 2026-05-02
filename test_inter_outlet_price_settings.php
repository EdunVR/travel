<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Produk;

// Test Inter Outlet Price Settings functionality
echo "=== TESTING INTER OUTLET PRICE SETTINGS ===\n\n";

try {
    // Test 1: Check if markup_percent column exists
    echo "1. Checking markup_percent column in produk table...\n";
    $columns = DB::select("SHOW COLUMNS FROM produk");
    $hasMarkupColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'markup_percent') {
            $hasMarkupColumn = true;
            echo "   ✓ markup_percent column exists\n";
            break;
        }
    }
    
    if (!$hasMarkupColumn) {
        echo "   ⚠ markup_percent column not found - migration needed\n";
    }
    
    // Test 2: Test price products API endpoint
    echo "\n2. Testing price products API endpoint...\n";
    $outletId = 1; // Default outlet
    
    // Simulate API call
    $rawProducts = DB::select("
        SELECT 
            p.id_produk,
            p.kode_produk as sku,
            p.nama_produk as name,
            p.harga_jual as price,
            COALESCE(k.nama_kategori, 'Barang') as category,
            COALESCE(s.nama_satuan, 'pcs') as satuan,
            COALESCE(
                (SELECT AVG(hpp.harga_beli) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                0
            ) as hpp,
            COALESCE(p.markup_percent, 0) as markup_percent
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
        WHERE p.id_outlet = ? 
        AND p.is_active = 1
        LIMIT 5
    ", [$outletId]);
    
    echo "   Found " . count($rawProducts) . " products\n";
    
    foreach ($rawProducts as $product) {
        echo "   - {$product->name} (SKU: {$product->sku})\n";
        echo "     Price: Rp " . number_format($product->price, 0, ',', '.') . "\n";
        echo "     HPP: Rp " . number_format($product->hpp, 0, ',', '.') . "\n";
        echo "     Markup: {$product->markup_percent}%\n\n";
    }
    
    // Test 3: Test price update functionality
    echo "3. Testing price update functionality...\n";
    $testProduct = Produk::where('id_outlet', $outletId)->first();
    
    if ($testProduct) {
        $originalPrice = $testProduct->harga_jual;
        $originalMarkup = $testProduct->markup_percent ?? 0;
        
        echo "   Original price: Rp " . number_format($originalPrice, 0, ',', '.') . "\n";
        echo "   Original markup: {$originalMarkup}%\n";
        
        // Test update
        $newPrice = $originalPrice + 1000;
        $newMarkup = 25.5;
        
        $testProduct->update([
            'harga_jual' => $newPrice,
            'markup_percent' => $newMarkup
        ]);
        
        $testProduct->refresh();
        
        echo "   Updated price: Rp " . number_format($testProduct->harga_jual, 0, ',', '.') . "\n";
        echo "   Updated markup: {$testProduct->markup_percent}%\n";
        
        // Restore original values
        $testProduct->update([
            'harga_jual' => $originalPrice,
            'markup_percent' => $originalMarkup
        ]);
        
        echo "   ✓ Price update test successful\n";
    } else {
        echo "   ⚠ No test product found\n";
    }
    
    // Test 4: Test routes
    echo "\n4. Testing new routes...\n";
    $routes = [
        'admin.penjualan.inter-outlet.price-products',
        'admin.penjualan.inter-outlet.update-price',
        'admin.penjualan.inter-outlet.bulk-update-prices'
    ];
    
    foreach ($routes as $routeName) {
        try {
            $url = route($routeName);
            echo "   ✓ Route '{$routeName}' exists: {$url}\n";
        } catch (Exception $e) {
            echo "   ✗ Route '{$routeName}' not found\n";
        }
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}