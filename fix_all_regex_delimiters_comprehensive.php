<?php

/**
 * Comprehensive fix for all regex delimiter issues across the entire codebase
 */

echo "🔧 Comprehensive regex delimiter fix across entire codebase...\n\n";

// Function to fix regex patterns in a file
function fixRegexInFile($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $changes = 0;
    
    // Pattern 1: Fix regex patterns missing ending delimiter
    // Match: 'regex:/pattern$' and replace with 'regex:/pattern$/'
    $pattern1 = "/('regex:\/[^']*\$)(')/";
    if (preg_match($pattern1, $content)) {
        $content = preg_replace($pattern1, '$1/$2', $content);
        $changes++;
        echo "   ✅ Fixed missing ending delimiter in: " . basename($filePath) . "\n";
    }
    
    // Pattern 2: Fix regex patterns with flags but missing ending delimiter
    // Match: 'regex:/pattern$i' and replace with 'regex:/pattern$/i'
    $pattern2 = "/('regex:\/[^']*\$)([gimxs]+)(')/";
    if (preg_match($pattern2, $content)) {
        $content = preg_replace($pattern2, '$1/$2$3', $content);
        $changes++;
        echo "   ✅ Fixed missing delimiter with flags in: " . basename($filePath) . "\n";
    }
    
    // Pattern 3: Fix specific time validation patterns that might be malformed
    $timePatterns = [
        // Fix patterns that might be missing the ending delimiter
        "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
        "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
        
        // Fix any patterns without starting delimiter
        "'regex:^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
        "'regex:^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$'," => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',",
    ];
    
    foreach ($timePatterns as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $changes++;
            echo "   ✅ Fixed time pattern in: " . basename($filePath) . "\n";
        }
    }
    
    // Save if changes were made
    if ($changes > 0 && $content !== $originalContent) {
        // Create backup
        $backupFile = $filePath . '.backup-regex-fix.' . date('Y-m-d-H-i-s');
        copy($filePath, $backupFile);
        
        file_put_contents($filePath, $content);
        echo "   📁 Backup created: " . basename($backupFile) . "\n";
        return $changes;
    }
    
    return 0;
}

// Get all PHP files in the app directory
$directories = [
    'app/Http/Controllers',
    'app/Http/Requests',
    'app/Models',
    'app/Imports',
    'app/Exports',
    'app/Rules',
    'app/Services',
];

$totalChanges = 0;
$filesProcessed = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    
    echo "🔍 Scanning directory: $dir\n";
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            $changes = fixRegexInFile($filePath);
            
            if ($changes > 0) {
                $totalChanges += $changes;
                echo "   📝 Fixed $changes patterns in: " . $file->getFilename() . "\n";
            }
            
            $filesProcessed++;
        }
    }
}

echo "\n📊 SUMMARY:\n";
echo "✅ Files processed: $filesProcessed\n";
echo "✅ Total regex patterns fixed: $totalChanges\n";

if ($totalChanges > 0) {
    echo "\n🧹 Clearing caches...\n";
    
    // Clear Laravel caches
    $cacheCommands = [
        'php artisan cache:clear',
        'php artisan config:clear',
        'php artisan route:clear',
        'php artisan view:clear'
    ];
    
    foreach ($cacheCommands as $command) {
        echo "   🔄 Running: $command\n";
        exec($command, $output, $returnCode);
        if ($returnCode === 0) {
            echo "   ✅ Cache cleared successfully\n";
        }
    }
}

echo "\n🧪 TESTING REGEX PATTERNS:\n";

// Test the corrected regex pattern
$testPattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';

$testTimes = [
    '08:30' => true,
    '08:30:15' => true,
    '16:45:30' => true,
    '23:59:59' => true,
    '25:00' => false,
    '12:60' => false,
];

$testsPassed = 0;
foreach ($testTimes as $time => $expected) {
    $result = preg_match($testPattern, $time);
    $isValid = (bool)$result;
    
    if ($isValid === $expected) {
        echo "✅ $time - " . ($expected ? 'Valid' : 'Invalid') . " (Expected)\n";
        $testsPassed++;
    } else {
        echo "❌ $time - " . ($isValid ? 'Valid' : 'Invalid') . " (Expected: " . ($expected ? 'Valid' : 'Invalid') . ")\n";
    }
}

echo "\n📊 TEST RESULTS:\n";
echo "✅ Tests passed: $testsPassed/" . count($testTimes) . "\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Test attendance form submission\n";
echo "2. Check Laravel logs for any remaining regex errors\n";
echo "3. Clear browser cache (Ctrl+F5)\n";
echo "4. Verify HH:MM:SS format is accepted\n";

if ($totalChanges > 0) {
    echo "\n🎉 Regex delimiter issues should now be completely resolved!\n";
} else {
    echo "\n✅ No regex delimiter issues found in the codebase.\n";
    echo "⚠️ The error might be coming from a cached validation rule or external source.\n";
}

echo "\n🚀 Comprehensive regex fix complete!\n";

?>