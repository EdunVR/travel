<?php

// Test script to verify investor controllers return proper JSON responses

echo "=== TESTING INVESTOR CONTROLLERS ===\n\n";

// Test URLs
$baseUrl = 'http://localhost/MORRA'; // Adjust this to your local URL
$testUrls = [
    'Investor Profil' => $baseUrl . '/investor-admin/profil',
    'Investor Bagi Hasil' => $baseUrl . '/investor-admin/bagi-hasil', 
    'Investor Pencairan' => $baseUrl . '/investor-admin/pencairan'
];

// Test parameters for AJAX requests
$testParams = [
    'search' => '',
    'outlet_filter' => 'ALL',
    'status_filter' => 'ALL',
    'sort_key' => 'name',
    'sort_dir' => 'asc'
];

foreach ($testUrls as $name => $url) {
    echo "Testing: $name\n";
    echo "URL: $url\n";
    
    // Build query string
    $queryString = http_build_query($testParams);
    $fullUrl = $url . '?' . $queryString;
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
    } else {
        echo "HTTP Code: $httpCode\n";
        
        if ($httpCode === 200) {
            // Try to decode JSON
            $data = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "✅ Valid JSON Response\n";
                
                if (isset($data['success']) && $data['success'] === true) {
                    echo "✅ Success Response\n";
                    
                    if (isset($data['data']) && is_array($data['data'])) {
                        echo "✅ Data Array Present (" . count($data['data']) . " items)\n";
                    } else {
                        echo "⚠️  No data array or empty data\n";
                    }
                } else {
                    echo "❌ Success flag not true or missing\n";
                    echo "Response: " . substr($response, 0, 200) . "...\n";
                }
            } else {
                echo "❌ Invalid JSON Response\n";
                echo "JSON Error: " . json_last_error_msg() . "\n";
                echo "Response Preview: " . substr($response, 0, 200) . "...\n";
            }
        } else if ($httpCode === 302) {
            echo "⚠️  Redirect Response (likely authentication required)\n";
            echo "Response Preview: " . substr($response, 0, 200) . "...\n";
        } else {
            echo "❌ HTTP Error: $httpCode\n";
            echo "Response Preview: " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "\nIf all tests show ✅ Valid JSON Response, the controllers are working correctly.\n";
echo "If you see HTML responses or errors, check:\n";
echo "1. Routes are properly defined inside admin middleware group\n";
echo "2. Controllers are updated with new index methods\n";
echo "3. No syntax errors in controller files\n";
echo "4. Database connections are working\n";
echo "5. Required models and relationships exist\n";
echo "6. Authentication is working (302 redirects indicate auth issues)\n";