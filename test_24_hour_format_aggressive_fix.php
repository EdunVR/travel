<?php

/**
 * Test 24-Hour Format Aggressive Fix
 * 
 * This script tests if the aggressive 24-hour format fix is working
 */

echo "=== TESTING AGGRESSIVE 24-HOUR FORMAT FIX ===\n\n";

// Check if files exist
$files = [
    'public/css/force-24hour-format.css' => 'Global CSS file',
    'public/js/force-24hour-format.js' => 'Global JavaScript file',
    'resources/views/components/layouts/admin.blade.php' => 'Admin layout file',
    'resources/views/admin/sdm/attendance/index.blade.php' => 'Attendance view file'
];

echo "1. CHECKING FILE EXISTENCE\n";
echo "===========================\n";

foreach ($files as $file => $description) {
    $exists = file_exists($file);
    echo ($exists ? "✅" : "❌") . " {$description}: " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
    echo "   Path: {$file}\n\n";
}

echo "2. CHECKING ADMIN LAYOUT MODIFICATIONS\n";
echo "======================================\n";

$adminLayoutFile = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutFile)) {
    $content = file_get_contents($adminLayoutFile);
    
    $checks = [
        'Meta tag for time format' => 'meta name="time-format"',
        'Global CSS inclusion' => 'force-24hour-format.css',
        'Global JS inclusion' => 'force-24hour-format.js',
        'Global CSS rules' => 'webkit-datetime-edit-ampm-field',
        'Global JavaScript' => 'enforce24HourFormatGlobal',
        'MutationObserver' => 'MutationObserver',
        'setAttribute override' => 'originalSetAttribute'
    ];
    
    foreach ($checks as $check => $searchString) {
        $found = strpos($content, $searchString) !== false;
        echo ($found ? "✅" : "❌") . " {$check}: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
    }
} else {
    echo "❌ Admin layout file not found\n";
}

echo "\n3. CHECKING ATTENDANCE VIEW MODIFICATIONS\n";
echo "=========================================\n";

$attendanceViewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($attendanceViewFile)) {
    $content = file_get_contents($attendanceViewFile);
    
    $checks = [
        'Aggressive CSS rules' => 'FORCE 24-HOUR FORMAT - AGGRESSIVE APPROACH',
        'Multiple CSS selectors' => 'webkit-datetime-edit-meridiem-field',
        'JavaScript enforcement' => 'AGGRESSIVE 24-HOUR FORMAT ENFORCEMENT',
        'Console logging' => 'console.log',
        'MutationObserver' => 'MutationObserver',
        'Event listeners' => 'addEventListener'
    ];
    
    foreach ($checks as $check => $searchString) {
        $found = strpos($content, $searchString) !== false;
        echo ($found ? "✅" : "❌") . " {$check}: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
    }
} else {
    echo "❌ Attendance view file not found\n";
}

echo "\n4. CHECKING GLOBAL CSS FILE\n";
echo "============================\n";

$cssFile = 'public/css/force-24hour-format.css';
if (file_exists($cssFile)) {
    $content = file_get_contents($cssFile);
    $size = filesize($cssFile);
    
    echo "✅ CSS file exists\n";
    echo "   Size: {$size} bytes\n";
    
    $cssChecks = [
        'WebKit AM/PM hiding' => 'webkit-datetime-edit-ampm-field',
        'Firefox AM/PM hiding' => 'moz-time-picker-ampm',
        'Meridiem field hiding' => 'webkit-datetime-edit-meridiem-field',
        'Important declarations' => '!important',
        'Multiple selectors' => 'input[type="time"]'
    ];
    
    foreach ($cssChecks as $check => $searchString) {
        $found = strpos($content, $searchString) !== false;
        echo ($found ? "✅" : "❌") . " {$check}: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
    }
} else {
    echo "❌ Global CSS file not found\n";
}

echo "\n5. CHECKING GLOBAL JAVASCRIPT FILE\n";
echo "===================================\n";

$jsFile = 'public/js/force-24hour-format.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    $size = filesize($jsFile);
    
    echo "✅ JavaScript file exists\n";
    echo "   Size: {$size} bytes\n";
    
    $jsChecks = [
        'Enforcement function' => 'enforce24HourFormat',
        'MutationObserver' => 'MutationObserver',
        'Event listeners' => 'addEventListener',
        'Attribute enforcement' => 'setAttribute',
        'CSS property setting' => 'setProperty',
        'Validation logic' => 'setCustomValidity',
        'Debug logging' => 'console.log',
        'Retry mechanism' => 'retryAttempts'
    ];
    
    foreach ($jsChecks as $check => $searchString) {
        $found = strpos($content, $searchString) !== false;
        echo ($found ? "✅" : "❌") . " {$check}: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
    }
} else {
    echo "❌ Global JavaScript file not found\n";
}

echo "\n6. IMPLEMENTATION SUMMARY\n";
echo "=========================\n";

$implementations = [
    'Global CSS file created' => file_exists('public/css/force-24hour-format.css'),
    'Global JavaScript file created' => file_exists('public/js/force-24hour-format.js'),
    'Admin layout modified' => file_exists($adminLayoutFile) && strpos(file_get_contents($adminLayoutFile), 'force-24hour-format') !== false,
    'Attendance view enhanced' => file_exists($attendanceViewFile) && strpos(file_get_contents($attendanceViewFile), 'AGGRESSIVE') !== false
];

$passedCount = 0;
$totalCount = count($implementations);

foreach ($implementations as $implementation => $passed) {
    echo ($passed ? "✅" : "❌") . " {$implementation}\n";
    if ($passed) $passedCount++;
}

echo "\n";
echo "OVERALL STATUS: {$passedCount}/{$totalCount} implementations completed\n";

if ($passedCount === $totalCount) {
    echo "🎉 ALL IMPLEMENTATIONS COMPLETE - AGGRESSIVE 24-HOUR FORMAT FIX READY!\n";
} else {
    echo "⚠️  SOME IMPLEMENTATIONS MISSING - PLEASE CHECK ABOVE\n";
}

echo "\n7. TESTING INSTRUCTIONS\n";
echo "========================\n";
echo "1. Clear browser cache completely (Ctrl+Shift+Delete)\n";
echo "2. Open browser in incognito/private mode\n";
echo "3. Navigate to: /admin/sdm/attendance\n";
echo "4. Check console (F12) for enforcement messages:\n";
echo "   - Look for: '🕐 Loading AGGRESSIVE 24-hour format enforcement...'\n";
echo "   - Look for: '✅ AGGRESSIVE 24-hour format enforcement initialized'\n";
echo "5. Click purple 'Pengaturan Waktu' button\n";
echo "6. Verify NO AM/PM selectors in time pickers\n";
echo "7. Click blue 'Set Jam Kerja' button\n";
echo "8. Verify NO AM/PM selectors in time pickers\n";
echo "9. Try entering times and verify validation works\n";

echo "\n8. TROUBLESHOOTING\n";
echo "==================\n";
echo "If AM/PM still appears:\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Verify CSS and JS files are loading (Network tab in DevTools)\n";
echo "3. Try different browser (Chrome, Firefox, Safari, Edge)\n";
echo "4. Check if browser has cached old files\n";
echo "5. Manually call: window.enforce24HourFormat() in console\n";

echo "\n=== TEST COMPLETE ===\n";

?>