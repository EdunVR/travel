<?php

/**
 * Test RFID Integration API
 * 
 * This script tests all RFID-related API endpoints
 */

require_once 'vendor/autoload.php';

$baseUrl = 'http://localhost/tofu';
$apiBase = $baseUrl . '/api/morra/api/rfid';

echo "=== TESTING RFID INTEGRATION API ===\n\n";

// Test 1: Get current RFID mode
echo "[TEST 1] Getting current RFID mode...\n";
$response = file_get_contents($apiBase . '/mode');
$result = json_decode($response, true);

if ($result && $result['success']) {
    echo "✅ Current mode: " . $result['mode'] . "\n";
} else {
    echo "❌ Failed to get mode\n";
}
echo "\n";

// Test 2: Set RFID mode to register
echo "[TEST 2] Setting RFID mode to 'register'...\n";
$postData = json_encode(['mode' => 'register']);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$response = file_get_contents($apiBase . '/mode', false, $context);
$result = json_decode($response, true);

if ($result && $result['success']) {
    echo "✅ Mode set to: " . $result['mode'] . "\n";
} else {
    echo "❌ Failed to set mode\n";
}
echo "\n";

// Test 3: Simulate card detection
echo "[TEST 3] Simulating card detection...\n";
$testUID = "AB CD EF 12";
$postData = json_encode(['uid' => $testUID]);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$response = file_get_contents($apiBase . '/card-detected', false, $context);
$result = json_decode($response, true);

if ($result && $result['success']) {
    echo "✅ Card detection successful\n";
    echo "   Action: " . $result['action'] . "\n";
    echo "   Message: " . $result['message'] . "\n";
    if (isset($result['mode_changed_to'])) {
        echo "   Mode changed to: " . $result['mode_changed_to'] . "\n";
    }
} else {
    echo "❌ Card detection failed\n";
    if ($result && isset($result['message'])) {
        echo "   Error: " . $result['message'] . "\n";
    }
}
echo "\n";

// Test 4: Check detected UID endpoint
echo "[TEST 4] Checking detected UID endpoint...\n";
$response = file_get_contents($baseUrl . '/api/detected-rfid-uid');
$result = json_decode($response, true);

if ($result && $result['success'] && $result['uid']) {
    echo "✅ Detected UID: " . $result['uid'] . "\n";
} else {
    echo "ℹ️ No UID detected (this is normal if no card was just detected)\n";
}
echo "\n";

// Test 5: Reset mode back to attendance
echo "[TEST 5] Resetting mode back to 'attendance'...\n";
$postData = json_encode(['mode' => 'attendance']);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$response = file_get_contents($apiBase . '/mode', false, $context);
$result = json_decode($response, true);

if ($result && $result['success']) {
    echo "✅ Mode reset to: " . $result['mode'] . "\n";
} else {
    echo "❌ Failed to reset mode\n";
}
echo "\n";

echo "=== TESTING COMPLETE ===\n";
echo "\nNOTES:\n";
echo "- Make sure your Laravel server is running\n";
echo "- Update \$baseUrl if your server is on different host/port\n";
echo "- ESP32 CAM should be able to access these same endpoints\n";
echo "- Test with actual RFID cards using ESP32 CAM hardware\n";

?>