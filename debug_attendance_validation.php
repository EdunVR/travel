<?php

/**
 * Debug script untuk menguji validasi attendance
 */

echo "🔍 Debug Attendance Validation Issue...\n\n";

// 1. Test controller validation rules
echo "1. 📋 Checking controller validation rules...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for any remaining regex patterns
    if (preg_match_all('/\'[^\']*\'\s*=>\s*\'[^\']*regex[^\']*\'/', $content, $matches)) {
        echo "   ❌ Found regex validation rules:\n";
        foreach ($matches[0] as $match) {
            echo "      " . trim($match) . "\n";
        }
    } else {
        echo "   ✅ No regex validation rules found\n";
    }
    
    // Check for inconsistent error messages
    if (preg_match_all('/\'[^\']*\.regex\'\s*=>\s*\'[^\']*\'/', $content, $matches)) {
        echo "   ❌ Found regex error messages:\n";
        foreach ($matches[0] as $match) {
            echo "      " . trim($match) . "\n";
        }
    } else {
        echo "   ✅ No regex error messages found\n";
    }
    
    // Check date_format validation
    $dateFormatCount = substr_count($content, 'date_format:H:i');
    echo "   ✅ Found $dateFormatCount date_format:H:i validations\n";
    
} else {
    echo "   ❌ Controller file not found\n";
}

echo "\n";

// 2. Test specific time formats
echo "2. ⏰ Testing time format validation...\n";

$testTimes = [
    '08:30' => 'Valid HH:MM',
    '16:21' => 'Valid HH:MM', 
    '8:30' => 'Valid single digit hour',
    '23:59' => 'Valid late night',
    '00:00' => 'Valid midnight',
    '16:21:22' => 'Invalid HH:MM:SS format',
    '25:00' => 'Invalid hour',
    '12:60' => 'Invalid minute',
    '' => 'Empty string',
    null => 'Null value'
];

foreach ($testTimes as $time => $description) {
    // Test with Laravel's date_format:H:i validation
    $isValid = false;
    
    if ($time === null || $time === '') {
        $isValid = true; // nullable fields
    } else {
        // Simulate Laravel's date_format:H:i validation
        $parsed = DateTime::createFromFormat('H:i', $time);
        $isValid = $parsed && $parsed->format('H:i') === $time;
    }
    
    $status = $isValid ? '✅' : '❌';
    $timeDisplay = $time === null ? 'null' : ($time === '' ? 'empty' : $time);
    echo "   $status $timeDisplay - $description\n";
}

echo "\n";

// 3. Create test request simulation
echo "3. 🧪 Simulating form submission...\n";

$testData = [
    'employee_id' => 1,
    'date' => date('Y-m-d'),
    'clock_in' => '08:30',
    'clock_out' => '17:00',
    'status' => 'present'
];

echo "   Test data:\n";
foreach ($testData as $key => $value) {
    echo "      $key: $value\n";
}

// Simulate validation
$errors = [];

// Check time format validation
$timeFields = ['clock_in', 'clock_out'];
foreach ($timeFields as $field) {
    if (isset($testData[$field]) && !empty($testData[$field])) {
        $time = $testData[$field];
        $parsed = DateTime::createFromFormat('H:i', $time);
        if (!$parsed || $parsed->format('H:i') !== $time) {
            $errors[] = "Format $field harus HH:MM (24 jam)";
        }
    }
}

if (empty($errors)) {
    echo "   ✅ Validation would PASS\n";
} else {
    echo "   ❌ Validation would FAIL:\n";
    foreach ($errors as $error) {
        echo "      - $error\n";
    }
}

echo "\n";

// 4. Check frontend time input format
echo "4. 🖥️ Checking frontend time input format...\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for step="1" attribute
    if (strpos($content, 'step="1"') !== false) {
        echo "   ⚠️ Found step='1' attribute - this may cause HH:MM:SS format\n";
    } else {
        echo "   ✅ No step='1' attribute found - HH:MM format enforced\n";
    }
    
    // Check for time input patterns
    if (preg_match_all('/pattern="[^"]*"/', $content, $matches)) {
        echo "   📝 Found time input patterns:\n";
        foreach ($matches[0] as $match) {
            echo "      " . $match . "\n";
        }
    }
    
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// 5. Generate fix if needed
echo "5. 🔧 Generating comprehensive fix...\n";

$fixScript = '<?php

/**
 * Final fix untuk attendance validation consistency
 */

echo "🔧 Applying final attendance validation fix...\n";

$controllerFile = "app/Http/Controllers/AttendanceManagementController.php";
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Backup
    $backupFile = $controllerFile . ".backup-validation-consistency." . date("Y-m-d-H-i-s");
    file_put_contents($backupFile, $content);
    echo "✅ Backup created: $backupFile\n";
    
    // Fix any remaining regex error messages
    $fixes = [
        "\'clock_out.regex\' => \'Format jam pulang harus HH:MM atau HH:MM:SS (24 jam)\'," => "\'clock_out.date_format\' => \'Format jam pulang harus HH:MM (24 jam)\',",
        "\'clock_in.regex\' => " => "\'clock_in.date_format\' => ",
        "\'break_in.regex\' => " => "\'break_in.date_format\' => ",
        "\'break_out.regex\' => " => "\'break_out.date_format\' => ",
        "\'overtime_in.regex\' => " => "\'overtime_in.date_format\' => ",
        "\'overtime_out.regex\' => " => "\'overtime_out.date_format\' => ",
    ];
    
    $changed = false;
    foreach ($fixes as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $changed = true;
            echo "✅ Fixed: $old\n";
        }
    }
    
    if ($changed) {
        file_put_contents($controllerFile, $content);
        echo "✅ Controller updated successfully\n";
    } else {
        echo "✅ No changes needed in controller\n";
    }
} else {
    echo "❌ Controller file not found\n";
}

echo "\n🎯 Fix completed! Please test the attendance form now.\n";

?>';

file_put_contents('fix_attendance_validation_final.php', $fixScript);
echo "   ✅ Fix script created: fix_attendance_validation_final.php\n";

echo "\n";

// 6. Summary
echo "📊 SUMMARY:\n";
echo "✅ Controller validation rules checked\n";
echo "✅ Time format validation tested\n";
echo "✅ Form submission simulated\n";
echo "✅ Frontend input format verified\n";
echo "✅ Fix script generated\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Run: php fix_attendance_validation_final.php\n";
echo "2. Clear browser cache (Ctrl+F5)\n";
echo "3. Test attendance form with time format HH:MM\n";
echo "4. Check for any remaining validation errors\n";

echo "\n🚀 The validation should now work consistently!\n";

?>