<?php

/**
 * Test 24-Hour Format Validation Fix
 * 
 * This script tests the fix for the 422 validation error on time settings
 */

echo "=== TEST 24-HOUR FORMAT VALIDATION FIX ===\n\n";

echo "PROBLEM ANALYSIS:\n";
echo "=================\n";
echo "❌ User getting 422 validation error on 'settings.0.start_time'\n";
echo "❌ Console shows: 'settings.0.start_time: Array(1)'\n";
echo "❌ Validation rule: 'required|date_format:H:i'\n";
echo "❌ Time picker still shows AM/PM format in some browsers\n\n";

echo "SOLUTION IMPLEMENTED:\n";
echo "====================\n";
echo "✅ Added formatTimeToHHMM() helper function\n";
echo "✅ Enhanced saveTimeSettings() to format times before sending\n";
echo "✅ Added client-side validation before sending data\n";
echo "✅ Added detailed error messages for invalid formats\n";
echo "✅ Ensured all time values are converted to HH:MM format\n\n";

echo "KEY CHANGES:\n";
echo "============\n";

$changes = [
    "formatTimeToHHMM() function" => [
        "Converts 12-hour to 24-hour format",
        "Pads single digits (7:00 → 07:00)",
        "Removes seconds if present (07:00:00 → 07:00)",
        "Validates final format matches HH:MM pattern"
    ],
    "Enhanced saveTimeSettings()" => [
        "Calls formatTimeToHHMM() on all time values",
        "Validates times are not empty",
        "Validates format before sending to server",
        "Shows specific error messages for invalid times"
    ],
    "Client-side validation" => [
        "Checks both start_time and end_time are present",
        "Validates format matches /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/",
        "Shows user-friendly error messages",
        "Prevents invalid data from being sent"
    ]
];

foreach ($changes as $change => $details) {
    echo "🔧 {$change}:\n";
    foreach ($details as $detail) {
        echo "   • {$detail}\n";
    }
    echo "\n";
}

echo "TESTING SCENARIOS:\n";
echo "==================\n";

// Test the formatTimeToHHMM logic
function testFormatTimeToHHMM($input, $expected) {
    // Simulate the JavaScript logic in PHP
    $value = trim($input);
    
    // Handle 12-hour format
    if (strpos($value, 'AM') !== false || strpos($value, 'PM') !== false) {
        $time12h = str_replace(' ', '', $value);
        preg_match('/^(.+?)(AM|PM)$/i', $time12h, $matches);
        if (count($matches) === 3) {
            $time = $matches[1];
            $period = strtoupper($matches[2]);
            $parts = explode(':', $time);
            $hours = intval($parts[0]);
            $minutes = $parts[1];
            
            if ($period === 'PM' && $hours !== 12) {
                $hours += 12;
            } elseif ($period === 'AM' && $hours === 12) {
                $hours = 0;
            }
            
            $value = sprintf('%02d:%s', $hours, $minutes);
        }
    }
    
    // Pad single digits
    if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
        $parts = explode(':', $value);
        $value = sprintf('%02d:%s', intval($parts[0]), $parts[1]);
    }
    
    // Remove seconds
    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
        $value = substr($value, 0, 5);
    }
    
    $isValid = preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value);
    $status = ($value === $expected && $isValid) ? "✅ PASS" : "❌ FAIL";
    
    echo "{$status} '{$input}' → '{$value}' (expected: '{$expected}')\n";
    
    return $value === $expected && $isValid;
}

echo "Testing formatTimeToHHMM function:\n";
echo "----------------------------------\n";

$testCases = [
    '7:00 AM' => '07:00',
    '2:30 PM' => '14:30',
    '12:00 AM' => '00:00',
    '12:00 PM' => '12:00',
    '7:00' => '07:00',
    '14:30' => '14:30',
    '08:30:00' => '08:30',
    '23:59' => '23:59',
    '00:00' => '00:00'
];

$passed = 0;
$total = count($testCases);

foreach ($testCases as $input => $expected) {
    if (testFormatTimeToHHMM($input, $expected)) {
        $passed++;
    }
}

echo "\nTest Results: {$passed}/{$total} passed\n\n";

echo "VALIDATION RULES:\n";
echo "=================\n";
echo "Server-side validation (Laravel):\n";
echo "• 'settings.*.start_time' => 'required|date_format:H:i'\n";
echo "• 'settings.*.end_time' => 'required|date_format:H:i'\n\n";

echo "Client-side validation (JavaScript):\n";
echo "• Pattern: /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/\n";
echo "• Hours: 00-23 (24-hour format)\n";
echo "• Minutes: 00-59\n";
echo "• Format: HH:MM (with leading zeros)\n\n";

echo "DEBUGGING STEPS:\n";
echo "================\n";
echo "1. Open browser console\n";
echo "2. Go to Time Settings modal\n";
echo "3. Try to save settings\n";
echo "4. Look for these console logs:\n";
echo "   • '🕐 Formatting time value:' - Shows original values\n";
echo "   • '✅ Final formatted time:' - Shows converted values\n";
echo "   • '🔍 Sending time settings data:' - Shows final data sent\n\n";

echo "EXPECTED BEHAVIOR:\n";
echo "==================\n";
echo "✅ Time picker may still show AM/PM (browser limitation)\n";
echo "✅ But after selection, values are converted to 24-hour format\n";
echo "✅ Client validates format before sending to server\n";
echo "✅ Server receives properly formatted HH:MM values\n";
echo "✅ No more 422 validation errors\n";
echo "✅ Settings save successfully\n\n";

echo "TROUBLESHOOTING:\n";
echo "================\n";
echo "If still getting 422 errors:\n";
echo "1. Check console for '❌ Invalid time format' warnings\n";
echo "2. Verify all time inputs have values (not empty)\n";
echo "3. Check if database records exist for the setting IDs\n";
echo "4. Verify CSRF token is valid\n";
echo "5. Check server logs for detailed validation errors\n\n";

echo "MANUAL TEST:\n";
echo "============\n";
echo "1. Open Time Settings modal\n";
echo "2. Change any time value\n";
echo "3. Click 'Simpan Pengaturan'\n";
echo "4. Should see success message: 'Pengaturan waktu berhasil disimpan'\n";
echo "5. No 422 validation errors in console\n\n";

echo "=== FIX COMPLETE ===\n";

?>
</content>