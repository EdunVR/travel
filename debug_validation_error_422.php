<?php

/**
 * Debug Validation Error 422
 * 
 * This script helps debug the specific validation error for start_time
 */

echo "=== DEBUG VALIDATION ERROR 422 ===\n\n";

echo "ERROR ANALYSIS:\n";
echo "===============\n";
echo "From console log: 'settings.0.start_time: Array(1)'\n";
echo "This means the first setting's start_time field failed validation\n\n";

echo "POSSIBLE CAUSES:\n";
echo "================\n";

$causes = [
    "Empty or null value" => "start_time is empty, null, or undefined",
    "Wrong format" => "Value is not in HH:MM format (e.g., '7:00' instead of '07:00')",
    "Invalid characters" => "Contains non-numeric characters or wrong separators",
    "12-hour format" => "Still contains AM/PM despite conversion attempts",
    "Seconds included" => "Format is HH:MM:SS instead of HH:MM",
    "Invalid time" => "Time like 25:00 or 08:60"
];

foreach ($causes as $cause => $description) {
    echo "❓ {$cause}:\n";
    echo "   {$description}\n\n";
}

echo "DEBUGGING STEPS:\n";
echo "================\n";

echo "1. Check what exact value is being sent:\n";
echo "   - Open browser console\n";
echo "   - Look for '🔍 Sending time settings data:' log\n";
echo "   - Check the start_time value for settings[0]\n\n";

echo "2. Check the validation rule:\n";
echo "   - Rule: 'required|date_format:H:i'\n";
echo "   - H = 24-hour format (00-23)\n";
echo "   - i = minutes with leading zeros (00-59)\n";
echo "   - Valid examples: '07:00', '14:30', '23:59'\n";
echo "   - Invalid examples: '7:00', '24:00', '08:60', '2:30 PM'\n\n";

echo "3. Test validation manually:\n";
echo "   - Use PHP to test the validation rule\n";
echo "   - See examples below\n\n";

echo "MANUAL VALIDATION TEST:\n";
echo "=======================\n";

// Test various time formats
$testValues = [
    '07:00' => 'Valid 24-hour format',
    '7:00' => 'Invalid - single digit hour',
    '14:30' => 'Valid afternoon time',
    '2:30 PM' => 'Invalid - 12-hour format',
    '24:00' => 'Invalid - hour 24',
    '08:60' => 'Invalid - minute 60',
    '' => 'Invalid - empty',
    null => 'Invalid - null',
    '08:30:00' => 'Invalid - includes seconds'
];

foreach ($testValues as $value => $description) {
    $valueStr = $value === null ? 'null' : "'{$value}'";
    
    // Test Laravel's date_format validation
    $isValid = false;
    if ($value !== null && $value !== '') {
        try {
            $dateTime = DateTime::createFromFormat('H:i', $value);
            $isValid = $dateTime && $dateTime->format('H:i') === $value;
        } catch (Exception $e) {
            $isValid = false;
        }
    }
    
    $status = $isValid ? "✅ PASS" : "❌ FAIL";
    echo "{$status} {$valueStr} - {$description}\n";
}

echo "\nCOMMON FIXES:\n";
echo "=============\n";

$fixes = [
    "Ensure leading zeros" => "Convert '7:00' to '07:00'",
    "Remove AM/PM" => "Convert '2:30 PM' to '14:30'",
    "Remove seconds" => "Convert '08:30:00' to '08:30'",
    "Validate range" => "Ensure hours 00-23, minutes 00-59",
    "Handle null/empty" => "Provide default value or make optional"
];

foreach ($fixes as $fix => $solution) {
    echo "🔧 {$fix}:\n";
    echo "   {$solution}\n\n";
}

echo "JAVASCRIPT DEBUGGING:\n";
echo "=====================\n";

echo "Add this to browser console to debug:\n\n";

echo "// Check current time settings data\n";
echo "console.log('Current timeSettings:', window.Alpine.store ? window.Alpine.store('attendance')?.timeSettings : 'Alpine store not found');\n\n";

echo "// Check individual setting values\n";
echo "const settings = document.querySelectorAll('input[type=\"time\"]');\n";
echo "settings.forEach((input, i) => {\n";
echo "  console.log(`Input ${i}:`, {\n";
echo "    value: input.value,\n";
echo "    length: input.value?.length,\n";
echo "    type: typeof input.value,\n";
echo "    model: input.getAttribute('x-model')\n";
echo "  });\n";
echo "});\n\n";

echo "// Test validation manually\n";
echo "function testTimeValidation(timeValue) {\n";
echo "  const regex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;\n";
echo "  const isValid = regex.test(timeValue);\n";
echo "  console.log(`Testing '${timeValue}': ${isValid ? 'VALID' : 'INVALID'}`);\n";
echo "  return isValid;\n";
echo "}\n\n";

echo "// Test current values\n";
echo "testTimeValidation('07:00'); // Should be true\n";
echo "testTimeValidation('7:00');  // Should be false\n";
echo "testTimeValidation('14:30'); // Should be true\n";
echo "testTimeValidation('2:30 PM'); // Should be false\n\n";

echo "EXPECTED CONSOLE OUTPUT:\n";
echo "========================\n";

echo "Look for these logs in browser console:\n";
echo "- '🔍 Sending time settings data:' - Shows exact data being sent\n";
echo "- '🔍 Individual settings check:' - Shows each setting's validation\n";
echo "- '🕐 Original time value:' - Shows time before conversion\n";
echo "- '✅ Converted to 24-hour:' - Shows time after conversion\n\n";

echo "If you see validation errors, the data being sent is not in correct format.\n";
echo "The ensureTimeFormat() function should convert it, but might not be working.\n\n";

echo "NEXT STEPS:\n";
echo "===========\n";
echo "1. Copy the exact data from '🔍 Sending time settings data:' log\n";
echo "2. Check if start_time values are in HH:MM format\n";
echo "3. If not, the ensureTimeFormat() function needs fixing\n";
echo "4. If yes, check if database records exist for the IDs\n";

echo "\n=== DEBUG COMPLETE ===\n";

?>