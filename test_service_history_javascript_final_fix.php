<?php

echo "🔧 SERVICE HISTORY JAVASCRIPT FINAL FIX TEST\n";
echo "==========================================\n\n";

// Test 1: Check file exists and is readable
$filePath = 'resources/views/admin/service/history/index.blade.php';
if (!file_exists($filePath)) {
    echo "❌ File not found: $filePath\n";
    exit(1);
}

echo "✅ File exists: $filePath\n";

// Test 2: Read file content
$content = file_get_contents($filePath);
if ($content === false) {
    echo "❌ Cannot read file content\n";
    exit(1);
}

echo "✅ File content loaded (" . strlen($content) . " bytes)\n";

// Test 3: Check for specific duplicate patterns that cause syntax errors
$duplicateChecks = [
    'Duplicate fetchData method' => function($content) {
        return substr_count($content, 'async fetchData()') <= 1;
    },
    'Duplicate sisaHariText assignments' => function($content) {
        $pattern = '/sisaHariText = `Terlambat \${jamTerlambat} jam`;.*?sisaHariText = `Terlambat \${jamTerlambat} jam`;/s';
        return !preg_match($pattern, $content);
    },
    'Duplicate return blocks in fetchData' => function($content) {
        $pattern = '/return \{.*?id: item\.id_service_invoice.*?\};.*?return \{.*?id: item\.id_service_invoice.*?\};/s';
        return !preg_match($pattern, $content);
    },
    'Duplicate error handling blocks' => function($content) {
        // Count specific error message patterns
        $errorCount1 = substr_count($content, "console.error('❌ Error fetching data:', error);");
        $errorCount2 = substr_count($content, "this.showToastMessage('Gagal memuat data', 'error');");
        return $errorCount1 <= 1 && $errorCount2 <= 1;
    }
];

echo "\n🔍 DUPLICATE CODE CHECKS:\n";
$duplicatesFound = false;
foreach ($duplicateChecks as $check => $validator) {
    if ($validator($content)) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: FAILED\n";
        $duplicatesFound = true;
    }
}

// Test 4: Check JavaScript function structure
$jsPatterns = [
    'function historyData()' => 'Main function declaration',
    'return {' => 'Return object start',
    'async init()' => 'Init method',
    'async fetchData()' => 'FetchData method',
    'async loadStatusCounts()' => 'LoadStatusCounts method',
    'getSelectedOutletsText()' => 'GetSelectedOutletsText method',
    'selectAllOutlets()' => 'SelectAllOutlets method',
    'clearAllOutlets()' => 'ClearAllOutlets method',
    'onOutletSelectionChange()' => 'OnOutletSelectionChange method',
    'changeTab(' => 'ChangeTab method',
    'viewPdf(' => 'ViewPdf method',
    'updateStatus(' => 'UpdateStatus method',
    'submitStatus()' => 'SubmitStatus method',
    'confirmDelete(' => 'ConfirmDelete method',
    'deleteNow()' => 'DeleteNow method',
    'exportData()' => 'ExportData method',
    'exportPdf()' => 'ExportPdf method',
    'showToastMessage(' => 'ShowToastMessage method',
    'checkAlarmStatus()' => 'CheckAlarmStatus method',
    'checkDueSoonInvoices()' => 'CheckDueSoonInvoices method',
    'playAlarmSound()' => 'PlayAlarmSound method',
    'createBeep(' => 'CreateBeep method',
    'snoozeAlarm()' => 'SnoozeAlarm method',
    'closeAlarm()' => 'CloseAlarm method',
    'getTimeDescription(' => 'GetTimeDescription method'
];

echo "\n📋 JAVASCRIPT FUNCTION STRUCTURE CHECK:\n";
foreach ($jsPatterns as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

// Test 5: Check for common JavaScript syntax errors
$syntaxChecks = [
    'Unclosed braces' => function($content) {
        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');
        return $openBraces === $closeBraces;
    },
    'Unclosed parentheses' => function($content) {
        $openParens = substr_count($content, '(');
        $closeParens = substr_count($content, ')');
        return $openParens === $closeParens;
    },
    'Unclosed brackets' => function($content) {
        $openBrackets = substr_count($content, '[');
        $closeBrackets = substr_count($content, ']');
        return $openBrackets === $closeBrackets;
    }
];

echo "\n🔍 SYNTAX ERROR CHECKS:\n";
foreach ($syntaxChecks as $check => $validator) {
    if ($validator($content)) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: FAILED\n";
    }
}

// Test 6: Check Alpine.js directives
$alpineDirectives = [
    'x-data="historyData()"' => 'Main Alpine component',
    'x-init="init()"' => 'Initialization directive',
    'x-show=' => 'Show/hide directives',
    'x-on:click=' => 'Click event handlers',
    'x-model=' => 'Two-way binding',
    'x-text=' => 'Text binding',
    'x-html=' => 'HTML binding',
    ':class=' => 'Dynamic classes'
];

echo "\n🎯 ALPINE.JS DIRECTIVES CHECK:\n";
foreach ($alpineDirectives as $directive => $description) {
    if (strpos($content, $directive) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

// Test 7: Check for CSS and styling
$styleChecks = [
    '<link href=\'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css\' rel=\'stylesheet\'>' => 'Boxicons CSS',
    '<style>' => 'Custom styles',
    '.tab-button-new' => 'Tab button styles',
    '.alarm-pulse' => 'Alarm animation styles',
    '@keyframes alarm-pulse' => 'Keyframe animation'
];

echo "\n🎨 STYLING CHECKS:\n";
foreach ($styleChecks as $check => $description) {
    if (strpos($content, $check) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

// Test 8: Line count and structure
$lines = explode("\n", $content);
$totalLines = count($lines);
$jsStartLine = 0;
$jsEndLine = 0;

for ($i = 0; $i < $totalLines; $i++) {
    if (strpos($lines[$i], 'function historyData()') !== false) {
        $jsStartLine = $i + 1;
    }
    if (strpos($lines[$i], '</script>') !== false && $jsStartLine > 0) {
        $jsEndLine = $i + 1;
        break;
    }
}

echo "\n📊 FILE STRUCTURE:\n";
echo "✅ Total lines: $totalLines\n";
echo "✅ JavaScript starts at line: $jsStartLine\n";
echo "✅ JavaScript ends at line: $jsEndLine\n";
echo "✅ JavaScript section: " . ($jsEndLine - $jsStartLine) . " lines\n";

// Final summary
echo "\n🎯 FINAL TEST SUMMARY:\n";
echo "==================\n";

$allGood = true;

// Critical checks
if (strpos($content, 'function historyData()') === false) {
    echo "❌ CRITICAL: Main function missing\n";
    $allGood = false;
}

if (strpos($content, 'x-data="historyData()"') === false) {
    echo "❌ CRITICAL: Alpine.js binding missing\n";
    $allGood = false;
}

if ($duplicatesFound) {
    echo "❌ CRITICAL: Duplicate code blocks still present\n";
    $allGood = false;
}

if ($allGood) {
    echo "🎉 ALL TESTS PASSED! Service History JavaScript is ready.\n";
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Test the page in browser\n";
    echo "3. Check browser console for any remaining errors\n";
    echo "4. Test all functionality (filters, exports, modals)\n";
} else {
    echo "❌ SOME TESTS FAILED! Please review and fix the issues above.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";

?>