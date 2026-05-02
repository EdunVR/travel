<?php

/**
 * Final debug test for regex delimiter issue
 */

echo "🔍 Final debug test for regex delimiter issue...\n\n";

// Test the exact validation that's failing
echo "📋 Testing exact validation rules from AttendanceManagementController...\n";

$rules = [
    'employee_id' => 'required|exists:recruitments,id',
    'date' => 'required|date',
    'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
    'status' => 'required|in:present,late,absent,leave,sick,permission',
    'notes' => 'nullable|string',
];

$testData = [
    'employee_id' => 1,
    'date' => '2026-01-27',
    'clock_in' => '08:30:15',
    'clock_out' => '17:45:30',
    'status' => 'present',
    'notes' => 'Test'
];

echo "🧪 Testing each regex rule individually...\n";

$timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];

foreach ($timeFields as $field) {
    $rule = $rules[$field];
    $value = $testData[$field] ?? null;
    
    echo "\n📝 Testing field: $field\n";
    echo "   Rule: $rule\n";
    echo "   Value: " . ($value ?? 'null') . "\n";
    
    if ($value) {
        // Extract the regex pattern
        if (preg_match('/regex:(.+)/', $rule, $matches)) {
            $pattern = $matches[1];
            echo "   Pattern: $pattern\n";
            
            // Test the pattern
            try {
                $result = preg_match($pattern, $value);
                if ($result === false) {
                    echo "   ❌ REGEX ERROR: " . preg_last_error_msg() . "\n";
                } else {
                    echo "   ✅ Pattern match result: " . ($result ? 'MATCH' : 'NO MATCH') . "\n";
                }
            } catch (Exception $e) {
                echo "   ❌ EXCEPTION: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "   ⚪ Field is null (nullable rule)\n";
    }
}

echo "\n🌐 Testing with Laravel Validator...\n";

// Test with Laravel's validator
try {
    $validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);
    
    if ($validator->fails()) {
        echo "❌ Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "✅ Validation passed!\n";
    }
} catch (Exception $e) {
    echo "❌ VALIDATOR EXCEPTION: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n🔍 Checking for any cached validation rules...\n";

// Check if there are any cached files that might contain old validation rules
$cacheDirectories = [
    'bootstrap/cache',
    'storage/framework/cache',
    'storage/framework/views',
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        echo "📁 Checking cache directory: $dir\n";
        
        $files = glob($dir . '/*');
        $count = count($files);
        echo "   Found $count cached files\n";
        
        // Look for any files that might contain regex patterns
        foreach ($files as $file) {
            if (is_file($file) && filesize($file) > 0) {
                $content = file_get_contents($file);
                if (strpos($content, 'regex:/') !== false) {
                    echo "   ⚠️ Found regex pattern in: " . basename($file) . "\n";
                }
            }
        }
    }
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "1. If validation still fails, the issue might be in a different controller or middleware\n";
echo "2. Check if there are any custom validation rules defined\n";
echo "3. Verify that the error is actually coming from the attendance form\n";
echo "4. Consider temporarily using date_format:H:i validation instead of regex\n";

echo "\n🚀 Debug test complete!\n";

?>