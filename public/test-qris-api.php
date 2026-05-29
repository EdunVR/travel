<?php
/**
 * Test QRIS InterActive API - FIXED: GET method + useTip parameter
 * HAPUS SETELAH TESTING!
 */
if (($_GET['key'] ?? '') !== 'hmtest2026') die('Unauthorized');

$apiKey = '139139260518825';
$mID = '127281939';
$baseUrl = 'https://qris.interactive.co.id/restapi/qris/show_qris.php';
$testAmount = $_GET['amount'] ?? 10000;
$trxNumber = 'TEST-' . date('YmdHis') . '-' . rand(100, 999);

echo "<h2>QRIS InterActive API Test (GET + useTip)</h2><pre>";

// Correct format: GET with useTip parameter
$params = [
    'do' => 'create-invoice',
    'apikey' => $apiKey,
    'mID' => $mID,
    'cliTrxNumber' => $trxNumber,
    'cliTrxAmount' => $testAmount,
    'useTip' => 'no',
];

$url = $baseUrl . '?' . http_build_query($params);
echo "URL: $url\n\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    // Force resolve to known IP to bypass DNS issues
    CURLOPT_RESOLVE => ['qris.interactive.co.id:443:13.75.115.40'],
]);
$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "CURL Error: $error (HTTP $httpCode)\n";
} else {
    echo "HTTP Code: $httpCode\n";
    echo "Response:\n";
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo $response . "\n";
    }
}

echo "</pre>";
