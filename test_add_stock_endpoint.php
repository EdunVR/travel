<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== TEST ADD STOCK ENDPOINT ===\n\n";

try {
    // 1. Check if route exists
    echo "1. Checking route registration...\n";
    $routes = Route::getRoutes();
    $addStockRoute = null;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'add-stock')) {
            $addStockRoute = $route;
            break;
        }
    }
    
    if ($addStockRoute) {
        echo "✅ Route found: " . $addStockRoute->methods()[0] . " " . $addStockRoute->uri() . "\n";
        echo "   Name: " . $addStockRoute->getName() . "\n";
        echo "   Action: " . $addStockRoute->getActionName() . "\n";
        echo "   Middleware: " . implode(', ', $addStockRoute->middleware()) . "\n\n";
    } else {
        echo "❌ Route not found\n";
        exit(1);
    }
    
    // 2. Check if product exists
    echo "2. Checking test product...\n";
    $produk = Produk::first();
    
    if (!$produk) {
        echo "❌ No products found for testing\n";
        exit(1);
    }
    
    echo "✅ Test product: {$produk->nama_produk} (ID: {$produk->id_produk})\n\n";
    
    // 3. Check controller method exists
    echo "3. Checking controller method...\n";
    $controller = new \App\Http\Controllers\ProdukController();
    
    if (method_exists($controller, 'addStock')) {
        echo "✅ Controller method addStock exists\n\n";
    } else {
        echo "❌ Controller method addStock not found\n";
        exit(1);
    }
    
    // 4. Test URL generation
    echo "4. Testing URL generation...\n";
    try {
        $url = route('admin.inventaris.produk.add-stock', ['productId' => $produk->id_produk]);
        echo "✅ Generated URL: {$url}\n\n";
    } catch (Exception $e) {
        echo "❌ URL generation failed: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    // 5. Check permissions
    echo "5. Checking permissions...\n";
    $user = User::first();
    if ($user) {
        echo "✅ Test user found: {$user->name}\n";
        // Check if user has permission (if using Spatie permission package)
        if (method_exists($user, 'hasPermissionTo')) {
            $hasPermission = $user->hasPermissionTo('inventaris.produk.edit');
            echo "   Has edit permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "⚠️  No users found\n";
    }
    
    echo "\n6. Expected request format:\n";
    echo "POST {$url}\n";
    echo "Headers:\n";
    echo "  Content-Type: application/json\n";
    echo "  X-CSRF-TOKEN: [token]\n";
    echo "  Accept: application/json\n";
    echo "Body:\n";
    echo "  {\n";
    echo "    \"jumlah\": 10,\n";
    echo "    \"hpp\": 50000\n";
    echo "  }\n\n";
    
    echo "7. Common issues to check:\n";
    echo "   - CSRF token mismatch\n";
    echo "   - User not authenticated\n";
    echo "   - User lacks permission\n";
    echo "   - Product ID not found\n";
    echo "   - Invalid JSON format\n";
    echo "   - Missing required headers\n\n";
    
    echo "=== TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}