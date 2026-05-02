<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING POS WITH AUTHENTICATION ===\n\n";

// 1. Login as a user
echo "1. Authenticating as user...\n";
$user = User::where('email', 'admin@admin.com')->first();
if (!$user) {
    $user = User::first();
}

if ($user) {
    Auth::login($user);
    echo "✅ Logged in as: {$user->name} (ID: {$user->id})\n";
    echo "User outlet: " . ($user->outlet_id ?? 'Not set') . "\n";
} else {
    echo "❌ No users found in database!\n";
    exit;
}

// 2. Test POS products API with authentication
echo "\n2. Testing POS products API with authentication...\n";

try {
    // Create authenticated request
    $request = new \Illuminate\Http\Request();
    $request->merge(['outlet_id' => 1]);
    
    // Set up request context
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    // Create controller
    $controller = new \App\Http\Controllers\PosController(
        app(\App\Services\JournalEntryService::class)
    );
    
    // Call method
    $response = $controller->getProducts($request);
    $data = $response->getData(true);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "Products count: " . count($data['data']) . "\n";
    
    if (count($data['data']) > 0) {
        echo "\nFirst product:\n";
        foreach ($data['data'][0] as $key => $value) {
            echo "- {$key}: {$value}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// 3. Test direct HTTP request simulation
echo "\n3. Testing HTTP request simulation...\n";

try {
    // Create a proper HTTP request
    $request = \Illuminate\Http\Request::create(
        '/admin/penjualan/pos/products?outlet_id=1',
        'GET'
    );
    
    // Set authenticated user
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    // Simulate middleware
    $request->session()->put('_token', csrf_token());
    $request->session()->put('login_web_' . sha1('web'), $user->id);
    
    // Test the route
    $router = app('router');
    $route = $router->getRoutes()->match($request);
    
    if ($route) {
        echo "✅ Route matched: " . $route->getName() . "\n";
        echo "Route action: " . $route->getActionName() . "\n";
    } else {
        echo "❌ No route matched\n";
    }
    
} catch (Exception $e) {
    echo "❌ Route test error: " . $e->getMessage() . "\n";
}

// 4. Check session data
echo "\n4. Checking session data...\n";
$sessionId = session()->getId();
echo "Session ID: {$sessionId}\n";
echo "Authenticated: " . (Auth::check() ? 'Yes' : 'No') . "\n";
echo "User ID: " . (Auth::id() ?? 'None') . "\n";

// 5. Test cache without authentication context
echo "\n5. Testing cache behavior...\n";
$cacheKey = "pos_products_outlet_1";
Cache::forget($cacheKey);

// Test direct query
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
        }
    ])
    ->where('id_outlet', 1)
    ->where('is_active', true)
    ->get()
    ->filter(function($produk) {
        return $produk->stok > 0;
    })
    ->map(function($produk) {
        return [
            'id_produk' => $produk->id_produk,
            'sku' => $produk->kode_produk,
            'name' => $produk->nama_produk,
            'category' => $produk->kategori ? $produk->kategori->nama_kategori : 'Barang',
            'price' => $produk->harga_jual,
            'stock' => $produk->stok,
            'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'pcs',
            'image' => null,
        ];
    })
    ->values()
    ->toArray();

echo "Direct query result: " . count($products) . " products\n";

if (count($products) > 0) {
    echo "Sample product: {$products[0]['name']} (Stock: {$products[0]['stock']})\n";
}

echo "\n=== TEST COMPLETE ===\n";