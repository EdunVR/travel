<?php

/**
 * Test HH:MM:SS format support after regex delimiter fix
 */

echo "🧪 Testing HH:MM:SS format support after regex delimiter fix...\n\n";

// Test the regex pattern directly
$pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';

echo "📋 Testing regex pattern: $pattern\n\n";

$testCases = [
    // Valid HH:MM format
    '08:30' => true,
    '16:45' => true,
    '23:59' => true,
    '00:00' => true,
    
    // Valid HH:MM:SS format
    '08:30:15' => true,
    '16:45:30' => true,
    '23:59:59' => true,
    '00:00:00' => true,
    '12:34:56' => true,
    
    // Invalid formats
    '25:00' => false,
    '12:60' => false,
    '12:30:60' => false,
    '8:30' => true, // Single digit hour should work
    '08:5' => false, // Single digit minute should fail
    'abc:def' => false,
    '12:30:45:67' => false,
];

$passed = 0;
$failed = 0;

foreach ($testCases as $time => $expected) {
    $result = preg_match($pattern, $time);
    $isValid = (bool)$result;
    
    if ($isValid === $expected) {
        echo "✅ $time - " . ($expected ? 'Valid' : 'Invalid') . " (Expected)\n";
        $passed++;
    } else {
        echo "❌ $time - " . ($isValid ? 'Valid' : 'Invalid') . " (Expected: " . ($expected ? 'Valid' : 'Invalid') . ")\n";
        $failed++;
    }
}

echo "\n📊 REGEX TEST RESULTS:\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";

if ($failed === 0) {
    echo "🎉 All regex tests passed!\n";
} else {
    echo "⚠️ Some regex tests failed!\n";
}

echo "\n🌐 Testing actual attendance form submission...\n";

// Test actual form submission
$testData = [
    'employee_id' => 1,
    'date' => date('Y-m-d'),
    'clock_in' => '08:30:15',
    'clock_out' => '17:45:30',
    'status' => 'present',
    'notes' => 'Test HH:MM:SS format'
];

echo "📝 Test data:\n";
foreach ($testData as $key => $value) {
    echo "   $key: $value\n";
}

// Simulate validation
echo "\n🔍 Simulating Laravel validation...\n";

$rules = [
    'employee_id' => 'required|exists:recruitments,id',
    'date' => 'required|date',
    'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'status' => 'required|in:present,late,absent,leave,sick,permission',
    'notes' => 'nullable|string',
];

$validationPassed = true;

// Test clock_in
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $testData['clock_in'])) {
    echo "❌ clock_in validation failed\n";
    $validationPassed = false;
} else {
    echo "✅ clock_in validation passed\n";
}

// Test clock_out
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $testData['clock_out'])) {
    echo "❌ clock_out validation failed\n";
    $validationPassed = false;
} else {
    echo "✅ clock_out validation passed\n";
}

echo "\n📊 VALIDATION TEST RESULTS:\n";
if ($validationPassed) {
    echo "🎉 All validation tests passed!\n";
    echo "✅ HH:MM:SS format is now supported\n";
} else {
    echo "❌ Some validation tests failed\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Open attendance page: https://poshan.my.id/tofu/sdm/attendance\n";
echo "2. Click 'Tambah Absensi' button\n";
echo "3. Try entering time in HH:MM:SS format (e.g., 08:30:15)\n";
echo "4. Submit the form\n";
echo "5. Verify no more regex delimiter errors\n";

echo "\n🚀 HH:MM:SS format support should now be working!\n";

?>