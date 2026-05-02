<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PosController;
use Illuminate\Http\Request;
use App\Models\User;

echo "=== FINAL POS PERFORMANCE TEST ===\n\n";

// Login
$user = User::first();
if ($user) {
    Auth::login($user);
    
    // Test performance
    $controller = new PosController(app(\App\Services\JournalEntryService::class));
    $request = new Request(['outlet_id' => 1]);
    
    $start = microtime(true);
    $response = $controller->getProducts($request);
    $end = microtime(true);
    
    $time = round(($end - $start) * 1000, 2);
    $data = $response->getData(true);
    
    echo "Final performance test:\n";
    echo "- Execution time: {$time}ms\n";
    echo "- Products loaded: " . count($data['data']) . "\n";
    echo "- Response format: " . ($data['performance'] ?? 'standard') . "\n";
    
    if ($time < 50) {
        echo "\n✅ OPTIMIZATION SUCCESS!\n";
        echo "Performance target achieved: {$time}ms < 50ms\n";
    } else {
        echo "\n⚠️ Performance could be better\n";
        echo "Current: {$time}ms, Target: <50ms\n";
    }
    
    // Test cached performance
    $start = microtime(true);
    $response = $controller->getProducts($request);
    $end = microtime(true);
    
    $cachedTime = round(($end - $start) * 1000, 2);
    echo "\nCached performance: {$cachedTime}ms\n";
    
    if ($cachedTime < 10) {
        echo "✅ Cache performance excellent!\n";
    }
    
} else {
    echo "❌ No user found for testing\n";
}

echo "\n=== TEST COMPLETE ===\n";