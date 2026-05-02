<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING POS IMAGE URL ISSUE ===\n\n";

// 1. Test current URL generation
echo "1. Testing current URL generation...\n";
$testPath = "product_images/KBX7VpqHNuIbTyOl4Pz1PmrkIWSV1OFqoEeYAoqQ.jpg";

$currentUrl = asset('storage/' . $testPath);
echo "Current URL: {$currentUrl}\n";

// 2. Test proper URL generation
echo "\n2. Testing proper URL generation methods...\n";

// Method 1: Using Storage facade
$storageUrl = \Illuminate\Support\Facades\Storage::url($testPath);
echo "Storage facade URL: {$storageUrl}\n";

// Method 2: Using route with proper path
$routeUrl = url('/storage/' . $testPath);
echo "Route URL: {$routeUrl}\n";

// Method 3: Check if storage link exists
echo "\n3. Checking storage symlink...\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath)) {
    echo "✅ Storage symlink exists\n";
    echo "Target: " . readlink($publicStoragePath) . "\n";
} else {
    echo "❌ Storage symlink missing\n";
    echo "Need to run: php artisan storage:link\n";
}

// 4. Check if file exists
echo "\n4. Checking file existence...\n";
$fullPath = storage_path('app/public/' . $testPath);
if (file_exists($fullPath)) {
    echo "✅ File exists: {$fullPath}\n";
} else {
    echo "❌ File not found: {$fullPath}\n";
    
    // Check alternative paths
    $altPath1 = public_path('storage/' . $testPath);
    $altPath2 = storage_path('app/' . $testPath);
    
    if (file_exists($altPath1)) {
        echo "✅ Found in public/storage: {$altPath1}\n";
    } elseif (file_exists($altPath2)) {
        echo "✅ Found in storage/app: {$altPath2}\n";
    } else {
        echo "❌ File not found in any location\n";
    }
}

// 5. Test URL accessibility
echo "\n5. Testing URL accessibility...\n";
$testUrls = [
    asset('storage/' . $testPath),
    url('/storage/' . $testPath),
    \Illuminate\Support\Facades\Storage::url($testPath)
];

foreach ($testUrls as $i => $url) {
    echo "URL " . ($i + 1) . ": {$url}\n";
    
    // Test with cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: {$httpCode}\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

// Recommendation
echo "\nRecommendation:\n";
if (is_link($publicStoragePath)) {
    echo "✅ Use Storage::url() method for proper URL generation\n";
} else {
    echo "❌ Run 'php artisan storage:link' first\n";
    echo "✅ Then use Storage::url() method\n";
}

echo "\nProposed fix:\n";
echo "Replace: asset('storage/' . \$product->image_path)\n";
echo "With: Storage::url(\$product->image_path)\n";