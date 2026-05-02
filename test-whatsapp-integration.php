<?php
/**
 * Test WhatsApp Integration dengan OpenWA
 * 
 * Usage: php test-whatsapp-integration.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "Testing WhatsApp Integration\n";
echo "========================================\n\n";

// Test 1: Check OpenWA server health
echo "1. Checking OpenWA server health...\n";
$whatsappService = new App\Services\WhatsAppService();

if ($whatsappService->isReady()) {
    echo "   ✅ OpenWA server is READY\n\n";
} else {
    echo "   ❌ OpenWA server is NOT READY\n";
    echo "   Please start OpenWA server first:\n";
    echo "   cd openwa-server && npm start\n\n";
    exit(1);
}

// Test 2: Get status
echo "2. Getting connection status...\n";
$status = $whatsappService->getStatus();
if ($status['success'] && $status['connected']) {
    echo "   ✅ Connected to WhatsApp\n";
    echo "   State: " . ($status['state'] ?? 'unknown') . "\n";
    echo "   Battery: " . ($status['battery'] ?? 'unknown') . "%\n\n";
} else {
    echo "   ❌ Not connected to WhatsApp\n";
    echo "   Error: " . ($status['error'] ?? 'Unknown error') . "\n\n";
    exit(1);
}

// Test 3: Send test message
echo "3. Sending test message...\n";
echo "   Enter phone number (e.g., 08123456789): ";
$phone = trim(fgets(STDIN));

if (empty($phone)) {
    echo "   ❌ Phone number is required\n";
    exit(1);
}

$message = "🧪 Test message dari HM Tour & Travel\n\n";
$message .= "Ini adalah test message untuk memastikan integrasi WhatsApp berfungsi dengan baik.\n\n";
$message .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$message .= "Server: OpenWA\n\n";
$message .= "Jika Anda menerima pesan ini, berarti sistem WhatsApp auto-send sudah berfungsi! ✅";

echo "   Sending to: $phone\n";
$result = $whatsappService->sendMessage($phone, $message);

if ($result['success']) {
    echo "   ✅ Message sent successfully!\n";
    echo "   Phone: " . ($result['phone'] ?? $phone) . "\n";
    echo "   Message ID: " . ($result['messageId'] ?? 'N/A') . "\n\n";
} else {
    echo "   ❌ Failed to send message\n";
    echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    
    if (isset($result['fallback_url'])) {
        echo "   Fallback URL: " . $result['fallback_url'] . "\n";
    }
    echo "\n";
    exit(1);
}

echo "========================================\n";
echo "✅ All tests passed!\n";
echo "========================================\n\n";

echo "Next steps:\n";
echo "1. Test booking via website: " . env('APP_URL') . "\n";
echo "2. Complete payment and check if WhatsApp is sent automatically\n";
echo "3. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "4. Check OpenWA logs: pm2 logs openwa-hm-tour (if using PM2)\n\n";
