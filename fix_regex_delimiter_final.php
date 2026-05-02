<?php

/**
 * Fix untuk menambahkan delimiter yang hilang pada regex pattern
 */

echo "🔧 Fixing regex delimiter issue...\n\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';

if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

// Backup file
$backupFile = $controllerFile . '.backup-regex-delimiter-fix.' . date('Y-m-d-H-i-s');
copy($controllerFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($controllerFile);

// Find and fix regex patterns without proper delimiters
$fixes = [
    // Pattern 1: regex without delimiters
    "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
    
    // Pattern 2: if there are patterns without starting delimiter
    "'regex:^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
    
    // Pattern 3: if there are patterns with only starting delimiter
    "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
];

$changedPatterns = 0;

// Check current patterns first
echo "🔍 Checking current regex patterns...\n";

// Look for all regex patterns in the file
if (preg_match_all("/'[^']*regex[^']*'/", $content, $matches)) {
    echo "Found regex patterns:\n";
    foreach ($matches[0] as $match) {
        echo "   " . $match . "\n";
        
        // Check if it's missing ending delimiter
        if (strpos($match, 'regex:/') !== false && !preg_match("/regex:\/.*\/[gimxs]*'/", $match)) {
            echo "   ⚠️ Missing ending delimiter: $match\n";
        }
    }
} else {
    echo "   ✅ No regex patterns found\n";
}

echo "\n";

// Apply fixes
foreach ($fixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedPatterns++;
        echo "✅ Fixed regex pattern: $old\n";
    }
}

// Additional comprehensive fix - find any regex pattern without proper delimiters
$regexPatterns = [
    // Fix patterns that might be malformed
    "/('regex:)([^\/])([^']+)(')/i" => "$1/$2$3/$4",
    "/('regex:\/[^']+)([^\/])(')/i" => "$1/$3",
];

foreach ($regexPatterns as $pattern => $replacement) {
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        $changedPatterns++;
        echo "✅ Applied comprehensive regex fix\n";
    }
}

// Manual fix for specific known patterns
$manualFixes = [
    // Ensure all time validation patterns have proper delimiters
    "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    
    // Required patterns for setWorkHours
    "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    
    // Time settings patterns
    "'settings.*.start_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'settings.*.start_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    "'settings.*.end_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'settings.*.end_time' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
];

foreach ($manualFixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $changedPatterns++;
        echo "✅ Applied manual fix: " . substr($old, 0, 50) . "...\n";
    }
}

// Save the updated content
file_put_contents($controllerFile, $content);

echo "\n📊 SUMMARY:\n";
echo "✅ Regex patterns fixed: $changedPatterns\n";
echo "✅ Controller file updated successfully\n";

echo "\n🧪 TESTING REGEX PATTERNS:\n";

// Test the corrected regex pattern
$testPattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';

$testTimes = [
    '08:30' => 'HH:MM format',
    '16:21:22' => 'HH:MM:SS format',
    '23:59' => 'HH:MM late night',
    '00:00:00' => 'HH:MM:SS midnight',
    '25:00' => 'Invalid hour (should fail)',
    '12:60' => 'Invalid minute (should fail)',
];

foreach ($testTimes as $time => $description) {
    $isValid = preg_match($testPattern, $time);
    $status = $isValid ? '✅' : '❌';
    echo "$status $time - $description\n";
}

echo "\n🔍 FINAL VERIFICATION:\n";

// Check the updated file for proper regex patterns
$updatedContent = file_get_contents($controllerFile);

// Count properly formatted regex patterns
$properRegexCount = preg_match_all("/regex:\/[^']+\/[gimxs]*'/", $updatedContent, $matches);
echo "✅ Found $properRegexCount properly formatted regex patterns\n";

// Check for any remaining malformed patterns
if (preg_match_all("/'[^']*regex[^']*'/", $updatedContent, $allMatches)) {
    $malformedCount = 0;
    foreach ($allMatches[0] as $match) {
        if (!preg_match("/regex:\/.*\/[gimxs]*'/", $match)) {
            $malformedCount++;
            echo "⚠️ Potentially malformed pattern: $match\n";
        }
    }
    
    if ($malformedCount === 0) {
        echo "✅ All regex patterns are properly formatted\n";
    } else {
        echo "⚠️ Found $malformedCount potentially malformed patterns\n";
    }
} else {
    echo "✅ No regex patterns found (using alternative validation)\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Clear application cache: php artisan cache:clear\n";
echo "2. Clear browser cache (Ctrl+F5)\n";
echo "3. Test attendance form submission\n";
echo "4. Verify no more 'No ending delimiter' errors\n";

echo "\n🚀 Regex delimiter issue should now be fixed!\n";

?>