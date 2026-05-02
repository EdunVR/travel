<?php

/**
 * Fix frontend untuk mendukung format HH:MM:SS
 */

echo "🔧 Fixing frontend to support HH:MM:SS format...\n\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';

if (!file_exists($viewFile)) {
    echo "❌ View file not found: $viewFile\n";
    exit(1);
}

// Backup file
$backupFile = $viewFile . '.backup-hhmmss-frontend.' . date('Y-m-d-H-i-s');
copy($viewFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($viewFile);

// 1. Add step="1" to all time inputs to enable seconds
$timeInputUpdates = [
    'type="time"' => 'type="time" step="1"'
];

// 2. Update pattern to accept both HH:MM and HH:MM:SS
$patternUpdates = [
    'pattern="[0-9]{2}:[0-9]{2}"' => 'pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"'
];

// 3. Update placeholder text
$placeholderUpdates = [
    'placeholder="HH:MM (24 jam)"' => 'placeholder="HH:MM atau HH:MM:SS (24 jam)"',
    'placeholder="HH:MM"' => 'placeholder="HH:MM atau HH:MM:SS"'
];

// Apply time input updates
$changedInputs = 0;
foreach ($timeInputUpdates as $old => $new) {
    // Only add step="1" if it doesn't already exist
    if (strpos($content, 'step="1"') === false) {
        $content = str_replace($old, $new, $content);
        $changedInputs = substr_count($content, 'step="1"');
        echo "✅ Added step='1' to time inputs: $changedInputs inputs updated\n";
    } else {
        echo "✅ step='1' already exists in time inputs\n";
    }
}

// Apply pattern updates
$changedPatterns = 0;
foreach ($patternUpdates as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $changedPatterns += $count;
        echo "✅ Updated pattern attribute: $count patterns updated\n";
    }
}

// Apply placeholder updates
$changedPlaceholders = 0;
foreach ($placeholderUpdates as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $changedPlaceholders += $count;
        echo "✅ Updated placeholder text: $count placeholders updated\n";
    }
}

// 4. Update label text to reflect both formats
$labelUpdates = [
    'Jam Masuk (24 jam)' => 'Jam Masuk (HH:MM atau HH:MM:SS)',
    'Jam Keluar (24 jam)' => 'Jam Keluar (HH:MM atau HH:MM:SS)',
    'Jam Pulang (24 jam)' => 'Jam Pulang (HH:MM atau HH:MM:SS)',
    'Jam Mulai Istirahat (24 jam)' => 'Jam Mulai Istirahat (HH:MM atau HH:MM:SS)',
    'Jam Selesai Istirahat (24 jam)' => 'Jam Selesai Istirahat (HH:MM atau HH:MM:SS)',
    'Jam Lembur Masuk (24 jam)' => 'Jam Lembur Masuk (HH:MM atau HH:MM:SS)',
    'Jam Lembur Keluar (24 jam)' => 'Jam Lembur Keluar (HH:MM atau HH:MM:SS)',
    'Jam Mulai (24 jam)' => 'Jam Mulai (HH:MM atau HH:MM:SS)',
    'Jam Selesai (24 jam)' => 'Jam Selesai (HH:MM atau HH:MM:SS)'
];

$changedLabels = 0;
foreach ($labelUpdates as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $changedLabels += $count;
        echo "✅ Updated label text: '$old' -> '$new'\n";
    }
}

// 5. Update JavaScript validation if exists
$jsValidationUpdates = [
    '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/' => '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'Format jam.*tidak valid.*HH:MM' => 'Format jam tidak valid. Gunakan HH:MM atau HH:MM:SS'
];

$changedJS = 0;
foreach ($jsValidationUpdates as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedJS++;
        echo "✅ Updated JavaScript validation: $old\n";
    }
}

// Save the updated content
file_put_contents($viewFile, $content);

echo "\n📊 SUMMARY:\n";
echo "✅ Time inputs updated: $changedInputs\n";
echo "✅ Patterns updated: $changedPatterns\n";
echo "✅ Placeholders updated: $changedPlaceholders\n";
echo "✅ Labels updated: $changedLabels\n";
echo "✅ JavaScript validations updated: $changedJS\n";
echo "✅ View file updated successfully\n";

echo "\n🧪 TESTING FRONTEND CHANGES:\n";

// Check if changes were applied correctly
$updatedContent = file_get_contents($viewFile);

if (strpos($updatedContent, 'step="1"') !== false) {
    echo "✅ step='1' attribute found - seconds input enabled\n";
} else {
    echo "❌ step='1' attribute not found\n";
}

if (strpos($updatedContent, 'pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"') !== false) {
    echo "✅ Updated pattern found - accepts both HH:MM and HH:MM:SS\n";
} else {
    echo "❌ Updated pattern not found\n";
}

if (strpos($updatedContent, 'HH:MM atau HH:MM:SS') !== false) {
    echo "✅ Updated labels found - shows both formats\n";
} else {
    echo "❌ Updated labels not found\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Open attendance management page\n";
echo "3. Test time inputs - should now show seconds picker\n";
echo "4. Try submitting with both formats:\n";
echo "   - HH:MM: 08:30, 16:21\n";
echo "   - HH:MM:SS: 08:30:00, 16:21:22\n";
echo "5. Verify no more 422 validation errors\n";

echo "\n🚀 Frontend now supports both HH:MM and HH:MM:SS formats!\n";

?>