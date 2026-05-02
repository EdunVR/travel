<?php

/**
 * Test POS Products API with Outlet Filter
 * 
 * Usage: php test_pos_outlet_products.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Outlet;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

echo "=== POS Products API Test ===\n\n";

// Get all outlets
$outlets = Outlet::all();
echo "📍 Total Outlets: " . $outlets->count() . "\n\n";

foreach ($outlets as $outlet) {
    echo "Outlet: {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    echo str_repeat("-", 50) . "\n";
    
    // Get products for this outlet
    $products = Produk::where('id_outlet', $outlet->id_outlet)
        ->where('is_active', true)
        ->with(['kategori', 'images', 'hppProduk'])
        ->get();
    
    echo "✅ Total Products: " . $products->count() . "\n";
    
    if ($products->count() > 0) {
        echo "\nSample Products:\n";
        foreach ($products->take(3) as $product) {
            $primaryImage = $product->images->where('is_primary', true)->first();
            $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
            
            echo "  - {$product->nama_produk}\n";
            echo "    SKU: {$product->sku}\n";
            echo "    Harga: Rp " . number_format($product->harga_jual, 0, ',', '.') . "\n";
            echo "    Stok: {$product->stok}\n";
            echo "    Kategori: " . ($product->kategori->nama_kategori ?? 'N/A') . "\n";
            echo "    Image: " . ($imageUrl ? "✅ Ada" : "❌ Tidak ada") . "\n";
            echo "\n";
        }
    } else {
        echo "⚠️  No products found for this outlet\n";
    }
    
    echo "\n";
}

// Test API response format
echo "\n=== Testing API Response Format ===\n\n";

$testOutlet = $outlets->first();
if ($testOutlet) {
    echo "Testing with Outlet: {$testOutlet->nama_outlet}\n\n";
    
    $products = Produk::where('id_outlet', $testOutlet->id_outlet)
        ->where('is_active', true)
        ->with(['kategori', 'images', 'hppProduk'])
        ->get();
    
    $formattedProducts = $products->map(function($product) {
        $primaryImage = $product->images->where('is_primary', true)->first();
        $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
        
        return [
            'sku' => $product->sku,
            'name' => $product->nama_produk,
            'price' => $product->harga_jual,
            'stock' => $product->stok,
            'category' => $product->kategori->nama_kategori ?? 'Uncategorized',
            'image' => $imageUrl,
        ];
    });
    
    $response = [
        'success' => true,
        'data' => $formattedProducts,
        'count' => $formattedProducts->count()
    ];
    
    echo "Response Structure:\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}

echo "\n✅ Test Complete!\n";
