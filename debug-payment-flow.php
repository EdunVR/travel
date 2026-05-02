<?php
/**
 * Debug payment flow untuk melihat apa yang terjadi
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "Debug Payment Flow\n";
echo "========================================\n\n";

// Check config
echo "1. Checking Laravel config...\n";
$openwaUrl = config('services.openwa.url');
$openwaApiKey = config('services.openwa.api_key');
$openwaTimeout = config('services.openwa.timeout');

echo "   OPENWA_URL: " . ($openwaUrl ?? 'NOT SET') . "\n";
echo "   OPENWA_API_KEY: " . ($openwaApiKey ? '***' . substr($openwaApiKey, -4) : 'NOT SET') . "\n";
echo "   OPENWA_TIMEOUT: " . ($openwaTimeout ?? 'NOT SET') . "\n\n";

// Check .env
echo "2. Checking .env values...\n";
echo "   OPENWA_URL from env: " . (env('OPENWA_URL') ?? 'NOT SET') . "\n";
echo "   OPENWA_API_KEY from env: " . (env('OPENWA_API_KEY') ? '***' . substr(env('OPENWA_API_KEY'), -4) : 'NOT SET') . "\n\n";

// Test WhatsAppService
echo "3. Testing WhatsAppService...\n";
try {
    $service = new App\Services\WhatsAppService();
    echo "   ✅ Service instantiated\n";
    
    // Test isReady
    echo "   Testing isReady()...\n";
    $isReady = $service->isReady();
    echo "   Result: " . ($isReady ? 'READY' : 'NOT READY') . "\n\n";
    
    // Test getStatus
    echo "   Testing getStatus()...\n";
    $status = $service->getStatus();
    echo "   Success: " . ($status['success'] ? 'YES' : 'NO') . "\n";
    echo "   Connected: " . ($status['connected'] ?? 'unknown') . "\n";
    if (isset($status['error'])) {
        echo "   Error: " . $status['error'] . "\n";
    }
    echo "\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n\n";
}

// Test direct HTTP call
echo "4. Testing direct HTTP call to OpenWA...\n";
try {
    $response = \Illuminate\Support\Facades\Http::timeout(5)->get('http://localhost:3000/health');
    echo "   Status: " . $response->status() . "\n";
    echo "   Body: " . $response->body() . "\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "Debug Complete\n";
echo "========================================\n";
