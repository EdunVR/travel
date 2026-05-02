<?php

echo "🧪 TESTING ROOT URL REDIRECT FIX\n";
echo "================================\n\n";

$baseUrl = 'https://poshan.my.id/tofu';

// Test cases
$tests = [
    [
        'name' => 'GET Root URL',
        'method' => 'GET',
        'url' => $baseUrl . '/',
        'expected' => [302, 301] // Redirect codes
    ],
    [
        'name' => 'POST Root URL (should return 405)',
        'method' => 'POST',
        'url' => $baseUrl . '/',
        'expected' => [405] // Method not allowed
    ],
    [
        'name' => 'Health Check',
        'method' => 'GET',
        'url' => $baseUrl . '/health',
        'expected' => [200] // OK
    ],
    [
        'name' => 'Login Page',
        'method' => 'GET',
        'url' => $baseUrl . '/login',
        'expected' => [200] // OK
    ]
];

foreach ($tests as $test) {
    echo "Testing: {$test['name']}\n";
    echo "URL: {$test['url']}\n";
    echo "Method: {$test['method']}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $test['method']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Root Redirect Test Bot/1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    if ($test['method'] === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: $error\n";
    } else {
        $isExpected = in_array($httpCode, $test['expected']);
        $status = $isExpected ? "✅ PASS" : "❌ FAIL";
        
        echo "Response Code: $httpCode\n";
        echo "Status: $status\n";
        
        if (!$isExpected) {
            echo "Expected: " . implode(' or ', $test['expected']) . "\n";
        }
        
        // Extract redirect location if it's a redirect
        if ($httpCode >= 300 && $httpCode < 400) {
            if (preg_match('/Location: (.+)/i', $response, $matches)) {
                echo "Redirect to: " . trim($matches[1]) . "\n";
            }
        }
        
        // Show response headers for debugging
        if ($httpCode === 405) {
            if (preg_match('/Allow: (.+)/i', $response, $matches)) {
                echo "Allowed methods: " . trim($matches[1]) . "\n";
            }
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "🔍 MANUAL TESTING STEPS:\n";
echo "1. Open browser and go to: $baseUrl\n";
echo "2. Should redirect to login page automatically\n";
echo "3. Try accessing $baseUrl/health - should show JSON status\n";
echo "4. Check browser console for any errors\n";
echo "5. Test with different browsers and devices\n\n";

echo "🛠️ TROUBLESHOOTING:\n";
echo "- If still getting 405 errors, clear browser cache\n";
echo "- Check server logs for detailed error messages\n";
echo "- Verify .htaccess rules are applied correctly\n";
echo "- Test session functionality after login\n\n";

echo "✅ ROOT REDIRECT FIX TESTING COMPLETE\n";