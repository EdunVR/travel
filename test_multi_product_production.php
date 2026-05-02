<?php

require_once 'vendor/autoload.php';

// Test Multi-Product Production Creation
echo "=== Testing Multi-Product Production Creation ===\n";

$testData = [
    'outlet_id' => 3,
    'production_line' => 'Lini A',
    'start_date' => '2026-01-08',
    'end_date' => '2026-01-10',
    'priority' => 'normal',
    'business_type' => 'tofu',
    'products' => [
        [
            'product_id' => 1,
            'target_quantity' => 100,
            'sample_quantity' => 5
        ],
        [
            'product_id' => 2,
            'target_quantity' => 50,
            'sample_quantity' => 2
        ]
    ],
    'tofu_data' => [
        'perendaman_waktu' => 8.5,
        'perendaman_qty' => 50,
        'rijek_telur' => 2,
        'pasteurisasi_waktu' => 30,
        'pasteurisasi_suhu' => 85,
        'berat_sari_kedelai' => 45.5,
        'waktu_pencampuran' => 15,
        'filling_waktu' => 2.5,
        'filling_mesin1' => 80,
        'filling_mesin2' => 70,
        'filling_total' => 150,
        'rijek_mentah' => 3
    ],
    'materials' => [
        [
            'material_id' => 1,
            'material_type' => 'bahan',
            'quantity' => 20,
            'unit' => 'kg'
        ]
    ],
    'labor_costs' => [
        'worker_count' => 3,
        'cost_per_worker' => 100000
    ],
    'operational_costs' => [
        [
            'description' => 'Listrik',
            'amount' => 50000
        ]
    ]
];

// Calculate total target quantity
$totalTargetQuantity = 0;
foreach ($testData['products'] as $product) {
    $totalTargetQuantity += $product['target_quantity'];
}
$testData['target_quantity'] = $totalTargetQuantity;

echo "Test Data:\n";
echo "- Outlet ID: " . $testData['outlet_id'] . "\n";
echo "- Production Line: " . $testData['production_line'] . "\n";
echo "- Business Type: " . $testData['business_type'] . "\n";
echo "- Products Count: " . count($testData['products']) . "\n";
echo "- Total Target Quantity: " . $totalTargetQuantity . "\n";
echo "- Has Tofu Data: " . (isset($testData['tofu_data']) ? 'Yes' : 'No') . "\n";

// Test the API endpoint
$url = 'http://localhost/tofu/admin/produksi/produksi';
echo "\nTesting API: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
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
} else {
    echo "Response:\n";
    $data = json_decode($response, true);
    if ($data) {
        echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "- Message: " . ($data['message'] ?? 'No message') . "\n";
        
        if (isset($data['errors'])) {
            echo "- Errors:\n";
            foreach ($data['errors'] as $field => $errors) {
                echo "  - $field: " . implode(', ', $errors) . "\n";
            }
        }
        
        if (isset($data['data'])) {
            echo "- Production ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
            echo "- Production Code: " . ($data['data']['production_code'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Invalid JSON Response:\n";
        echo substr($response, 0, 500) . "...\n";
    }
}

echo "\n=== Test Complete ===\n";