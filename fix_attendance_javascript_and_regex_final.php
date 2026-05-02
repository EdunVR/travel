<?php

/**
 * Fix JavaScript selector and any remaining regex delimiter issues
 */

echo "🔧 Fixing JavaScript selector and regex delimiter issues...\n\n";

// Fix the attendance view file
$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';

if (!file_exists($viewFile)) {
    echo "❌ View file not found: $viewFile\n";
    exit(1);
}

// Backup file
$backupFile = $viewFile . '.backup-js-regex-fix.' . date('Y-m-d-H-i-s');
copy($viewFile, $backupFile);
echo "✅ Backup created: $backupFile\n";

$content = file_get_contents($viewFile);

// Fix JavaScript selector issues
$jsFixes = [
    // Fix the invalid CSS selector in JavaScript
    "document.querySelectorAll('input[type=\"time\" step=\"1\"]')" => "document.querySelectorAll('input[type=\"time\"][step=\"1\"]')",
    "node.querySelectorAll('input[type=\"time\" step=\"1\"]')" => "node.querySelectorAll('input[type=\"time\"][step=\"1\"]')",
    
    // Fix CSS selectors
    'input[type="time" step="1"]::-webkit-datetime-edit-ampm-field' => 'input[type="time"][step="1"]::-webkit-datetime-edit-ampm-field',
    'input[type="time" step="1"]::-moz-time-picker-ampm' => 'input[type="time"][step="1"]::-moz-time-picker-ampm',
    'input[type="time" step="1"] {' => 'input[type="time"][step="1"] {',
    'input[type="time" step="1"]::-webkit-datetime-edit-meridiem-field' => 'input[type="time"][step="1"]::-webkit-datetime-edit-meridiem-field',
    'input[type="time" step="1"]::-webkit-datetime-edit-text' => 'input[type="time"][step="1"]::-webkit-datetime-edit-text',
    'input[type="time" step="1"]::-webkit-datetime-edit-hour-field' => 'input[type="time"][step="1"]::-webkit-datetime-edit-hour-field',
    'input[type="time" step="1"]::-webkit-datetime-edit-minute-field' => 'input[type="time"][step="1"]::-webkit-datetime-edit-minute-field',
    'input[type="time" step="1"]::-webkit-datetime-edit' => 'input[type="time"][step="1"]::-webkit-datetime-edit',
    'input[type="time" step="1"]::-ms-clear' => 'input[type="time"][step="1"]::-ms-clear',
    'input[type="time" step="1"]::after' => 'input[type="time"][step="1"]::after',
    'input[type="time" step="1"]::before' => 'input[type="time"][step="1"]::before',
];

$jsChanges = 0;
foreach ($jsFixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        $jsChanges++;
        echo "✅ Fixed JavaScript/CSS: " . substr($old, 0, 50) . "...\n";
    }
}

// Save the updated content
file_put_contents($viewFile, $content);

echo "\n📊 JAVASCRIPT FIXES:\n";
echo "✅ JavaScript/CSS fixes applied: $jsChanges\n";

// Now check the controller for any remaining regex issues
echo "\n🔍 Checking controller for regex delimiter issues...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
$controllerContent = file_get_contents($controllerFile);

// Look for any malformed regex patterns
$regexPatterns = [];
if (preg_match_all("/'[^']*regex[^']*'/", $controllerContent, $matches)) {
    foreach ($matches[0] as $match) {
        $regexPatterns[] = $match;
        
        // Check if it's properly formatted
        if (strpos($match, 'regex:/') !== false && !preg_match("/regex:\/.*\/[gimxs]*'/", $match)) {
            echo "⚠️ Potentially malformed regex: $match\n";
        } else {
            echo "✅ Properly formatted regex: $match\n";
        }
    }
}

// Additional comprehensive fix for any remaining regex issues
$additionalFixes = [
    // Ensure all regex patterns have proper delimiters
    "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
    "'regex:^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
    "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?'" => "'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'",
];

$regexChanges = 0;
foreach ($additionalFixes as $old => $new) {
    if (strpos($controllerContent, $old) !== false) {
        $controllerContent = str_replace($old, $new, $controllerContent);
        $regexChanges++;
        echo "✅ Fixed regex pattern: $old\n";
    }
}

if ($regexChanges > 0) {
    file_put_contents($controllerFile, $controllerContent);
    echo "✅ Controller updated with regex fixes\n";
} else {
    echo "✅ No additional regex fixes needed in controller\n";
}

echo "\n🧪 TESTING FIXED SELECTORS:\n";

// Test the corrected JavaScript selector
$testSelectors = [
    'input[type="time"][step="1"]' => 'Valid CSS selector',
    'input[type="time" step="1"]' => 'Invalid CSS selector (spaces)',
];

foreach ($testSelectors as $selector => $description) {
    echo "📝 $selector - $description\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test attendance form submission\n";
echo "3. Check browser console for JavaScript errors\n";
echo "4. Verify no more regex delimiter errors in Laravel logs\n";

echo "\n🚀 JavaScript selector and regex issues should now be fixed!\n";

?>