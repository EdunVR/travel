<?php

/**
 * Fix frontend time inputs to match backend HH:MM validation
 */

echo "🔧 Fixing frontend time inputs to match HH:MM validation...\n\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';

if (!file_exists($viewFile)) {
    echo "❌ View file not found: $viewFile\n";
    exit(1);
}

// Backup file
$backupFile = $viewFile . '.backup-time-format-fix.' . date('Y-m-d-H-i-s');
copy($viewFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($viewFile);

// Fix all time inputs to remove step="1" and update labels/placeholders
$fixes = [
    // Remove step="1" from all time inputs
    'type="time" step="1"' => 'type="time"',
    
    // Update labels to show only HH:MM format
    'Jam Masuk (HH:MM atau HH:MM:SS)' => 'Jam Masuk (HH:MM)',
    'Jam Keluar (HH:MM atau HH:MM:SS)' => 'Jam Keluar (HH:MM)',
    'Jam Mulai Istirahat (HH:MM atau HH:MM:SS)' => 'Jam Mulai Istirahat (HH:MM)',
    'Jam Selesai Istirahat (HH:MM atau HH:MM:SS)' => 'Jam Selesai Istirahat (HH:MM)',
    'Jam Lembur Masuk (HH:MM atau HH:MM:SS)' => 'Jam Lembur Masuk (HH:MM)',
    'Jam Lembur Keluar (HH:MM atau HH:MM:SS)' => 'Jam Lembur Keluar (HH:MM)',
    'Jam Pulang (HH:MM atau HH:MM:SS)' => 'Jam Pulang (HH:MM)',
    'Jam Mulai (HH:MM atau HH:MM:SS)' => 'Jam Mulai (HH:MM)',
    'Jam Selesai (HH:MM atau HH:MM:SS)' => 'Jam Selesai (HH:MM)',
    
    // Update placeholders
    'placeholder="HH:MM atau HH:MM:SS (24 jam)"' => 'placeholder="HH:MM (24 jam)"',
    
    // Update patterns to only accept HH:MM
    'pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"' => 'pattern="[0-9]{2}:[0-9]{2}"',
];

$changes = 0;

foreach ($fixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changes++;
        echo "✅ Fixed: " . substr($old, 0, 50) . "...\n";
    }
}

// Also need to update the CSS selectors that were previously fixed
$cssFixes = [
    // Update CSS selectors to not include step attribute
    'input[type="time"][step="1"]' => 'input[type="time"]',
];

foreach ($cssFixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changes++;
        echo "✅ Fixed CSS selector: $old\n";
    }
}

// Save the updated content
file_put_contents($viewFile, $content);

echo "\n📊 SUMMARY:\n";
echo "✅ Frontend fixes applied: $changes\n";
echo "✅ Time inputs now configured for HH:MM format only\n";

echo "\n🔄 CHANGES MADE:\n";
echo "- Removed step=\"1\" from all time inputs\n";
echo "- Updated all labels to show HH:MM format only\n";
echo "- Updated placeholders and patterns\n";
echo "- Fixed CSS selectors\n";

echo "\n⚠️ IMPORTANT:\n";
echo "- Time inputs now only accept HH:MM format (e.g., 08:30)\n";
echo "- Seconds input is disabled\n";
echo "- This matches the backend date_format:H:i validation\n";

echo "\n🧪 TESTING:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Open attendance form\n";
echo "3. Try entering time in HH:MM format (e.g., 08:30)\n";
echo "4. Submit form - should work without validation errors\n";

echo "\n🎯 EXPECTED BEHAVIOR:\n";
echo "✅ Time inputs show HH:MM format only\n";
echo "✅ No seconds picker visible\n";
echo "✅ Form accepts 08:30, 16:45, etc.\n";
echo "✅ No more 422 validation errors\n";

echo "\n🚀 Frontend time format fix complete!\n";

?>