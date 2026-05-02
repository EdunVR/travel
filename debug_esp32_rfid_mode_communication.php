<?php

echo "=== ESP32-CAM RFID Mode Communication Debug ===\n\n";

// Test Laravel API endpoints
$baseUrl = 'https://poshan.my.id/tofu';

echo "1. TESTING LARAVEL API ENDPOINTS\n";
echo "================================\n\n";

// Test GET mode endpoint
echo "Testing GET /api/morra/api/rfid/mode:\n";
$getModeUrl = $baseUrl . '/api/morra/api/rfid/mode';
echo "URL: $getModeUrl\n";

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
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n\n";

// Test POST mode endpoint
echo "Testing POST /api/morra/api/rfid/mode (set to register):\n";
$postModeUrl = $baseUrl . '/api/morra/api/rfid/mode';
echo "URL: $postModeUrl\n";

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
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n\n";

// Test GET mode again to verify change
echo "Testing GET /api/morra/api/rfid/mode (after setting to register):\n";
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
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n\n";

echo "2. ESP32-CAM CONFIGURATION CHECK\n";
echo "=================================\n\n";

echo "ESP32-CAM should be configured with:\n";
echo "serverURL = \"https://poshan.my.id/tofu\"\n";
echo "apiEndpoint = \"/api/morra/api/rfid\"\n\n";

echo "Full URLs ESP32 should call:\n";
echo "- Mode Check: https://poshan.my.id/tofu/api/morra/api/rfid/mode\n";
echo "- Card Detected: https://poshan.my.id/tofu/api/morra/api/rfid/card-detected\n\n";

echo "3. DEBUGGING STEPS\n";
echo "==================\n\n";

echo "1. Check ESP32 Serial Monitor for:\n";
echo "   - WiFi connection status\n";
echo "   - HTTP response codes when checking mode\n";
echo "   - JSON parsing errors\n\n";

echo "2. Check Laravel logs:\n";
echo "   - tail -f storage/logs/laravel.log\n";
echo "   - Look for API requests from ESP32\n\n";

echo "3. Test manually:\n";
echo "   - Set mode to 'register' via web interface\n";
echo "   - Monitor ESP32 serial for mode change\n";
echo "   - Should see: '🔄 Mode berubah ke: register'\n\n";

echo "4. Common issues:\n";
echo "   - SSL certificate problems (ESP32 uses setInsecure())\n";
echo "   - Network connectivity\n";
echo "   - JSON parsing errors\n";
echo "   - Cache not working properly\n\n";

echo "5. Expected ESP32 Serial Output:\n";
echo "   Every 2 seconds should show mode check\n";
echo "   When mode changes: '🔄 Mode berubah ke: register'\n";
echo "   LED should flash 3 times on mode change\n\n";

?>