<?php

/**
 * Manual Test RFID API - Debugging Version
 */

$baseUrl = 'https://poshan.my.id/tofu';
$apiBase = $baseUrl . '/api/morra/api/rfid';

echo "=== MANUAL RFID API TESTING ===\n\n";

// Test 1: Test basic connectivity
echo "[TEST 1] Testing basic connectivity...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
        'header' => [
            'User-Agent: ESP32-CAM-RFID/1.0',
            'Accept: application/json'
        ]
    ]
]);

$testUrl = $apiBase . '/mode';
echo "Testing URL: $testUrl\n";

$response = @file_get_contents($testUrl, false, $context);
if ($response === false) {
    echo "❌ Connection failed\n";
    $error = error_get_last();
    echo "Error: " . $error['message'] . "\n";
} else {
    echo "✅ Connection successful\n";
    echo "Response: $response\n";
}
echo "\n";

// Test 2: Test with cURL (more detailed)
echo "[TEST 2] Testing with cURL (detailed)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'ESP32-CAM-RFID/1.0');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
if ($response) {
    echo "Response: $response\n";
}

curl_close($ch);
echo "\n";

// Test 3: Test POST method
echo "[TEST 3] Testing POST method (set mode)...\n";
$postData = json_encode(['mode' => 'register']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'ESP32-CAM-RFID/1.0');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
if ($response) {
    echo "Response: $response\n";
}

curl_close($ch);
echo "\n";

// Test 4: Test card detection
echo "[TEST 4] Testing card detection...\n";
$cardUrl = $apiBase . '/card-detected';
$cardData = json_encode(['uid' => 'TEST123']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $cardData);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'ESP32-CAM-RFID/1.0');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
if ($response) {
    echo "Response: $response\n";
}

curl_close($ch);
echo "\n";

echo "=== TESTING COMPLETE ===\n";
echo "\nDEBUG INFO:\n";
echo "- Base URL: $baseUrl\n";
echo "- API Base: $apiBase\n";
echo "- Mode URL: $testUrl\n";
echo "- Card URL: $cardUrl\n";

?>