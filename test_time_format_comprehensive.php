<?php
function validateTimeFormat($timeString) {
    if (empty($timeString)) return false;
    
    // Remove seconds if present
    $timeParts = explode(":", $timeString);
    if (count($timeParts) === 3) {
        $timeString = $timeParts[0] . ":" . $timeParts[1];
    }
    
    // Pad single digit hours
    if (preg_match('/^\d{1}:\d{2}$/', $timeString)) {
        $timeString = "0" . $timeString;
    }
    
    // Validate H:i format
    return preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $timeString);
}


/**
 * Comprehensive test for time format fix
 */

echo "=== COMPREHENSIVE TIME FORMAT TEST ===\n";

// Test various time formats
$testCases = [
    "08:30" => true,    // Valid H:i
    "14:15" => true,    // Valid H:i
    "23:59" => true,    // Valid H:i
    "00:00" => true,    // Valid H:i
    "8:30" => true,     // Should be padded to 08:30
    "08:30:00" => true, // Should remove seconds
    "25:00" => false,   // Invalid hour
    "12:60" => false,   // Invalid minute
    "" => false,        // Empty
];

echo "\n1. Testing time format validation...\n";

foreach ($testCases as $timeString => $expected) {
    $isValid = validateTimeFormat($timeString);
    $result = $isValid ? "✓" : "✗";
    $status = ($isValid === $expected) ? "PASS" : "FAIL";
    
    echo sprintf("%-10s | %-5s | %s\n", $timeString, $result, $status);
}

// Test controller validation
echo "\n2. Testing controller validation rules...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];
    
    foreach ($timeFields as $field) {
        if (strpos($content, "'$field' => 'nullable|date_format:H:i'") !== false) {
            echo "✓ $field validation rule is correct\n";
        } else if (strpos($content, "'$field'") !== false) {
            echo "? $field validation rule found but may need checking\n";
        } else {
            echo "✗ $field validation rule not found\n";
        }
    }
} else {
    echo "✗ Controller file not found\n";
}

// Test view file changes
echo "\n3. Testing view file changes...\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check if step="1" is removed
    $stepCount = substr_count($content, 'step="1"');
    echo $stepCount === 0 ? "✓ All step=\"1\" attributes removed\n" : "✗ Found $stepCount step=\"1\" attributes\n";
    
    // Check if formatTimeToHHMM function exists
    echo strpos($content, 'formatTimeToHHMM') !== false ? "✓ formatTimeToHHMM function exists\n" : "✗ formatTimeToHHMM function missing\n";
    
    // Check if ensureTimeFormat function exists
    echo strpos($content, 'ensureTimeFormat') !== false ? "✓ ensureTimeFormat function exists\n" : "✗ ensureTimeFormat function missing\n";
    
} else {
    echo "✗ View file not found\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "If all tests pass, the time format issue should be resolved.\n";

?>