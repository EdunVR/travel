<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 TESTING ROUTE GENERATION\n";
echo "==========================\n\n";

try {
    $testId = 21;
    $routeUrl = route('admin.penjualan.inter-outlet-sale.print', $testId);
    echo "Generated URL: {$routeUrl}\n";
    
    $baseUrl = config('app.url');
    echo "Base URL: {$baseUrl}\n";
    
    if (strpos($routeUrl, '/tofu/') !== false) {
        echo "✅ SUCCESS: URL includes /tofu/ path\n";
    } else {
        echo "⚠️  WARNING: URL may not include /tofu/ path\n";
    }
    
    // Test with ID 0 (as used in JavaScript)
    $routeUrlZero = route('admin.penjualan.inter-outlet-sale.print', 0);
    echo "\nRoute with ID 0: {$routeUrlZero}\n";
    
    // Test replacement logic
    $replacedUrl = str_replace('/0/', "/{$testId}/", $routeUrlZero);
    echo "After replacement: {$replacedUrl}\n";
    
    if ($replacedUrl === $routeUrl) {
        echo "✅ Replacement logic works correctly\n";
    } else {
        echo "❌ Replacement logic has issues\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n";