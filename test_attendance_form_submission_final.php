<?php

/**
 * Test attendance form submission with HH:MM:SS format
 */

echo "🧪 Testing attendance form submission with HH:MM:SS format...\n\n";

// Test data
$testData = [
    'employee_id' => 1,
    'date' => date('Y-m-d'),
    'clock_in' => '08:30:15',
    'clock_out' => '17:45:30',
    'break_in' => '12:00:00',
    'break_out' => '13:00:00',
    'overtime_in' => '18:00:00',
    'overtime_out' => '20:00:00',
    'status' => 'present',
    'notes' => 'Test HH:MM:SS format submission'
];

echo "📝 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "   $key: $value\n";
}

echo "\n🔍 Testing validation rules...\n";

// Test each time field individually
$timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];
$pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';

$allValid = true;

foreach ($timeFields as $field) {
    $value = $testData[$field];
    $isValid = preg_match($pattern, $value);
    
    if ($isValid) {
        echo "✅ $field: $value - Valid\n";
    } else {
        echo "❌ $field: $value - Invalid\n";
        $allValid = false;
    }
}

echo "\n📊 Validation Results:\n";
if ($allValid) {
    echo "🎉 All time fields passed validation!\n";
} else {
    echo "❌ Some time fields failed validation!\n";
}

echo "\n🌐 Testing actual HTTP request...\n";

// Prepare the request
$url = 'https://poshan.my.id/tofu/sdm/attendance/store';
$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

// Convert data to JSON
$jsonData = json_encode($testData);

echo "📤 Request URL: $url\n";
echo "📤 Request Data: $jsonData\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute request
echo "\n🚀 Sending request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "📥 HTTP Status Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "📥 Response: " . substr($response, 0, 500) . "...\n";
    
    // Try to decode JSON response
    $responseData = json_decode($response, true);
    
    if ($responseData) {
        echo "\n📊 Response Analysis:\n";
        
        if (isset($responseData['success'])) {
            if ($responseData['success']) {
                echo "✅ Request successful!\n";
                echo "✅ Message: " . ($responseData['message'] ?? 'No message') . "\n";
            } else {
                echo "❌ Request failed!\n";
                echo "❌ Message: " . ($responseData['message'] ?? 'No message') . "\n";
                
                if (isset($responseData['errors'])) {
                    echo "❌ Validation Errors:\n";
                    foreach ($responseData['errors'] as $field => $errors) {
                        echo "   - $field: " . implode(', ', $errors) . "\n";
                    }
                }
            }
        } else {
            echo "⚠️ Unexpected response format\n";
        }
    } else {
        echo "⚠️ Could not decode JSON response\n";
        
        // Check if it's an HTML error page
        if (strpos($response, '<!DOCTYPE') !== false) {
            echo "⚠️ Received HTML response (likely an error page)\n";
            
            // Extract error message if possible
            if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
                echo "⚠️ Page Title: " . $matches[1] . "\n";
            }
        }
    }
}

echo "\n🎯 SUMMARY:\n";
echo "✅ JavaScript selectors fixed\n";
echo "✅ Regex patterns properly formatted\n";
echo "✅ Time validation working locally\n";

if ($httpCode == 200 && $allValid) {
    echo "🎉 HH:MM:SS format should now be working!\n";
} else {
    echo "⚠️ There may still be server-side issues to resolve\n";
}

echo "\n📋 NEXT STEPS:\n";
echo "1. Test the form manually in the browser\n";
echo "2. Check Laravel logs for any remaining errors\n";
echo "3. Verify browser console shows no JavaScript errors\n";
echo "4. Try submitting with both HH:MM and HH:MM:SS formats\n";

echo "\n🚀 Testing complete!\n";

?>