<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTING PDF URL DIRECT ACCESS\n";
echo "================================\n\n";

// Get a test transaction
$transaction = \App\Models\InterOutletSale::orderBy('id', 'desc')->first();

if (!$transaction) {
    echo "❌ No transactions found\n";
    exit;
}

echo "📋 Testing transaction ID: {$transaction->id}\n";
echo "   No Transaksi: {$transaction->no_transaksi}\n";
echo "   Status: {$transaction->status}\n\n";

// Test URLs
$baseUrl = config('app.url');
$url1 = "{$baseUrl}/admin/penjualan/inter-outlet/{$transaction->id}/print";
$url2 = "{$baseUrl}/admin/penjualan/inter-outlet-sale/{$transaction->id}/print";

echo "🌐 Testing URLs:\n";
echo "   URL 1: {$url1}\n";
echo "   URL 2: {$url2}\n\n";

// Function to test URL
function testUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    // Add session cookie if available
    $sessionName = config('session.cookie');
    if (isset($_COOKIE[$sessionName])) {
        curl_setopt($ch, CURLOPT_COOKIE, "{$sessionName}={$_COOKIE[$sessionName]}");
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'content_type' => $contentType,
        'response' => $response,
        'error' => $error,
        'response_length' => strlen($response)
    ];
}

// Test both URLs
echo "🧪 Testing URL 1:\n";
$result1 = testUrl($url1);
echo "   HTTP Code: {$result1['http_code']}\n";
echo "   Content Type: {$result1['content_type']}\n";
echo "   Response Length: {$result1['response_length']} bytes\n";
if ($result1['error']) {
    echo "   Error: {$result1['error']}\n";
}
if ($result1['http_code'] !== 200) {
    echo "   Response Preview: " . substr($result1['response'], 0, 200) . "...\n";
}

echo "\n🧪 Testing URL 2:\n";
$result2 = testUrl($url2);
echo "   HTTP Code: {$result2['http_code']}\n";
echo "   Content Type: {$result2['content_type']}\n";
echo "   Response Length: {$result2['response_length']} bytes\n";
if ($result2['error']) {
    echo "   Error: {$result2['error']}\n";
}
if ($result2['http_code'] !== 200) {
    echo "   Response Preview: " . substr($result2['response'], 0, 200) . "...\n";
}

echo "\n📊 ANALYSIS:\n";
if ($result1['http_code'] === 200 && strpos($result1['content_type'], 'pdf') !== false) {
    echo "   ✅ URL 1 works - returns PDF\n";
} else {
    echo "   ❌ URL 1 failed - HTTP {$result1['http_code']}\n";
}

if ($result2['http_code'] === 200 && strpos($result2['content_type'], 'pdf') !== false) {
    echo "   ✅ URL 2 works - returns PDF\n";
} else {
    echo "   ❌ URL 2 failed - HTTP {$result2['http_code']}\n";
}

echo "\n💡 RECOMMENDATION:\n";
if ($result2['http_code'] === 200) {
    echo "   Use URL 2: /admin/penjualan/inter-outlet-sale/{id}/print\n";
} elseif ($result1['http_code'] === 200) {
    echo "   Use URL 1: /admin/penjualan/inter-outlet/{id}/print\n";
} else {
    echo "   Both URLs failed - check authentication and permissions\n";
}

echo "\n";