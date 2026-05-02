<?php

echo "=== Testing API Outlets Route ===\n\n";

// Test dengan curl
$url = 'http://localhost/tofu/api/outlets';

echo "Testing URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

if ($httpCode === 200) {
    echo "✅ API outlets route is working!\n";
    
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success']) {
        echo "✅ Response format is correct\n";
        echo "✅ Data count: " . count($data['data'] ?? []) . "\n";
    } else {
        echo "❌ Response format incorrect\n";
    }
} else {
    echo "❌ API outlets route failed with HTTP $httpCode\n";
    
    if (strpos($response, 'login') !== false || strpos($response, 'auth') !== false) {
        echo "⚠️ Authentication required - this is expected\n";
        echo "✅ Route exists but requires login\n";
    }
}

echo "\n=== Test Complete ===\n";