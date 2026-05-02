<?php

/**
 * Test Fonnte WhatsApp API
 * 
 * Usage: php test-fonnte-api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FONNTE WHATSAPP API TEST ===\n\n";

// Get configuration
$token = env('FONNTE_TOKEN');
$adminPhone = env('ADMIN_WHATSAPP', '628976688800');

echo "Configuration:\n";
echo "Token: " . ($token ? substr($token, 0, 10) . '...' : 'NOT SET') . "\n";
echo "Admin Phone: $adminPhone\n\n";

if (!$token) {
    echo "❌ ERROR: FONNTE_TOKEN not configured in .env\n";
    echo "Please add: FONNTE_TOKEN=your_token_here\n";
    exit(1);
}

// Test message
$message = "*TEST MESSAGE FROM HM TOUR*\n\n";
$message .= "Halo! Ini adalah pesan test dari sistem HM Tour.\n\n";
$message .= "Jika Anda menerima pesan ini, berarti integrasi Fonnte berhasil! ✅\n\n";
$message .= "Waktu: " . date('Y-m-d H:i:s') . "\n\n";
$message .= "Terima kasih! 🙏";

echo "Sending test message to: $adminPhone\n";
echo "Message length: " . strlen($message) . " characters\n\n";

// Send via Fonnte API
$url = 'https://api.fonnte.com/send';

$data = [
    'target' => $adminPhone,
    'message' => $message,
    'countryCode' => '62',
];

echo "API Endpoint: $url\n";
echo "Request Data:\n";
print_r($data);
echo "\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_VERBOSE => true,
]);

echo "Sending request...\n\n";

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
$curlInfo = curl_getinfo($curl);

curl_close($curl);

echo "=== RESPONSE ===\n\n";
echo "HTTP Code: $httpCode\n";

if ($curlError) {
    echo "❌ cURL Error: $curlError\n";
} else {
    echo "✅ No cURL errors\n";
}

echo "\nResponse Body:\n";
echo $response . "\n\n";

$result = json_decode($response, true);

if ($result) {
    echo "Parsed Response:\n";
    print_r($result);
    echo "\n";
}

echo "\n=== CURL INFO ===\n";
echo "Total Time: " . $curlInfo['total_time'] . " seconds\n";
echo "Connect Time: " . $curlInfo['connect_time'] . " seconds\n";
echo "Size Download: " . $curlInfo['size_download'] . " bytes\n";

echo "\n=== RESULT ===\n";

if ($httpCode === 200) {
    if (isset($result['status'])) {
        if ($result['status'] === true || $result['status'] === 'success') {
            echo "✅ SUCCESS! Message sent successfully.\n";
            echo "Check your WhatsApp at: $adminPhone\n";
        } else {
            echo "⚠️  WARNING: HTTP 200 but status is: " . json_encode($result['status']) . "\n";
            echo "Message might still be sent. Check your WhatsApp.\n";
        }
    } else {
        echo "⚠️  WARNING: HTTP 200 but no status field in response.\n";
        echo "Message might still be sent. Check your WhatsApp.\n";
    }
} else {
    echo "❌ FAILED! HTTP Code: $httpCode\n";
    
    if ($httpCode === 502) {
        echo "\n🔍 Troubleshooting HTTP 502:\n";
        echo "1. Check if your Fonnte device is online and connected\n";
        echo "2. Verify your token is correct and active\n";
        echo "3. Check Fonnte dashboard: https://fonnte.com/dashboard\n";
        echo "4. Try again in a few minutes (server might be busy)\n";
        echo "5. Contact Fonnte support if issue persists\n";
    } elseif ($httpCode === 401) {
        echo "\n🔍 Troubleshooting HTTP 401:\n";
        echo "1. Your token might be invalid or expired\n";
        echo "2. Check FONNTE_TOKEN in .env file\n";
        echo "3. Get new token from: https://fonnte.com/dashboard\n";
    } elseif ($httpCode === 400) {
        echo "\n🔍 Troubleshooting HTTP 400:\n";
        echo "1. Check phone number format (should be 628xxx)\n";
        echo "2. Check message content (no special characters issues)\n";
        echo "3. Review Fonnte API documentation\n";
    }
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Check Fonnte dashboard: https://fonnte.com/dashboard\n";
echo "2. Verify device is connected and online\n";
echo "3. Check device battery and internet connection\n";
echo "4. Review API quota/limits\n";
echo "5. Check Laravel logs: storage/logs/laravel.log\n";

echo "\n=== END TEST ===\n";
