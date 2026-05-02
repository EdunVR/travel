<?php

/**
 * Test Inter Outlet API Endpoints
 */

echo "🧪 Testing Inter Outlet API Endpoints...\n\n";

// Test dengan curl jika tersedia
function testEndpoint($url, $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        echo "Content Type: $contentType\n";
        
        if ($httpCode == 200) {
            if (strpos($contentType, 'application/json') !== false) {
                echo "✅ Response OK (JSON)\n";
                $data = json_decode($response, true);
                if ($data && isset($data['success'])) {
                    echo "✅ Valid JSON structure\n";
                } else {
                    echo "⚠️  JSON structure may be invalid\n";
                }
            } else {
                echo "❌ Response is not JSON (probably HTML)\n";
                echo "First 200 chars: " . substr($response, 0, 200) . "\n";
            }
        } else {
            echo "❌ HTTP Error: $httpCode\n";
            if ($response) {
                echo "Response: " . substr($response, 0, 200) . "\n";
            }
        }
    } else {
        echo "⚠️  cURL not available, cannot test endpoint\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Get base URL
$baseUrl = 'http://localhost';
if (isset($_SERVER['HTTP_HOST'])) {
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
} elseif (file_exists('.env')) {
    $env = file_get_contents('.env');
    if (preg_match('/APP_URL=(.+)/', $env, $matches)) {
        $baseUrl = trim($matches[1]);
    }
}

echo "Base URL: $baseUrl\n\n";

// Test endpoints
testEndpoint($baseUrl . '/admin/penjualan/inter-outlet/products?outlet_id=1', 'Products API');
testEndpoint($baseUrl . '/admin/penjualan/inter-outlet/outlets?current_outlet_id=1', 'Outlets API');

echo "📋 Catatan:\n";
echo "- Jika mendapat HTTP 401/403: masalah authentication\n";
echo "- Jika mendapat HTTP 404: route tidak ditemukan\n";
echo "- Jika mendapat HTML response: middleware redirect atau error page\n";
echo "- Jika mendapat JSON: endpoint berfungsi dengan baik\n\n";