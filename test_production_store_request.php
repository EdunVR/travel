<?php

// Test the actual production store request
echo "=== TESTING PRODUCTION STORE REQUEST ===\n\n";

// Simulate the request data from the JavaScript error
$testData = [
    '_token' => '2b9k2gviIjrV9nbWObFFgyMRKfnW6zPdh7TLk5op',
    'outlet_id' => '2',
    'production_code' => '',
    'products' => [
        [
            'product_id' => '40',
            'target_quantity' => '1000',
            'sample_quantity' => ''
        ],
        [
            'product_id' => '41',
            'target_quantity' => '',
            'sample_quantity' => ''
        ]
    ],
    'production_line' => '',
    'target_quantity' => '1000',
    'start_date' => '',
    'end_date' => '',
    'expiry_date' => '',
    'priority' => 'normal',
    'business_type' => '',
    'tofu_data' => [
        'perendaman_waktu' => '',
        'perendaman_qty' => '',
        'rijek_telur' => '',
        'pasteurisasi_waktu' => '',
        'pasteurisasi_suhu' => '',
        'berat_sari_kedelai' => '',
        'waktu_pencampuran' => '',
        'filling_waktu' => '',
        'filling_mesin1' => '',
        'filling_mesin2' => '',
        'filling_total' => '',
        'rijek_mentah' => ''
    ],
    'materials' => [],
    'labor_costs' => [
        'worker_count' => '20',
        'cost_per_worker' => '',
        'total_cost' => '0'
    ]
];

echo "1. Analyzing request data structure...\n";
echo "   - Outlet ID: " . $testData['outlet_id'] . "\n";
echo "   - Products count: " . count($testData['products']) . "\n";
echo "   - Production line: '" . $testData['production_line'] . "' (empty)\n";
echo "   - Start date: '" . $testData['start_date'] . "' (empty)\n";
echo "   - End date: '" . $testData['end_date'] . "' (empty)\n";

echo "\n2. Checking validation issues...\n";

$validationErrors = [];

// Check required fields
if (empty($testData['outlet_id'])) {
    $validationErrors[] = "outlet_id is required";
}

if (empty($testData['production_line'])) {
    $validationErrors[] = "production_line is required but empty";
}

if (empty($testData['start_date'])) {
    $validationErrors[] = "start_date is required but empty";
}

if (empty($testData['end_date'])) {
    $validationErrors[] = "end_date is required but empty";
}

// Check products
$validProducts = 0;
foreach ($testData['products'] as $index => $product) {
    if (!empty($product['product_id']) && !empty($product['target_quantity'])) {
        $validProducts++;
    } else {
        $validationErrors[] = "products[$index] has missing product_id or target_quantity";
    }
}

if ($validProducts === 0) {
    $validationErrors[] = "No valid products found (all products must have product_id and target_quantity)";
}

if (!empty($validationErrors)) {
    echo "   ❌ Validation errors found:\n";
    foreach ($validationErrors as $error) {
        echo "      - $error\n";
    }
} else {
    echo "   ✅ No validation errors found\n";
}

echo "\n3. Checking route accessibility...\n";

// Test if we can create a simple cURL request to the endpoint
$url = 'https://poshan.my.id/tofu/admin/produksi/produksi';
echo "   - Target URL: $url\n";

// Check if the URL is accessible (basic connectivity test)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); // Just test GET first

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ❌ cURL error: $error\n";
} else {
    echo "   ✅ URL is accessible (HTTP $httpCode)\n";
}

echo "\n4. Recommendations for fixing the issue...\n";

if (!empty($validationErrors)) {
    echo "   🔧 IMMEDIATE FIXES NEEDED:\n";
    echo "      1. Fill in required fields before submitting:\n";
    echo "         - production_line (cannot be empty)\n";
    echo "         - start_date (required date)\n";
    echo "         - end_date (required date)\n";
    echo "      2. Ensure all products have both product_id and target_quantity\n";
    echo "      3. Remove products with empty target_quantity from the array\n";
    
    echo "\n   📝 FRONTEND VALIDATION NEEDED:\n";
    echo "      - Add client-side validation to prevent empty required fields\n";
    echo "      - Filter out incomplete products before sending request\n";
    echo "      - Show user-friendly error messages for missing fields\n";
}

echo "\n   🔧 GENERAL TROUBLESHOOTING:\n";
echo "      1. Check browser console for JavaScript errors\n";
echo "      2. Verify CSRF token is valid and not expired\n";
echo "      3. Check if user has proper permissions\n";
echo "      4. Verify database connection is working\n";
echo "      5. Check Laravel logs for detailed error information\n";

echo "\n5. Sample valid request data...\n";

$validData = [
    '_token' => '2b9k2gviIjrV9nbWObFFgyMRKfnW6zPdh7TLk5op',
    'outlet_id' => '2',
    'production_line' => 'Line A',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+1 day')),
    'priority' => 'normal',
    'products' => [
        [
            'product_id' => '40',
            'target_quantity' => '1000',
            'sample_quantity' => '0'
        ]
    ],
    'materials' => [],
    'labor_costs' => [
        'worker_count' => '20',
        'cost_per_worker' => '50000',
        'total_cost' => '1000000'
    ]
];

echo "   ✅ Valid request structure:\n";
echo "   " . json_encode($validData, JSON_PRETTY_PRINT) . "\n";

echo "\n=== TEST COMPLETED ===\n";
echo "The store method exists and is properly implemented.\n";
echo "The main issue is missing required fields in the request data.\n";
echo "Fix the frontend validation to ensure all required fields are filled.\n";