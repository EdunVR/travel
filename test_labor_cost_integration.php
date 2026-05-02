<?php

require_once 'vendor/autoload.php';

// Test labor cost integration in HPP preview
echo "🧪 Testing Labor Cost Integration in HPP Preview\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test data
$testData = [
    'materials' => [
        [
            'material_id' => 1,
            'quantity' => 10,
            'material_type' => 'bahan'
        ]
    ],
    'labor_costs' => [
        'worker_count' => 5,
        'cost_per_worker' => 100000,
        'total_cost' => 500000
    ],
    'operational_costs' => [
        [
            'cost_type' => 'Listrik',
            'amount' => 50000
        ]
    ],
    'quantity' => 100
];

echo "📋 Test Data:\n";
echo "Materials: " . json_encode($testData['materials'], JSON_PRETTY_PRINT) . "\n";
echo "Labor Costs: " . json_encode($testData['labor_costs'], JSON_PRETTY_PRINT) . "\n";
echo "Operational Costs: " . json_encode($testData['operational_costs'], JSON_PRETTY_PRINT) . "\n";
echo "Quantity: " . $testData['quantity'] . "\n\n";

// Test URL
$url = 'http://localhost/production/calculate-hpp-preview';

// Create POST data
$postData = http_build_query($testData);

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
]);

echo "🌐 Making request to: $url\n";
echo "📤 POST Data: $postData\n\n";

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit(1);
}

echo "📥 HTTP Response Code: $httpCode\n";
echo "📥 Response Body:\n";
echo $response . "\n\n";

// Parse JSON response
$data = json_decode($response, true);

if ($data && isset($data['success']) && $data['success']) {
    echo "✅ Request successful!\n\n";
    
    $responseData = $data['data'];
    
    echo "💰 Cost Breakdown:\n";
    echo "- Material Cost: Rp " . number_format($responseData['material_cost'] ?? 0, 0, ',', '.') . "\n";
    echo "- Labor Cost: Rp " . number_format($responseData['labor_cost'] ?? 0, 0, ',', '.') . "\n";
    echo "- Operational Cost: Rp " . number_format($responseData['operational_cost'] ?? 0, 0, ',', '.') . "\n";
    echo "- Total Cost: Rp " . number_format($responseData['total_cost'] ?? 0, 0, ',', '.') . "\n";
    echo "- HPP per Unit: Rp " . number_format($responseData['hpp_per_unit'] ?? 0, 0, ',', '.') . "\n\n";
    
    // Check if labor cost is properly calculated
    $expectedLaborCost = $testData['labor_costs']['worker_count'] * $testData['labor_costs']['cost_per_worker'];
    $actualLaborCost = $responseData['labor_cost'] ?? 0;
    
    echo "🔍 Labor Cost Validation:\n";
    echo "- Expected: Rp " . number_format($expectedLaborCost, 0, ',', '.') . " (5 workers × Rp 100,000)\n";
    echo "- Actual: Rp " . number_format($actualLaborCost, 0, ',', '.') . "\n";
    
    if ($actualLaborCost == $expectedLaborCost) {
        echo "✅ Labor cost calculation is CORRECT!\n";
    } else {
        echo "❌ Labor cost calculation is INCORRECT!\n";
        echo "   Difference: Rp " . number_format(abs($actualLaborCost - $expectedLaborCost), 0, ',', '.') . "\n";
    }
    
    // Check breakdown data
    if (isset($responseData['breakdown']['labor_costs'])) {
        echo "\n📊 Labor Cost Breakdown:\n";
        $breakdown = $responseData['breakdown']['labor_costs'];
        echo "- Worker Count: " . ($breakdown['worker_count'] ?? 'N/A') . "\n";
        echo "- Cost per Worker: Rp " . number_format($breakdown['cost_per_worker'] ?? 0, 0, ',', '.') . "\n";
        echo "- Total Cost: Rp " . number_format($breakdown['total_cost'] ?? 0, 0, ',', '.') . "\n";
    }
    
} else {
    echo "❌ Request failed!\n";
    if (isset($data['message'])) {
        echo "Error: " . $data['message'] . "\n";
    }
    if (isset($data['errors'])) {
        echo "Validation Errors: " . json_encode($data['errors'], JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";