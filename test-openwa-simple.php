<?php
/**
 * Simple test untuk OpenWA integration
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing OpenWA Integration...\n\n";

// Test 1: Check if service can be instantiated
echo "1. Creating WhatsAppService instance...\n";
try {
    $whatsappService = new App\Services\WhatsAppService();
    echo "   ✅ Service created successfully\n\n";
} catch (\Exception $e) {
    echo "   ❌ Failed to create service: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check if server is ready
echo "2. Checking OpenWA server status...\n";
$isReady = $whatsappService->isReady();
if ($isReady) {
    echo "   ✅ OpenWA server is READY\n\n";
} else {
    echo "   ❌ OpenWA server is NOT READY\n";
    echo "   Make sure OpenWA server is running: cd openwa-server && npm start\n\n";
}

// Test 3: Get detailed status
echo "3. Getting detailed status...\n";
$status = $whatsappService->getStatus();
echo "   Connected: " . ($status['connected'] ? 'YES' : 'NO') . "\n";
echo "   State: " . ($status['state'] ?? 'unknown') . "\n";
if (isset($status['battery'])) {
    echo "   Battery: " . $status['battery'] . "%\n";
}
if (isset($status['error'])) {
    echo "   Error: " . $status['error'] . "\n";
}
echo "\n";

// Test 4: Test send message (optional)
echo "4. Do you want to test sending a message? (y/n): ";
$answer = trim(fgets(STDIN));

if (strtolower($answer) === 'y') {
    echo "   Enter phone number (e.g., 08123456789): ";
    $phone = trim(fgets(STDIN));
    
    if (!empty($phone)) {
        $message = "🧪 Test dari HM Tour\n\nIni adalah test message untuk memastikan OpenWA berfungsi.\n\nTimestamp: " . date('Y-m-d H:i:s');
        
        echo "   Sending message to $phone...\n";
        $result = $whatsappService->sendMessage($phone, $message);
        
        if ($result['success']) {
            echo "   ✅ Message sent successfully!\n";
            echo "   Message ID: " . ($result['messageId'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Failed to send message\n";
            echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
            if (isset($result['fallback_url'])) {
                echo "   Fallback URL: " . $result['fallback_url'] . "\n";
            }
        }
    }
}

echo "\n";
echo "========================================\n";
echo "Test Complete!\n";
echo "========================================\n";
