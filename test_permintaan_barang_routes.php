<?php

echo "=== TESTING PERMINTAAN BARANG ROUTES ===\n\n";

// Test route generation
echo "1. Testing Route Generation:\n";

try {
    // Test if routes exist
    $routes = [
        'index' => route('admin.supply-chain.permintaan-barang.index'),
        'show' => route('admin.supply-chain.permintaan-barang.show', 1),
        'update' => route('admin.supply-chain.permintaan-barang.update', 1),
        'pdf' => route('admin.supply-chain.permintaan-barang.pdf', 1),
        'approve' => route('admin.supply-chain.permintaan-barang.approve', 1),
        'reject' => route('admin.supply-chain.permintaan-barang.reject', 1),
    ];
    
    foreach ($routes as $name => $url) {
        echo "✅ Route '$name': $url\n";
    }
} catch (Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing Route List:\n";
// Check if routes are registered
$output = shell_exec('php artisan route:list --name=permintaan-barang 2>&1');
if ($output) {
    echo $output;
} else {
    echo "❌ Could not get route list\n";
}

echo "\n3. Manual URL Test:\n";
$baseUrl = "https://poshan.my.id/tofu/admin/supply-chain/permintaan-barang";
echo "Base URL: $baseUrl\n";
echo "Update URL (PUT): $baseUrl/1\n";
echo "Show URL (GET): $baseUrl/1\n";
echo "PDF URL (GET): $baseUrl/1/pdf\n";

echo "\n4. Testing with cURL:\n";
$testId = 1;
$updateUrl = "$baseUrl/$testId";

echo "Testing PUT request to: $updateUrl\n";

// Test with cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $updateUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: $httpCode\n";
if ($httpCode == 405) {
    echo "❌ 405 Method Not Allowed - Route not accepting PUT\n";
} elseif ($httpCode == 404) {
    echo "❌ 404 Not Found - Route not found\n";
} elseif ($httpCode == 200 || $httpCode == 422) {
    echo "✅ Route exists and accepts PUT method\n";
} else {
    echo "Response code: $httpCode\n";
}

echo "\n=== ROUTE DEBUGGING ===\n";
echo "If you see 405 errors, check:\n";
echo "1. Route is properly defined in web.php\n";
echo "2. Route cache is cleared: php artisan route:clear\n";
echo "3. Route parameters match controller method\n";
echo "4. Middleware is not blocking the request\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Clear route cache: php artisan route:clear\n";
echo "2. Check route list: php artisan route:list --name=permintaan-barang\n";
echo "3. Test in browser network tab\n";
echo "4. Check Laravel logs for errors\n";