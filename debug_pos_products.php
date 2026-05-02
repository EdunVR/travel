<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produk;
use App\Models\HppProduk;
use App\Models\Outlet;

echo "=== DEBUG POS PRODUCTS LOADING ===\n\n";

// Test outlet ID 1 (dari console log)
$outletId = 1;

echo "1. Checking outlet exists...\n";
$outlet = Outlet::find($outletId);
if ($outlet) {
    echo "✅ Outlet found: {$outlet->nama_outlet}\n";
} else {
    echo "❌ Outlet ID {$outletId} not found!\n";
    exit;
}

echo "\n2. Checking products in outlet {$outletId}...\n";
$allProducts = Produk::where('id_outlet', $outletId)->get();
echo "Total products in outlet: " . $allProducts->count() . "\n";

if ($allProducts->count() > 0) {
    echo "\nSample products:\n";
    foreach ($allProducts->take(5) as $produk) {
        echo "- ID: {$produk->id_produk}, Name: {$produk->nama_produk}, Active: " . ($produk->is_active ? 'Yes' : 'No') . "\n";
    }
}

echo "\n3. Checking active products...\n";
$activeProducts = Produk::where('id_outlet', $outletId)
    ->where('is_active', true)
    ->get();
echo "Active products: " . $activeProducts->count() . "\n";

echo "\n4. Checking products with stock...\n";
$productsWithStock = [];
foreach ($activeProducts as $produk) {
    $stok = $produk->stok; // This calls the accessor
    if ($stok > 0) {
        $productsWithStock[] = [
            'id' => $produk->id_produk,
            'name' => $produk->nama_produk,
            'stock' => $stok
        ];
    }
}

echo "Products with stock > 0: " . count($productsWithStock) . "\n";

if (count($productsWithStock) > 0) {
    echo "\nProducts with stock:\n";
    foreach (array_slice($productsWithStock, 0, 5) as $produk) {
        echo "- ID: {$produk['id']}, Name: {$produk['name']}, Stock: {$produk['stock']}\n";
    }
} else {
    echo "\n❌ NO PRODUCTS WITH STOCK FOUND!\n";
    
    echo "\n5. Debugging stock calculation...\n";
    if ($activeProducts->count() > 0) {
        $sampleProduct = $activeProducts->first();
        echo "Sample product: {$sampleProduct->nama_produk}\n";
        
        // Check HppProduk records
        $hppRecords = HppProduk::where('id_produk', $sampleProduct->id_produk)->get();
        echo "HPP records for this product: " . $hppRecords->count() . "\n";
        
        if ($hppRecords->count() > 0) {
            echo "HPP records:\n";
            foreach ($hppRecords as $hpp) {
                echo "- HPP ID: {$hpp->id}, Stock: {$hpp->stok}, HPP: {$hpp->hpp}\n";
            }
            
            $totalStok = $hppRecords->sum('stok');
            echo "Total calculated stock: {$totalStok}\n";
        } else {
            echo "❌ No HPP records found for this product!\n";
        }
    }
}

echo "\n6. Testing the exact query from PosController...\n";
try {
    $products = Produk::select([
            'id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 
            'id_outlet', 'id_kategori', 'id_satuan', 'is_active'
        ])
        ->with([
            'satuan:id_satuan,nama_satuan',
            'kategori:id_kategori,nama_kategori',
            'hppProduk' => function($query) {
                $query->select('id_produk', 'hpp', 'stok')
                      ->where('stok', '>', 0);
            },
            'primaryImage:id_image,id_produk,path',
            'images' => function($query) {
                $query->select(['id_image', 'id_produk', 'path'])->limit(1);
            }
        ])
        ->where('id_outlet', $outletId)
        ->where('is_active', true)
        ->get();
    
    echo "Query executed successfully. Products found: " . $products->count() . "\n";
    
    $filteredProducts = $products->filter(function($produk) {
        return $produk->stok > 0;
    });
    
    echo "After stock filter: " . $filteredProducts->count() . "\n";
    
    if ($filteredProducts->count() > 0) {
        echo "\nFiltered products:\n";
        foreach ($filteredProducts->take(3) as $produk) {
            echo "- ID: {$produk->id_produk}, Name: {$produk->nama_produk}, Stock: {$produk->stok}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "\n";
}

echo "\n7. Checking if CacheService is causing issues...\n";
try {
    $cacheKey = "pos_products_outlet_{$outletId}";
    
    // Try to get from cache
    if (class_exists('\App\Services\CacheService')) {
        echo "CacheService exists\n";
        
        // Clear cache for this outlet
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        echo "Cache cleared for key: {$cacheKey}\n";
    } else {
        echo "CacheService not found\n";
    }
    
} catch (Exception $e) {
    echo "Cache error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";