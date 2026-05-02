<?php

/**
 * Fix untuk membuat attendance validation menerima format HH:MM:SS
 */

echo "🔧 Fixing attendance validation to accept HH:MM:SS format...\n\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';

if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

// Backup file
$backupFile = $controllerFile . '.backup-hhmmss-support.' . date('Y-m-d-H-i-s');
copy($controllerFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($controllerFile);

// 1. Replace validation rules to accept both HH:MM and HH:MM:SS
$oldValidationRules = [
    "'clock_in' => 'nullable|date_format:H:i'," => "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'clock_out' => 'nullable|date_format:H:i'," => "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'break_in' => 'nullable|date_format:H:i'," => "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'break_out' => 'nullable|date_format:H:i'," => "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'overtime_in' => 'nullable|date_format:H:i'," => "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'overtime_out' => 'nullable|date_format:H:i'," => "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
];

// 2. Replace error messages to reflect both formats
$oldErrorMessages = [
    "'clock_in.date_format' => 'Format jam masuk harus HH:MM (24 jam)'," => "'clock_in.regex' => 'Format jam masuk harus HH:MM atau HH:MM:SS (24 jam)',",
    "'clock_out.date_format' => 'Format jam keluar harus HH:MM (24 jam)'," => "'clock_out.regex' => 'Format jam keluar harus HH:MM atau HH:MM:SS (24 jam)',",
    "'clock_out.date_format' => 'Format jam pulang harus HH:MM (24 jam)'," => "'clock_out.regex' => 'Format jam pulang harus HH:MM atau HH:MM:SS (24 jam)',",
    "'break_in.date_format' => 'Format jam mulai istirahat harus HH:MM (24 jam)'," => "'break_in.regex' => 'Format jam mulai istirahat harus HH:MM atau HH:MM:SS (24 jam)',",
    "'break_out.date_format' => 'Format jam selesai istirahat harus HH:MM (24 jam)'," => "'break_out.regex' => 'Format jam selesai istirahat harus HH:MM atau HH:MM:SS (24 jam)',",
    "'overtime_in.date_format' => 'Format jam lembur masuk harus HH:MM (24 jam)'," => "'overtime_in.regex' => 'Format jam lembur masuk harus HH:MM atau HH:MM:SS (24 jam)',",
    "'overtime_out.date_format' => 'Format jam lembur keluar harus HH:MM (24 jam)'," => "'overtime_out.regex' => 'Format jam lembur keluar harus HH:MM atau HH:MM:SS (24 jam)',",
];

// 3. Special case for setWorkHours method - update both validation rules
$setWorkHoursValidation = [
    "'clock_in' => 'required|date_format:H:i'," => "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'clock_out' => 'required|date_format:H:i'," => "'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
];

// Apply validation rule changes
$changedRules = 0;
foreach ($oldValidationRules as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedRules++;
        echo "✅ Updated validation rule: " . trim($old) . "\n";
    }
}

// Apply setWorkHours validation changes
foreach ($setWorkHoursValidation as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedRules++;
        echo "✅ Updated setWorkHours validation: " . trim($old) . "\n";
    }
}

// Apply error message changes
$changedMessages = 0;
foreach ($oldErrorMessages as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedMessages++;
        echo "✅ Updated error message: " . trim($old) . "\n";
    }
}

// 4. Update time settings validation as well
$timeSettingsValidation = [
    "'settings.*.start_time' => 'required|date_format:H:i'," => "'settings.*.start_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'settings.*.end_time' => 'required|date_format:H:i'," => "'settings.*.end_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
];

$timeSettingsMessages = [
    "'settings.*.start_time.date_format' => 'Format jam mulai harus HH:MM (24 jam)'," => "'settings.*.start_time.regex' => 'Format jam mulai harus HH:MM atau HH:MM:SS (24 jam)',",
    "'settings.*.end_time.date_format' => 'Format jam selesai harus HH:MM (24 jam)'," => "'settings.*.end_time.regex' => 'Format jam selesai harus HH:MM atau HH:MM:SS (24 jam)',",
];

// Apply time settings changes
foreach ($timeSettingsValidation as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedRules++;
        echo "✅ Updated time settings validation: " . trim($old) . "\n";
    }
}

foreach ($timeSettingsMessages as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedMessages++;
        echo "✅ Updated time settings message: " . trim($old) . "\n";
    }
}

// Save the updated content
file_put_contents($controllerFile, $content);

echo "\n📊 SUMMARY:\n";
echo "✅ Validation rules updated: $changedRules\n";
echo "✅ Error messages updated: $changedMessages\n";
echo "✅ Controller file updated successfully\n";

echo "\n🧪 TESTING REGEX PATTERN:\n";

// Test the regex pattern
$testTimes = [
    '08:30' => 'HH:MM format',
    '16:21:22' => 'HH:MM:SS format',
    '23:59' => 'HH:MM late night',
    '00:00:00' => 'HH:MM:SS midnight',
    '8:30' => 'Single digit hour (should pass)',
    '25:00' => 'Invalid hour (should fail)',
    '12:60' => 'Invalid minute (should fail)',
    '12:30:60' => 'Invalid second (should fail)',
];

$pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';

foreach ($testTimes as $time => $description) {
    $isValid = preg_match($pattern, $time);
    $status = $isValid ? '✅' : '❌';
    echo "$status $time - $description\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test attendance form with both formats:\n";
echo "   - HH:MM format: 08:30, 16:21\n";
echo "   - HH:MM:SS format: 08:30:00, 16:21:22\n";
echo "3. Verify no more 422 validation errors\n";

echo "\n🚀 Attendance validation now accepts both HH:MM and HH:MM:SS formats!\n";

?>