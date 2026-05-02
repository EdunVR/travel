<?php

echo "=== Testing Service Management Dashboard Checkbox Filter ===\n";

// Test 1: Check if view file exists and has correct structure
$viewPath = 'resources/views/admin/service/index.blade.php';
if (file_exists($viewPath)) {
    echo "✓ Service dashboard view file exists\n";
    
    $content = file_get_contents($viewPath);
    
    // Check for Alpine.js component
    if (strpos($content, 'x-data="serviceDashboard()"') !== false) {
        echo "✓ Alpine.js component found\n";
    } else {
        echo "✗ Alpine.js component missing\n";
    }
    
    // Check for checkbox filter
    if (strpos($content, 'x-model="selectedOutlets"') !== false) {
        echo "✓ Checkbox filter implementation found\n";
    } else {
        echo "✗ Checkbox filter implementation missing\n";
    }
    
    // Check for select all/clear all buttons
    if (strpos($content, 'selectAllOutlets()') !== false && strpos($content, 'clearAllOutlets()') !== false) {
        echo "✓ Select all/clear all functionality found\n";
    } else {
        echo "✗ Select all/clear all functionality missing\n";
    }
    
} else {
    echo "✗ Service dashboard view file not found\n";
}

// Test 2: Check ServiceController for multiple outlet support
$controllerPath = 'app/Http/Controllers/ServiceController.php';
if (file_exists($controllerPath)) {
    echo "✓ ServiceController exists\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check for multiple outlet IDs support in getStatusCounts
    if (strpos($content, 'outlet_ids') !== false && strpos($content, 'whereIn(\'outlet_id\', $outletIds)') !== false) {
        echo "✓ Multiple outlet support in getStatusCounts method found\n";
    } else {
        echo "✗ Multiple outlet support in getStatusCounts method missing\n";
    }
    
} else {
    echo "✗ ServiceController not found\n";
}

// Test 3: Test API endpoint for status counts
$url = 'http://localhost/tofu/admin/service/status-counts?outlet_ids[]=1&outlet_ids[]=2';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

try {
    $response = file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0] ?? 'Unknown';
    
    if (strpos($httpCode, '200') !== false) {
        echo "✓ Status counts API endpoint accessible\n";
        
        $data = json_decode($response, true);
        if (isset($data['menunggu']) && isset($data['lunas'])) {
            echo "✓ Status counts API returns expected data structure\n";
        } else {
            echo "⚠ Status counts API response structure may be incorrect\n";
        }
    } else {
        echo "⚠ Status counts API response: $httpCode\n";
    }
} catch (Exception $e) {
    echo "⚠ Could not test status counts API: " . $e->getMessage() . "\n";
}

echo "\n=== Service Management Dashboard Test Complete ===\n";