<?php

echo "=== ESP32 Registration Mode Fix Test ===\n\n";

$baseUrl = 'https://poshan.my.id/tofu';

echo "1. TESTING MODE RESET AFTER REGISTRATION\n";
echo "========================================\n\n";

// Set mode to register
echo "Step 1: Setting mode to 'register'\n";
$postModeUrl = $baseUrl . '/api/morra/api/rfid/mode';
$postData = json_encode(['mode' => 'register']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postModeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: ESP32-CAM-RFID/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Simulate card detection
echo "Step 2: Simulating card detection in register mode\n";
$cardUrl = $baseUrl . '/api/morra/api/rfid/card-detected';
$cardData = json_encode([
    'uid' => '4A 8C C9 06',
    'photo' => 'test_photo_data'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $cardData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: ESP32-CAM-RFID/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Check if mode changed back to attendance
echo "Step 3: Checking if mode changed back to 'attendance'\n";
$getModeUrl = $baseUrl . '/api/morra/api/rfid/mode';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $getModeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: ESP32-CAM-RFID/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

$responseData = json_decode($response, true);
if ($responseData && $responseData['mode'] === 'attendance') {
    echo "✅ SUCCESS: Mode correctly changed back to 'attendance'\n";
} else {
    echo "❌ FAILED: Mode did not change back to 'attendance'\n";
}

echo "\n2. EXPECTED ESP32 BEHAVIOR\n";
echo "==========================\n\n";

echo "When ESP32 detects card in register mode:\n";
echo "1. Card detected: 🔍 Kartu terdeteksi - UID: 4A 8C C9 06\n";
echo "2. Photo captured: 📸 Photo captured: XXXX bytes\n";
echo "3. Data sent: 📤 Sending with photo (XXXX chars)\n";
echo "4. Server response: 📥 HTTP Response Code: 200\n";
echo "5. Registration success: ✅ Card ready for registration\n";
echo "6. Mode change: 🔄 Mode otomatis berubah ke: attendance\n";
echo "7. LED flashes: 5 green flashes + 3 mode change flashes\n\n";

echo "3. TROUBLESHOOTING HTTP ERROR -1\n";
echo "=================================\n\n";

echo "Common causes of HTTP Error -1:\n";
echo "- Network connectivity issues\n";
echo "- SSL handshake failures\n";
echo "- Server timeout\n";
echo "- Data payload too large\n";
echo "- Insufficient memory\n\n";

echo "Solutions implemented:\n";
echo "- Increased timeout to 20 seconds\n";
echo "- Added memory checks before HTTP\n";
echo "- Reduced photo size if memory low\n";
echo "- Better error reporting\n";
echo "- Watchdog resets during operation\n\n";

echo "4. TESTING CHECKLIST\n";
echo "=====================\n\n";

echo "[ ] Upload fixed ESP32 firmware\n";
echo "[ ] Set mode to 'register' via web interface\n";
echo "[ ] Tap RFID card\n";
echo "[ ] Check ESP32 serial for successful HTTP response\n";
echo "[ ] Verify mode changes back to 'attendance'\n";
echo "[ ] Test subsequent card taps work in attendance mode\n";
echo "[ ] Verify LED feedback patterns\n\n";

echo "The fix should resolve both the HTTP Error -1 and\n";
echo "the mode not resetting after registration.\n\n";

?>