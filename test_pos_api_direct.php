<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PosController;
use Illuminate\Http\Request;

echo "=== TESTING POS API DIRECTLY ===\n\n";

try {
    // Create a mock request
    $request = new Request();
    $request->merge(['outlet_id' => 1]);
    
    // Create controller instance
    $controller = new PosController(app(\App\Services\JournalEntryService::class));
    
    echo "1. Testing getProducts method directly...\n";
    
    // Call the method
    $response = $controller->getProducts($request);
    
    // Get response data
    $responseData = $response->getData(true);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "Products count: " . count($responseData['data']) . "\n";
    
    if (count($responseData['data']) > 0) {
        echo "\nFirst product:\n";
        $firstProduct = $responseData['data'][0];
        foreach ($firstProduct as $key => $value) {
            echo "- {$key}: {$value}\n";
        }
    } else {
        echo "\n❌ NO PRODUCTS RETURNED!\n";
        
        // Let's debug step by step
        echo "\n2. Debugging step by step...\n";
        
        // Test without cache
        echo "Testing without cache...\n";
        
        $outletId = 1;
        
        $products = \App\Models\Produk::select([
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
        
        echo "Raw query result count: " . $products->count() . "\n";
        
        if ($products->count() > 0) {
            $sampleProduct = $products->first();
            echo "Sample product stock: " . $sampleProduct->stok . "\n";
            echo "Sample product hppProduk count: " . $sampleProduct->hppProduk->count() . "\n";
            
            // Test the filter
            $filtered = $products->filter(function($produk) {
                echo "Checking product {$produk->id_produk} stock: {$produk->stok}\n";
                return $produk->stok > 0;
            });
            
            echo "After filter count: " . $filtered->count() . "\n";
            
            if ($filtered->count() > 0) {
                // Test the map
                $mapped = $filtered->map(function($produk) {
                    echo "Mapping product {$produk->id_produk}\n";
                    
                    // Get primary image or first image
                    $imageUrl = null;
                    if ($produk->primaryImage) {
                        $imageUrl = asset('storage/' . $produk->primaryImage->path);
                    } elseif ($produk->images->count() > 0) {
                        $imageUrl = asset('storage/' . $produk->images->first()->path);
                    }
                    
                    return [
                        'id_produk' => $produk->id_produk,
                        'sku' => $produk->kode_produk,
                        'name' => $produk->nama_produk,
                        'category' => $produk->kategori ? $produk->kategori->nama_kategori : 'Barang',
                        'price' => $produk->harga_jual,
                        'stock' => $produk->stok,
                        'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'pcs',
                        'image' => $imageUrl,
                    ];
                });
                
                $finalArray = $mapped->values()->toArray();
                echo "Final array count: " . count($finalArray) . "\n";
                
                if (count($finalArray) > 0) {
                    echo "First mapped product:\n";
                    foreach ($finalArray[0] as $key => $value) {
                        echo "- {$key}: {$value}\n";
                    }
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";