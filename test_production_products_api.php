<?php

require_once 'vendor/autoload.php';

// Test Production Products API Endpoint
echo "=== Testing Production Products API ===\n";

// Test with different outlet IDs
$testOutlets = [3, 1, 2]; // Common outlet IDs

foreach ($testOutlets as $outletId) {
    echo "\n--- Testing Outlet ID: $outletId ---\n";
    
    $url = "http://localhost/admin/produksi/produksi/products?outlet_id=" . $outletId;
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    
    if ($error) {
        echo "CURL Error: $error\n";
        continue;
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        
        if ($data) {
            echo "Response Structure:\n";
            echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            echo "- Data Type: " . gettype($data['data']) . "\n";
            
            if (isset($data['data']) && is_array($data['data'])) {
                echo "- Products Count: " . count($data['data']) . "\n";
                
                if (count($data['data']) > 0) {
                    echo "- First Product Structure:\n";
                    $firstProduct = $data['data'][0];
                    foreach ($firstProduct as $key => $value) {
                        echo "  - $key: " . (is_string($value) ? $value : gettype($value)) . "\n";
                    }
                }
            }
            
            if (isset($data['message'])) {
                echo "- Message: " . $data['message'] . "\n";
            }
        } else {
            echo "Invalid JSON Response\n";
            echo "Raw Response: " . substr($response, 0, 200) . "...\n";
        }
    } else {
        echo "HTTP Error: $httpCode\n";
        echo "Response: " . substr($response, 0, 200) . "...\n";
    }
}

echo "\n=== Test Complete ===\n";