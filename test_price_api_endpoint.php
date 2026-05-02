<?php

echo "=== TESTING PRICE PRODUCTS API ENDPOINT ===\n\n";

// Test the API endpoint directly
$url = 'https://group.dahana-boiler.com/MORRA/admin/penjualan/inter-outlet/price-products?outlet_id=1';

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Add headers to simulate browser request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

echo "1. Testing API endpoint: $url\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($error) {
    echo "   ✗ cURL Error: $error\n";
} else {
    echo "   HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "   ✓ API endpoint accessible\n";
        
        $data = json_decode($response, true);
        if ($data) {
            echo "   Response structure:\n";
            echo "   - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            echo "   - Count: " . ($data['count'] ?? 'N/A') . "\n";
            echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
            
            if (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
                echo "   - Sample product: " . $data['data'][0]['name'] . "\n";
            }
        } else {
            echo "   Response (first 200 chars): " . substr($response, 0, 200) . "\n";
        }
    } else {
        echo "   ✗ HTTP Error $httpCode\n";
        echo "   Response (first 200 chars): " . substr($response, 0, 200) . "\n";
    }
}

echo "\n✓ API endpoint test completed\n";