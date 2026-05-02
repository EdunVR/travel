<?php

echo "🔍 DEBUGGING ATTENDANCE 422 ERROR\n";
echo "==================================\n\n";

// Test data that should be valid for date_format:H:i
$testData = [
    'employee_id' => 1,
    'date' => '2026-01-27',
    'clock_in' => '08:30',
    'clock_out' => '17:00',
    'break_in' => '12:00',
    'break_out' => '13:00',
    'overtime_in' => '18:00',
    'overtime_out' => '20:00',
    'status' => 'present',
    'notes' => 'Test attendance'
];

echo "📊 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "   $key: $value\n";
}

echo "\n🔍 Testing date_format:H:i validation manually:\n";

$timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];

foreach ($timeFields as $field) {
    if (isset($testData[$field])) {
        $value = $testData[$field];
        
        // Test with PHP's DateTime::createFromFormat (same as Laravel's date_format:H:i)
        $dateTime = DateTime::createFromFormat('H:i', $value);
        $isValid = $dateTime && $dateTime->format('H:i') === $value;
        
        if ($isValid) {
            echo "   ✅ $field: $value - Valid\n";
        } else {
            echo "   ❌ $field: $value - Invalid\n";
        }
    }
}

echo "\n🌐 Testing with cURL to actual endpoint:\n";

$url = 'https://poshan.my.id/tofu/sdm/attendance/store';
$data = json_encode($testData);

echo "URL: $url\n";
echo "Data: $data\n\n";

// Get CSRF token first
echo "1. Getting CSRF token...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://poshan.my.id/tofu/sdm/attendance');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";

// Extract CSRF token
preg_match('/name="_token" value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? null;

if ($csrfToken) {
    echo "   ✅ CSRF Token: " . substr($csrfToken, 0, 20) . "...\n";
} else {
    echo "   ❌ CSRF Token not found\n";
    echo "   Response preview: " . substr($response, 0, 500) . "...\n";
}

echo "\n2. Testing attendance store endpoint...\n";

if ($csrfToken) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . $csrfToken,
        'X-Requested-With: XMLHttpRequest'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "   HTTP Code: $httpCode\n";
    echo "   Response: " . substr($response, 0, 1000) . "\n";

    if ($httpCode === 422) {
        echo "\n❌ 422 VALIDATION ERROR REPRODUCED!\n";
        $responseData = json_decode($response, true);
        if (isset($responseData['errors'])) {
            echo "Validation Errors:\n";
            foreach ($responseData['errors'] as $field => $errors) {
                echo "   - $field: " . implode(', ', $errors) . "\n";
            }
        }
    } else if ($httpCode === 200 || $httpCode === 201) {
        echo "\n✅ SUCCESS! No validation error\n";
    } else {
        echo "\n⚠️  Unexpected HTTP code: $httpCode\n";
    }
} else {
    echo "   ❌ Cannot test without CSRF token\n";
}

echo "\n📋 POSSIBLE CAUSES:\n";
echo "1. Frontend sending data in wrong format\n";
echo "2. Backend validation rules changed\n";
echo "3. Middleware or authentication issue\n";
echo "4. Database constraint issue\n";
echo "5. Missing required fields\n";

echo "\n🔧 NEXT STEPS:\n";
echo "1. Check browser network tab for exact request data\n";
echo "2. Check Laravel log for detailed validation errors\n";
echo "3. Verify employee_id exists in database\n";
echo "4. Test with minimal required fields only\n";

echo "\n✅ Debug test complete!\n";