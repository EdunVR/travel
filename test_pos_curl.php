<?php

echo "=== TESTING POS API WITH CURL ===\n\n";

// Test URL dari console log
$url = "https://group.dahana-boiler.com/MORRA_POSHAN/admin/penjualan/pos/products?outlet_id=1";

echo "Testing URL: {$url}\n\n";

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

// Add headers to simulate browser request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json, text/plain, */*',
    'Accept-Language: en-US,en;q=0.9',
    'Cache-Control: no-cache',
    'Pragma: no-cache'
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: {$httpCode}\n";

if ($error) {
    echo "cURL Error: {$error}\n";
} else {
    echo "Response Length: " . strlen($response) . " bytes\n";
    echo "Response:\n";
    echo $response . "\n";
    
    // Try to decode JSON
    $data = json_decode($response, true);
    if ($data) {
        echo "\nParsed JSON:\n";
        echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        if (isset($data['data'])) {
            echo "Data count: " . count($data['data']) . "\n";
            if (count($data['data']) > 0) {
                echo "First item keys: " . implode(', ', array_keys($data['data'][0])) . "\n";
            }
        }
    } else {
        echo "\nFailed to parse JSON. Raw response:\n";
        echo substr($response, 0, 500) . "...\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";