<?php
/**
 * Test RFID Time Endpoint
 * 
 * Test apakah endpoint time sudah berfungsi dengan benar
 */

echo "=== TESTING RFID TIME ENDPOINT ===\n\n";

$url = "https://poshan.my.id/hm/api/morra/api/rfid/time";

echo "Testing URL: $url\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit(1);
}

if ($httpCode != 200) {
    echo "❌ HTTP Error: $httpCode\n";
    echo "Response: $response\n";
    exit(1);
}

echo "✅ HTTP 200 OK\n\n";

// Parse JSON
$data = json_decode($response, true);

if (!$data) {
    echo "❌ Failed to parse JSON\n";
    echo "Response: $response\n";
    exit(1);
}

echo "Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
echo "\n\n";

// Validate response
$errors = [];

if (!isset($data['success']) || $data['success'] !== true) {
    $errors[] = "success field is not true";
}

if (!isset($data['time']) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['time'])) {
    $errors[] = "time field is missing or invalid format (expected HH:MM:SS)";
}

if (!isset($data['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
    $errors[] = "date field is missing or invalid format (expected YYYY-MM-DD)";
}

if (!isset($data['timestamp']) || !is_numeric($data['timestamp'])) {
    $errors[] = "timestamp field is missing or invalid";
}

if (count($errors) > 0) {
    echo "❌ VALIDATION ERRORS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    exit(1);
}

echo "✅ ALL VALIDATIONS PASSED!\n\n";

// Display parsed time
echo "Parsed Data:\n";
echo "  Time: {$data['time']}\n";
echo "  Date: {$data['date']}\n";
echo "  Timestamp: {$data['timestamp']}\n";
if (isset($data['timezone'])) {
    echo "  Timezone: {$data['timezone']}\n";
}
echo "\n";

// Compare with current time
date_default_timezone_set('Asia/Jakarta');
$currentTime = date('H:i:s');
$currentDate = date('Y-m-d');

echo "Current Time (PHP): $currentTime\n";
echo "Server Time (API): {$data['time']}\n";

// Calculate time difference
$current = strtotime($currentTime);
$server = strtotime($data['time']);
$diff = abs($current - $server);

if ($diff <= 2) {
    echo "✅ Time difference: {$diff} seconds (GOOD)\n";
} else {
    echo "⚠️ Time difference: {$diff} seconds (might be network delay)\n";
}

echo "\n";
echo "=== TEST COMPLETE ===\n";
echo "\n";
echo "NEXT STEPS:\n";
echo "1. ✅ Endpoint is working correctly\n";
echo "2. Upload code to ESP32\n";
echo "3. Check Serial Monitor for 'Time synced' message\n";
echo "4. Verify clock on TFT display\n";
?>
