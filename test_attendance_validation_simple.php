<?php

require_once 'vendor/autoload.php';

echo "🔍 TESTING ATTENDANCE VALIDATION LOGIC\n";
echo "=====================================\n\n";

// Test the exact validation that Laravel uses
function testDateFormatValidation($value, $format = 'H:i') {
    try {
        $dateTime = DateTime::createFromFormat($format, $value);
        $isValid = $dateTime && $dateTime->format($format) === $value;
        return $isValid;
    } catch (Exception $e) {
        return false;
    }
}

// Test various time formats that users might input
$testCases = [
    // Valid HH:MM formats
    '08:30' => 'Valid HH:MM',
    '17:00' => 'Valid HH:MM',
    '00:00' => 'Valid midnight',
    '23:59' => 'Valid late night',
    '12:00' => 'Valid noon',
    
    // Invalid formats that might cause 422
    '8:30' => 'Single digit hour (should fail)',
    '08:3' => 'Single digit minute (should fail)',
    '08:30:00' => 'With seconds (should fail for H:i)',
    '25:00' => 'Invalid hour (should fail)',
    '08:60' => 'Invalid minute (should fail)',
    '' => 'Empty string (should pass as nullable)',
    null => 'Null value (should pass as nullable)',
    '08-30' => 'Wrong separator (should fail)',
    '08.30' => 'Wrong separator (should fail)',
    'abc' => 'Non-numeric (should fail)',
];

echo "📊 Testing date_format:H:i validation:\n\n";

foreach ($testCases as $value => $description) {
    $isValid = testDateFormatValidation($value);
    $status = $isValid ? '✅' : '❌';
    $valueDisplay = $value === null ? 'null' : "'$value'";
    echo "$status $valueDisplay - $description\n";
}

echo "\n🔍 CHECKING CONTROLLER VALIDATION RULES:\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for date_format:H:i rules
    $dateFormatCount = substr_count($content, 'date_format:H:i');
    echo "✅ Found $dateFormatCount date_format:H:i rules in controller\n";
    
    // Check for any remaining regex patterns
    $regexCount = substr_count($content, 'regex:/');
    if ($regexCount > 0) {
        echo "⚠️  Found $regexCount regex patterns (potential delimiter issues)\n";
    } else {
        echo "✅ No regex patterns found (good!)\n";
    }
    
    // Check error messages
    $errorMessageCount = substr_count($content, '.date_format');
    echo "✅ Found $errorMessageCount date_format error messages\n";
    
} else {
    echo "❌ Controller file not found: $controllerFile\n";
}

echo "\n🔍 CHECKING FRONTEND TIME INPUT FORMAT:\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for step attribute (which might cause HH:MM:SS format)
    $stepCount = substr_count($content, 'step="1"');
    if ($stepCount > 0) {
        echo "⚠️  Found $stepCount time inputs with step=\"1\" (causes HH:MM:SS format)\n";
    } else {
        echo "✅ No step=\"1\" attributes found (good for HH:MM format)\n";
    }
    
    // Check for time input labels
    $hhmmCount = substr_count($content, '(HH:MM)');
    echo "✅ Found $hhmmCount labels indicating HH:MM format\n";
    
} else {
    echo "❌ View file not found: $viewFile\n";
}

echo "\n🔧 COMMON CAUSES OF 422 VALIDATION ERROR:\n";
echo "1. Frontend sending HH:MM:SS but backend expects HH:MM\n";
echo "2. Frontend sending single-digit hours/minutes (8:30 vs 08:30)\n";
echo "3. Browser auto-formatting time inputs differently\n";
echo "4. JavaScript modifying time values before submission\n";
echo "5. Missing required fields (employee_id, date, status)\n";

echo "\n💡 DEBUGGING STEPS:\n";
echo "1. Open browser DevTools → Network tab\n";
echo "2. Submit attendance form\n";
echo "3. Check the request payload in Network tab\n";
echo "4. Verify exact format of time fields being sent\n";
echo "5. Compare with validation rules in controller\n";

echo "\n✅ Validation test complete!\n";