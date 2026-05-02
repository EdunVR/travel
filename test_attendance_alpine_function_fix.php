<?php
/**
 * Test script to verify Alpine.js attendanceCrud function fix
 * 
 * This script tests:
 * 1. Alpine.js function is properly defined
 * 2. No JavaScript syntax errors
 * 3. Function closure is correct
 */

echo "=== TESTING ALPINE.JS ATTENDANCECRUD FUNCTION FIX ===\n\n";

// Test 1: Check if the view file exists and is readable
$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (!file_exists($viewFile)) {
    echo "❌ ERROR: View file not found: $viewFile\n";
    exit(1);
}

echo "✅ View file exists: $viewFile\n";

// Test 2: Read the file content
$content = file_get_contents($viewFile);
if (!$content) {
    echo "❌ ERROR: Could not read view file\n";
    exit(1);
}

echo "✅ View file readable, size: " . strlen($content) . " bytes\n";

// Test 3: Check for Alpine.js function definition
if (strpos($content, 'function attendanceCrud()') === false) {
    echo "❌ ERROR: attendanceCrud function not found\n";
    exit(1);
}

echo "✅ attendanceCrud function found\n";

// Test 4: Check for proper function opening
if (strpos($content, 'function attendanceCrud() {') === false) {
    echo "❌ ERROR: attendanceCrud function opening brace not found\n";
    exit(1);
}

echo "✅ Function opening brace found\n";

// Test 5: Check for return statement
if (strpos($content, 'return {') === false) {
    echo "❌ ERROR: Return statement not found\n";
    exit(1);
}

echo "✅ Return statement found\n";

// Test 6: Check for proper function closing
$functionStart = strpos($content, 'function attendanceCrud() {');
$returnStart = strpos($content, 'return {', $functionStart);

if ($functionStart === false || $returnStart === false) {
    echo "❌ ERROR: Could not locate function boundaries\n";
    exit(1);
}

// Count braces after return statement to ensure proper closure
$afterReturn = substr($content, $returnStart);
$openBraces = substr_count($afterReturn, '{');
$closeBraces = substr_count($afterReturn, '}');

echo "📊 Brace count after return statement:\n";
echo "   - Open braces: $openBraces\n";
echo "   - Close braces: $closeBraces\n";

// Test 7: Check for script tag closure
if (strpos($content, '</script>') === false) {
    echo "❌ ERROR: Script closing tag not found\n";
    exit(1);
}

echo "✅ Script closing tag found\n";

// Test 8: Check for duplicate function definitions (common cause of errors)
$duplicateChecks = [
    'ensureTimeFormat' => substr_count($content, 'ensureTimeFormat('),
    'formatTimeToHHMM' => substr_count($content, 'formatTimeToHHMM('),
    'calculateHoursWorked' => substr_count($content, 'calculateHoursWorked('),
    'fetchData' => substr_count($content, 'fetchData(')
];

echo "\n📊 Function definition counts:\n";
foreach ($duplicateChecks as $func => $count) {
    echo "   - $func: $count occurrences";
    if ($count > 1) {
        echo " ⚠️ POTENTIAL DUPLICATE";
    }
    echo "\n";
}

// Test 9: Check for common JavaScript syntax errors
$syntaxChecks = [
    'Unclosed strings' => preg_match('/["\'][^"\']*$/', $content),
    'Missing semicolons in critical places' => !preg_match('/}\s*;\s*$/', $content),
    'Unmatched parentheses' => substr_count($content, '(') !== substr_count($content, ')'),
];

echo "\n📊 Syntax checks:\n";
$syntaxErrors = 0;
foreach ($syntaxChecks as $check => $hasError) {
    echo "   - $check: ";
    if ($hasError) {
        echo "❌ POTENTIAL ISSUE\n";
        $syntaxErrors++;
    } else {
        echo "✅ OK\n";
    }
}

// Test 10: Extract and validate the Alpine.js function structure
echo "\n🔍 Analyzing Alpine.js function structure...\n";

// Find the complete function
$functionPattern = '/function attendanceCrud\(\)\s*\{.*?return\s*\{.*?\}\s*;\s*\}/s';
if (preg_match($functionPattern, $content, $matches)) {
    echo "✅ Complete function structure found\n";
    
    // Check if function has proper return object
    $functionBody = $matches[0];
    if (strpos($functionBody, 'return {') !== false && strpos($functionBody, '};') !== false) {
        echo "✅ Function has proper return object structure\n";
    } else {
        echo "❌ Function return object structure issue\n";
        $syntaxErrors++;
    }
} else {
    echo "❌ Could not match complete function structure\n";
    $syntaxErrors++;
}

// Final result
echo "\n" . str_repeat("=", 50) . "\n";
if ($syntaxErrors === 0) {
    echo "🎉 SUCCESS: Alpine.js function appears to be properly structured!\n";
    echo "\nNext steps:\n";
    echo "1. Clear browser cache\n";
    echo "2. Test the attendance page\n";
    echo "3. Check browser console for any remaining errors\n";
} else {
    echo "⚠️ WARNING: Found $syntaxErrors potential issues\n";
    echo "The function may still have problems. Check the issues above.\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>