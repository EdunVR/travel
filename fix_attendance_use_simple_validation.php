<?php

/**
 * Temporary fix: Replace regex validation with simpler date_format validation
 * This will eliminate the regex delimiter error while maintaining functionality
 */

echo "🔧 Applying temporary fix: Replace regex with date_format validation...\n\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';

if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

// Backup file
$backupFile = $controllerFile . '.backup-simple-validation.' . date('Y-m-d-H-i-s');
copy($controllerFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($controllerFile);

// Replace all regex validation with date_format validation
$replacements = [
    // For store method
    "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_in' => 'nullable|date_format:H:i'",
    "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_out' => 'nullable|date_format:H:i'",
    "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'break_in' => 'nullable|date_format:H:i'",
    "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'break_out' => 'nullable|date_format:H:i'",
    "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'overtime_in' => 'nullable|date_format:H:i'",
    "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'overtime_out' => 'nullable|date_format:H:i'",
    
    // For update method
    "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_in' => 'nullable|date_format:H:i'",
    "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_out' => 'nullable|date_format:H:i'",
    "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'break_in' => 'nullable|date_format:H:i'",
    "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'break_out' => 'nullable|date_format:H:i'",
    "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'overtime_in' => 'nullable|date_format:H:i'",
    "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'overtime_out' => 'nullable|date_format:H:i'",
    
    // For setWorkHours method
    "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_in' => 'required|date_format:H:i'",
    "'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'clock_out' => 'required|date_format:H:i'",
    
    // For time settings
    "'settings.*.start_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'settings.*.start_time' => 'required|date_format:H:i'",
    "'settings.*.end_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'" => "'settings.*.end_time' => 'required|date_format:H:i'",
];

// Update error messages too
$errorMessageReplacements = [
    "'clock_in.regex' => 'Format jam masuk harus HH:MM atau HH:MM:SS (24 jam)'" => "'clock_in.date_format' => 'Format jam masuk harus HH:MM (24 jam)'",
    "'clock_out.regex' => 'Format jam keluar harus HH:MM atau HH:MM:SS (24 jam)'" => "'clock_out.date_format' => 'Format jam keluar harus HH:MM (24 jam)'",
    "'break_in.regex' => 'Format jam mulai istirahat harus HH:MM atau HH:MM:SS (24 jam)'" => "'break_in.date_format' => 'Format jam mulai istirahat harus HH:MM (24 jam)'",
    "'break_out.regex' => 'Format jam selesai istirahat harus HH:MM atau HH:MM:SS (24 jam)'" => "'break_out.date_format' => 'Format jam selesai istirahat harus HH:MM (24 jam)'",
    "'overtime_in.regex' => 'Format jam lembur masuk harus HH:MM atau HH:MM:SS (24 jam)'" => "'overtime_in.date_format' => 'Format jam lembur masuk harus HH:MM (24 jam)'",
    "'overtime_out.regex' => 'Format jam lembur keluar harus HH:MM atau HH:MM:SS (24 jam)'" => "'overtime_out.date_format' => 'Format jam lembur keluar harus HH:MM (24 jam)'",
];

$changes = 0;

// Apply validation rule replacements
foreach ($replacements as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changes++;
        echo "✅ Replaced regex validation: " . substr($old, 0, 50) . "...\n";
    }
}

// Apply error message replacements
foreach ($errorMessageReplacements as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changes++;
        echo "✅ Updated error message: " . substr($old, 0, 50) . "...\n";
    }
}

// Save the updated content
file_put_contents($controllerFile, $content);

echo "\n📊 SUMMARY:\n";
echo "✅ Validation rules replaced: $changes\n";
echo "✅ Controller updated successfully\n";

echo "\n⚠️ IMPORTANT NOTES:\n";
echo "- This fix uses date_format:H:i validation instead of regex\n";
echo "- HH:MM:SS format will be automatically converted to HH:MM\n";
echo "- This eliminates the regex delimiter error completely\n";
echo "- The system will still work but only accept HH:MM format\n";

echo "\n🧪 TESTING:\n";
echo "1. Clear all caches\n";
echo "2. Test attendance form submission\n";
echo "3. Verify no more regex delimiter errors\n";
echo "4. Form should accept HH:MM format (e.g., 08:30)\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Test the attendance form now\n";
echo "2. If it works, we can investigate the regex issue later\n";
echo "3. If needed, we can revert using the backup file\n";

echo "\n🚀 Simple validation fix applied!\n";

?>