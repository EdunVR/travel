<?php

echo "=== Testing SDM Dashboard Direct Access ===\n";

// Test direct access to SDM dashboard
$url = 'http://localhost/tofu/admin/sdm';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true,
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]
]);

try {
    echo "Attempting to access: $url\n";
    $response = file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0] ?? 'Unknown';
    
    echo "HTTP Response: $httpCode\n";
    
    if (strpos($httpCode, '200') !== false) {
        echo "✓ SDM Dashboard accessible (HTTP 200)\n";
        
        // Check for the specific error
        if (strpos($response, 'Undefined variable $outlets') !== false) {
            echo "✗ Undefined variable \$outlets error found in response\n";
        } else {
            echo "✓ No \$outlets error found in response\n";
        }
        
        // Check if outlets data is present
        if (strpos($response, 'outlets:') !== false) {
            echo "✓ Outlets data found in response\n";
        } else {
            echo "⚠ Outlets data not found in response\n";
        }
        
    } elseif (strpos($httpCode, '302') !== false) {
        echo "⚠ SDM Dashboard redirecting (HTTP 302) - likely auth redirect\n";
    } elseif (strpos($httpCode, '500') !== false) {
        echo "✗ SDM Dashboard returning server error (HTTP 500)\n";
        
        // Check for the specific error in 500 response
        if (strpos($response, 'Undefined variable $outlets') !== false) {
            echo "✗ Confirmed: Undefined variable \$outlets error\n";
        }
    } else {
        echo "⚠ SDM Dashboard unexpected response: $httpCode\n";
    }
    
} catch (Exception $e) {
    echo "⚠ Could not test SDM dashboard access: " . $e->getMessage() . "\n";
}

// Check Laravel logs for any errors
$logPath = 'storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50); // Get last 50 lines
    
    $sdmErrors = array_filter($recentLines, function($line) {
        return strpos($line, 'SdmDashboardController') !== false || 
               strpos($line, 'Undefined variable $outlets') !== false ||
               strpos($line, 'admin.sdm.index') !== false;
    });
    
    if (!empty($sdmErrors)) {
        echo "\nRecent SDM-related log entries:\n";
        foreach ($sdmErrors as $error) {
            echo "- " . trim($error) . "\n";
        }
    } else {
        echo "\n✓ No recent SDM-related errors in logs\n";
    }
} else {
    echo "\n⚠ Laravel log file not found\n";
}

echo "\n=== SDM Direct Access Test Complete ===\n";