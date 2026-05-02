<?php

/**
 * Test script untuk memverifikasi fix autentikasi POS
 */

echo "🧪 Testing POS authentication fix...\n";

// Test 1: Cek apakah routes ada
echo "1. Checking routes...\n";
$routes = shell_exec("php artisan route:list --name=pos");
if (strpos($routes, "pos.products") !== false) {
    echo "   ✅ Route pos.products found\n";
} else {
    echo "   ❌ Route pos.products not found\n";
}

if (strpos($routes, "admin.penjualan.pos.products") !== false) {
    echo "   ✅ Route admin.penjualan.pos.products found\n";
} else {
    echo "   ❌ Route admin.penjualan.pos.products not found\n";
}

// Test 2: Test endpoint dengan curl
echo "2. Testing endpoints...\n";
$baseUrl = env("APP_URL", "http://localhost");

// Test products endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/admin/penjualan/pos/products?outlet_id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Products endpoint HTTP code: $httpCode\n";
if ($httpCode == 200) {
    echo "   ✅ Products endpoint accessible\n";
} elseif ($httpCode == 401) {
    echo "   ⚠️  Products endpoint requires authentication (expected)\n";
} elseif ($httpCode == 302) {
    echo "   ⚠️  Products endpoint redirects (likely to login - expected)\n";
} else {
    echo "   ❌ Products endpoint error: $httpCode\n";
}

echo "🎯 Fix completed! Please test in browser:\n";
echo "   1. Login to admin panel\n";
echo "   2. Go to POS page\n";
echo "   3. Check if products load without 401 error\n";
