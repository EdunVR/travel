<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PosController;
use App\Services\JournalEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "=== TESTING POS OPTIMIZATION ===\n\n";

// Login as user for authentication
$user = User::first();
if ($user) {
    Auth::login($user);
    echo "✅ Authenticated as: {$user->name}\n";
}

// Create controller instance
$controller = new PosController(app(JournalEntryService::class));

echo "\n1. Testing optimized getProducts performance...\n";

// Test multiple times to get average
$times = [];
$outletId = 1;

for ($i = 1; $i <= 5; $i++) {
    // Clear cache first
    \App\Services\CacheService::forget("pos_products_optimized_outlet_{$outletId}");
    
    $startTime = microtime(true);
    
    $request = new Request(['outlet_id' => $outletId]);
    $response = $controller->getProducts($request);
    $data = $response->getData(true);
    
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    
    $times[] = $executionTime;
    
    echo "Run {$i}: " . round($executionTime, 2) . "ms - Products: " . count($data['data']) . "\n";
}

$averageTime = array_sum($times) / count($times);
echo "\nAverage execution time: " . round($averageTime, 2) . "ms\n";

// Test cached performance
echo "\n2. Testing cached performance...\n";
$cachedTimes = [];

for ($i = 1; $i <= 3; $i++) {
    $startTime = microtime(true);
    
    $request = new Request(['outlet_id' => $outletId]);
    $response = $controller->getProducts($request);
    $data = $response->getData(true);
    
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000;
    
    $cachedTimes[] = $executionTime;
    
    echo "Cached run {$i}: " . round($executionTime, 2) . "ms\n";
}

$averageCachedTime = array_sum($cachedTimes) / count($cachedTimes);
echo "Average cached time: " . round($averageCachedTime, 2) . "ms\n";

// Test cache management
echo "\n3. Testing cache management...\n";

// Test clear cache
$request = new Request(['outlet_id' => $outletId]);
$clearResponse = $controller->clearProductsCache($request);
$clearData = $clearResponse->getData(true);
echo "Cache clear: " . ($clearData['success'] ? 'Success' : 'Failed') . "\n";
echo "Message: " . $clearData['message'] . "\n";

// Test warm cache
$warmResponse = $controller->warmProductsCache();
$warmData = $warmResponse->getData(true);
echo "Cache warm: " . ($warmData['success'] ? 'Success' : 'Failed') . "\n";
echo "Message: " . $warmData['message'] . "\n";

// Performance comparison
echo "\n4. Performance Summary:\n";
echo "┌─────────────────────────────────────────┐\n";
echo "│           PERFORMANCE RESULTS           │\n";
echo "├─────────────────────────────────────────┤\n";
echo "│ First load (no cache): " . str_pad(round($averageTime, 2) . "ms", 12) . " │\n";
echo "│ Cached load:           " . str_pad(round($averageCachedTime, 2) . "ms", 12) . " │\n";
echo "│ Improvement:           " . str_pad(round((1 - $averageCachedTime/$averageTime) * 100, 1) . "%", 12) . " │\n";
echo "└─────────────────────────────────────────┘\n";

// Test data integrity
echo "\n5. Testing data integrity...\n";
$request = new Request(['outlet_id' => $outletId]);
$response = $controller->getProducts($request);
$data = $response->getData(true);

if (count($data['data']) > 0) {
    $product = $data['data'][0];
    $requiredFields = ['id_produk', 'sku', 'name', 'category', 'price', 'stock', 'satuan'];
    
    echo "Sample product data:\n";
    foreach ($requiredFields as $field) {
        $value = $product[$field] ?? 'MISSING';
        echo "- {$field}: {$value}\n";
    }
    
    $missingFields = array_diff($requiredFields, array_keys($product));
    if (empty($missingFields)) {
        echo "✅ All required fields present\n";
    } else {
        echo "❌ Missing fields: " . implode(', ', $missingFields) . "\n";
    }
} else {
    echo "❌ No products returned\n";
}

echo "\n=== OPTIMIZATION TEST COMPLETE ===\n";