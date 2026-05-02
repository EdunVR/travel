<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Products API ===\n\n";

try {
    // Check if products exist
    echo "1. Checking products in database:\n";
    $products = DB::table('produk')
        ->where('is_active', 1)
        ->count();
    
    echo "   Found " . $products . " active products\n";
    
    // Check products with stock > 0
    echo "\n2. Checking products with stock in hpp_produk:\n";
    $productsWithStock = DB::table('produk')
        ->leftJoin('hpp_produk', 'produk.id_produk', '=', 'hpp_produk.id_produk')
        ->select(
            'produk.id_produk', 
            'produk.nama_produk', 
            'produk.harga_jual',
            DB::raw('COALESCE(SUM(hpp_produk.stok), 0) as total_stok')
        )
        ->where('produk.is_active', 1)
        ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.harga_jual')
        ->having('total_stok', '>', 0)
        ->get();
    
    echo "   Found " . $productsWithStock->count() . " products with stock\n";
    
    if ($productsWithStock->count() > 0) {
        echo "\n   Products with stock:\n";
        foreach ($productsWithStock->take(5) as $product) {
            echo "   - ID: {$product->id_produk}, Name: {$product->nama_produk}, Price: " . number_format($product->harga_jual, 0) . ", Stock: {$product->total_stok}\n";
        }
    } else {
        echo "\n   ⚠️ WARNING: No products have stock > 0!\n";
        echo "   You need to add stock to products first.\n";
    }
    
    // Check route
    echo "\n3. Checking route registration:\n";
    $routes = Route::getRoutes();
    $found = false;
    foreach ($routes as $route) {
        if ($route->uri() === 'api/products' && in_array('GET', $route->methods())) {
            $found = true;
            echo "   ✓ Route found: GET /api/products\n";
            echo "   Controller: " . $route->getActionName() . "\n";
            break;
        }
    }
    
    if (!$found) {
        echo "   ✗ Route NOT found!\n";
    }
    
    echo "\n4. Testing API response:\n";
    $controller = new \App\Http\Controllers\PublicPackageController();
    $request = new \Illuminate\Http\Request();
    $response = $controller->getProducts($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   Response status: " . $response->getStatusCode() . "\n";
    echo "   Products returned: " . count($data) . "\n";
    
    if (count($data) > 0) {
        echo "\n   ✅ API is working correctly!\n";
        echo "\n   Sample response:\n";
        echo "   " . json_encode(array_slice($data, 0, 2), JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "\n   ⚠️ API returns empty array - no products with stock!\n";
        echo "\n   To fix this, add stock to products in the admin panel.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
