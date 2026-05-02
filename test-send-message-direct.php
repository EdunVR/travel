<?php
/**
 * Test send message directly
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "Test Send Message Direct\n";
echo "========================================\n\n";

$service = new App\Services\WhatsAppService();

echo "Enter phone number (e.g., 08123456789): ";
$phone = trim(fgets(STDIN));

if (empty($phone)) {
    echo "Phone number required!\n";
    exit(1);
}

$message = "🧪 Test dari HM Tour\n\n";
$message .= "Ini adalah test message untuk memastikan OpenWA auto-send berfungsi.\n\n";
$message .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$message .= "Server: OpenWA\n\n";
$message .= "Jika Anda menerima pesan ini, berarti sistem sudah berfungsi! ✅";

echo "\nSending message to: $phone\n";
echo "Message length: " . strlen($message) . " characters\n\n";

$result = $service->sendMessage($phone, $message);

echo "Result:\n";
echo "  Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";

if ($result['success']) {
    echo "  Phone: " . ($result['phone'] ?? 'N/A') . "\n";
    echo "  Message ID: " . ($result['messageId'] ?? 'N/A') . "\n";
    echo "\n✅ Message sent successfully!\n";
} else {
    echo "  Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['fallback_url'])) {
        echo "  Fallback URL: " . $result['fallback_url'] . "\n";
    }
    echo "\n❌ Failed to send message\n";
}

echo "\n========================================\n";
