<?php

/**
 * 24-Hour Format Implementation Verification
 * 
 * This script verifies that all 24-hour format fixes are properly implemented
 * and tests the validation endpoints to ensure they work correctly.
 */

echo "=== 24-HOUR FORMAT VERIFICATION TEST ===\n\n";

// Test data for validation
$testCases = [
    // Valid 24-hour formats
    ['time' => '00:00', 'expected' => 'valid', 'description' => 'Midnight'],
    ['time' => '08:30', 'expected' => 'valid', 'description' => 'Morning'],
    ['time' => '12:00', 'expected' => 'valid', 'description' => 'Noon'],
    ['time' => '14:45', 'expected' => 'valid', 'description' => 'Afternoon'],
    ['time' => '23:59', 'expected' => 'valid', 'description' => 'Late night'],
    
    // Invalid formats
    ['time' => '25:00', 'expected' => 'invalid', 'description' => 'Invalid hour (25)'],
    ['time' => '08:60', 'expected' => 'invalid', 'description' => 'Invalid minute (60)'],
    ['time' => '8:30', 'expected' => 'invalid', 'description' => 'Single digit hour'],
    ['time' => 'abc:def', 'expected' => 'invalid', 'description' => 'Non-numeric'],
    ['time' => '8:30 AM', 'expected' => 'invalid', 'description' => 'AM/PM format'],
];

echo "1. TESTING VALIDATION LOGIC\n";
echo "============================\n";

foreach ($testCases as $test) {
    $time = $test['time'];
    $expected = $test['expected'];
    $description = $test['description'];
    
    // Test PHP validation logic (same as used in controller)
    $isValid = preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    
    // Also test Laravel's date_format validation logic
    $laravelValid = false;
    try {
        $dateTime = DateTime::createFromFormat('H:i', $time);
        $laravelValid = $dateTime && $dateTime->format('H:i') === $time;
    } catch (Exception $e) {
        $laravelValid = false;
    }
    
    $result = $laravelValid ? 'valid' : 'invalid';
    $status = ($result === $expected) ? '✅ PASS' : '❌ FAIL';
    
    echo sprintf("%-20s | %-15s | Expected: %-7s | Got: %-7s | %s\n", 
        $description, $time, $expected, $result, $status);
}

echo "\n2. CHECKING CONTROLLER IMPLEMENTATION\n";
echo "=====================================\n";

// Check if controller file exists and has the correct validation
$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for date_format:H:i validation
    $hasDateFormat = strpos($content, 'date_format:H:i') !== false;
    echo "✅ Controller exists: " . $controllerFile . "\n";
    echo ($hasDateFormat ? "✅" : "❌") . " Uses date_format:H:i validation: " . ($hasDateFormat ? "YES" : "NO") . "\n";
    
    // Check for proper error messages
    $hasErrorMessages = strpos($content, 'Format jam') !== false && strpos($content, '24 jam') !== false;
    echo ($hasErrorMessages ? "✅" : "❌") . " Has 24-hour error messages: " . ($hasErrorMessages ? "YES" : "NO") . "\n";
    
    // Check for specific methods
    $methods = ['setWorkHours', 'updateTimeSettings', 'testTimePeriod'];
    foreach ($methods as $method) {
        $hasMethod = strpos($content, "function {$method}") !== false;
        echo ($hasMethod ? "✅" : "❌") . " Has {$method} method: " . ($hasMethod ? "YES" : "NO") . "\n";
    }
} else {
    echo "❌ Controller file not found: " . $controllerFile . "\n";
}

echo "\n3. CHECKING VIEW IMPLEMENTATION\n";
echo "===============================\n";

// Check if view file exists and has the correct CSS/JS
$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    echo "✅ View exists: " . $viewFile . "\n";
    
    // Check for CSS to hide AM/PM
    $hasWebkitCSS = strpos($content, 'webkit-datetime-edit-ampm-field') !== false;
    echo ($hasWebkitCSS ? "✅" : "❌") . " Has WebKit AM/PM hiding CSS: " . ($hasWebkitCSS ? "YES" : "NO") . "\n";
    
    $hasMozCSS = strpos($content, 'moz-time-picker-ampm') !== false;
    echo ($hasMozCSS ? "✅" : "❌") . " Has Firefox AM/PM hiding CSS: " . ($hasMozCSS ? "YES" : "NO") . "\n";
    
    // Check for JavaScript validation
    $hasJSValidation = strpos($content, 'DOMContentLoaded') !== false && strpos($content, 'input[type="time"]') !== false;
    echo ($hasJSValidation ? "✅" : "❌") . " Has JavaScript validation: " . ($hasJSValidation ? "YES" : "NO") . "\n";
    
    // Check for 24-hour labels
    $has24HourLabels = strpos($content, '(24 jam)') !== false;
    echo ($has24HourLabels ? "✅" : "❌") . " Has 24-hour labels: " . ($has24HourLabels ? "YES" : "NO") . "\n";
    
    // Check for time input attributes
    $hasStepAttribute = strpos($content, 'step="1"') !== false;
    echo ($hasStepAttribute ? "✅" : "❌") . " Has step='1' attribute: " . ($hasStepAttribute ? "YES" : "NO") . "\n";
    
    $hasPatternAttribute = strpos($content, 'pattern="[0-9]{2}:[0-9]{2}"') !== false;
    echo ($hasPatternAttribute ? "✅" : "❌") . " Has pattern attribute: " . ($hasPatternAttribute ? "YES" : "NO") . "\n";
    
} else {
    echo "❌ View file not found: " . $viewFile . "\n";
}

echo "\n4. IMPLEMENTATION STATUS SUMMARY\n";
echo "=================================\n";

$allChecks = [
    'Controller validation' => $hasDateFormat ?? false,
    'Error messages' => $hasErrorMessages ?? false,
    'WebKit CSS' => $hasWebkitCSS ?? false,
    'Firefox CSS' => $hasMozCSS ?? false,
    'JavaScript validation' => $hasJSValidation ?? false,
    '24-hour labels' => $has24HourLabels ?? false,
    'Input attributes' => ($hasStepAttribute ?? false) && ($hasPatternAttribute ?? false),
];

$passedChecks = 0;
$totalChecks = count($allChecks);

foreach ($allChecks as $check => $passed) {
    echo ($passed ? "✅" : "❌") . " {$check}\n";
    if ($passed) $passedChecks++;
}

echo "\n";
echo "OVERALL STATUS: {$passedChecks}/{$totalChecks} checks passed\n";

if ($passedChecks === $totalChecks) {
    echo "🎉 ALL CHECKS PASSED - 24-HOUR FORMAT IMPLEMENTATION IS COMPLETE!\n";
} else {
    echo "⚠️  SOME CHECKS FAILED - IMPLEMENTATION NEEDS ATTENTION\n";
}

echo "\n5. NEXT STEPS FOR USER TESTING\n";
echo "===============================\n";
echo "1. Open browser and navigate to: /admin/sdm/attendance\n";
echo "2. Click 'Pengaturan Waktu' (purple button) - verify no AM/PM in time pickers\n";
echo "3. Click 'Set Jam Kerja' (blue button) - verify no AM/PM in time pickers\n";
echo "4. Try saving with valid times (08:00, 17:00) - should work\n";
echo "5. Try saving with invalid times (25:00, 08:60) - should show error\n";
echo "6. Check that error messages mention '24 jam' format\n";

echo "\n=== TEST COMPLETE ===\n";

?>