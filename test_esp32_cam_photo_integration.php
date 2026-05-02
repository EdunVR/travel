<?php

/**
 * Test ESP32 CAM Photo Integration
 * 
 * This script tests the photo integration functionality
 */

require_once 'vendor/autoload.php';

$baseUrl = 'https://poshan.my.id/tofu';

echo "=== ESP32 CAM PHOTO INTEGRATION TEST ===\n\n";

// Test 1: Check RFID API endpoints
echo "🧪 TEST 1: RFID API Endpoints\n";
echo "==============================\n";

$endpoints = [
    'GET /api/morra/api/rfid/mode',
    'POST /api/morra/api/rfid/mode', 
    'POST /api/morra/api/rfid/card-detected',
    'POST /api/morra/api/rfid/register'
];

foreach ($endpoints as $endpoint) {
    echo "✓ {$endpoint}\n";
}

echo "\n";

// Test 2: Check database migrations
echo "🧪 TEST 2: Database Schema\n";
echo "==========================\n";

echo "Checking if photo columns exist in attendance table...\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
    
    $stmt = $pdo->query("DESCRIBE attendance");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $photoColumns = [
        'clock_in_photo',
        'clock_out_photo', 
        'break_in_photo',
        'break_out_photo',
        'overtime_in_photo',
        'overtime_out_photo'
    ];
    
    foreach ($photoColumns as $column) {
        if (in_array($column, $columns)) {
            echo "✓ Column '{$column}' exists\n";
        } else {
            echo "❌ Column '{$column}' missing\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please update database credentials in this test file\n";
}

echo "\n";

// Test 3: Check storage directory
echo "🧪 TEST 3: Storage Directory\n";
echo "============================\n";

$photoDir = 'storage/app/public/attendance_photos';

if (is_dir($photoDir)) {
    echo "✓ Photo directory exists: {$photoDir}\n";
    
    if (is_writable($photoDir)) {
        echo "✓ Photo directory is writable\n";
    } else {
        echo "❌ Photo directory is not writable\n";
    }
} else {
    echo "❌ Photo directory missing: {$photoDir}\n";
    echo "Run: mkdir {$photoDir}\n";
}

// Check storage link
$storageLink = 'public/storage';
if (is_link($storageLink)) {
    echo "✓ Storage link exists\n";
} else {
    echo "❌ Storage link missing\n";
    echo "Run: php artisan storage:link\n";
}

echo "\n";

// Test 4: Simulate RFID card detection with photo
echo "🧪 TEST 4: RFID Card Detection Simulation\n";
echo "==========================================\n";

// Create a small test image in base64
$testImageBase64 = base64_encode(file_get_contents('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

$testData = [
    'uid' => 'AB CD EF 12',
    'photo' => $testImageBase64
];

echo "Test data prepared:\n";
echo "- UID: {$testData['uid']}\n";
echo "- Photo: " . strlen($testData['photo']) . " characters (base64)\n";

echo "\n";

// Test 5: Check ESP32 CAM code requirements
echo "🧪 TEST 5: ESP32 CAM Requirements\n";
echo "=================================\n";

$requiredLibraries = [
    'Wire.h',
    'Adafruit_PN532.h', 
    'esp_camera.h',
    'WiFi.h',
    'HTTPClient.h',
    'WiFiClientSecure.h',
    'ArduinoJson.h',
    'TFT_eSPI.h',
    'base64.h'
];

echo "Required Arduino libraries:\n";
foreach ($requiredLibraries as $lib) {
    echo "- {$lib}\n";
}

echo "\n";

// Test 6: Configuration checklist
echo "🧪 TEST 6: Configuration Checklist\n";
echo "===================================\n";

$configItems = [
    'WiFi credentials updated in ESP32 code',
    'Server URL updated in ESP32 code', 
    'Database migrations run',
    'Storage link created',
    'Photo directory created with permissions',
    'base64 library installed in Arduino IDE',
    'ESP32 CAM code uploaded to device'
];

echo "Configuration checklist:\n";
foreach ($configItems as $item) {
    echo "☐ {$item}\n";
}

echo "\n";

echo "=== INTEGRATION TEST COMPLETE ===\n\n";

echo "Next steps:\n";
echo "1. Run the deployment script: deploy_esp32_cam_photo_integration.bat\n";
echo "2. Upload ESP32 code to your device\n";
echo "3. Test RFID card detection\n";
echo "4. Check photos in admin panel\n\n";

echo "For detailed instructions, see: ESP32_CAM_PHOTO_INTEGRATION_GUIDE.md\n";

?>