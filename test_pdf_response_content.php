<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTING PDF RESPONSE CONTENT\n";
echo "===============================\n\n";

// Get a test transaction
$transaction = \App\Models\InterOutletSale::orderBy('id', 'desc')->first();

if (!$transaction) {
    echo "❌ No transactions found\n";
    exit;
}

echo "📋 Testing transaction ID: {$transaction->id}\n\n";

// Test the URL that JavaScript is using
$baseUrl = config('app.url');
$testUrl = "{$baseUrl}/admin/penjualan/inter-outlet-sale/{$transaction->id}/print";

echo "🌐 Testing URL: {$testUrl}\n\n";

// Make request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "📊 Response Details:\n";
echo "   HTTP Code: {$httpCode}\n";
echo "   Content Type: {$contentType}\n";
echo "   Response Length: " . strlen($response) . " bytes\n\n";

// Check if it's a redirect to login
if (strpos($response, 'login') !== false || strpos($response, 'Login') !== false) {
    echo "🔐 ISSUE FOUND: Response contains login page\n";
    echo "   This means the user is not authenticated\n";
    echo "   The PDF URL requires authentication\n\n";
}

// Check if it's an error page
if (strpos($response, 'error') !== false || strpos($response, 'Error') !== false || strpos($response, '404') !== false) {
    echo "❌ ISSUE FOUND: Response contains error\n";
    echo "   Response preview:\n";
    echo "   " . substr($response, 0, 500) . "...\n\n";
}

// Check if it's HTML instead of PDF
if (strpos($response, '<html') !== false || strpos($response, '<!DOCTYPE') !== false) {
    echo "🌐 ISSUE FOUND: Response is HTML instead of PDF\n";
    
    // Extract title if available
    if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
        echo "   Page Title: {$matches[1]}\n";
    }
    
    // Check for specific error messages
    if (strpos($response, 'Unauthenticated') !== false) {
        echo "   ❌ Authentication required\n";
    } elseif (strpos($response, 'Unauthorized') !== false) {
        echo "   ❌ Authorization failed\n";
    } elseif (strpos($response, 'Not Found') !== false) {
        echo "   ❌ Route not found\n";
    } else {
        echo "   ℹ️  Unknown HTML response\n";
    }
    
    echo "\n   HTML Preview (first 300 chars):\n";
    echo "   " . substr($response, 0, 300) . "...\n\n";
}

echo "💡 SOLUTION:\n";
echo "   The PDF URL works but requires authentication.\n";
echo "   When accessed from the modal, the user should be logged in.\n";
echo "   The issue might be:\n";
echo "   1. Session not being passed to iframe\n";
echo "   2. CSRF token missing\n";
echo "   3. Authentication middleware blocking the request\n\n";

echo "🔧 NEXT STEPS:\n";
echo "   1. Test the URL while logged in to the admin panel\n";
echo "   2. Check if the modal iframe passes authentication cookies\n";
echo "   3. Verify the route doesn't require additional middleware\n";

echo "\n";