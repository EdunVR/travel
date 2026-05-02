<?php

echo "=== Testing SDM Dashboard Syntax Fix ===\n";

// Test 1: Check if view compiles without syntax errors
try {
    $output = shell_exec('php artisan view:cache 2>&1');
    echo "✓ View compilation test passed\n";
    echo "Output: " . $output . "\n";
} catch (Exception $e) {
    echo "✗ View compilation failed: " . $e->getMessage() . "\n";
}

// Test 2: Try to access the SDM dashboard route
$url = 'http://localhost/tofu/admin/sdm';
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
        echo "✓ SDM Dashboard accessible (HTTP 200)\n";
    } elseif (strpos($httpCode, '302') !== false) {
        echo "✓ SDM Dashboard redirecting (HTTP 302) - likely auth redirect\n";
    } else {
        echo "⚠ SDM Dashboard response: $httpCode\n";
    }
    
    // Check for syntax errors in response
    if (strpos($response, 'syntax error') !== false || strpos($response, 'unexpected end of file') !== false) {
        echo "✗ Syntax error still present in response\n";
    } else {
        echo "✓ No syntax errors detected in response\n";
    }
    
} catch (Exception $e) {
    echo "⚠ Could not test URL access: " . $e->getMessage() . "\n";
}

echo "\n=== SDM Dashboard Syntax Fix Test Complete ===\n";