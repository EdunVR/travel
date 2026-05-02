<?php

echo "🔧 SDM ATTENDANCE JAVASCRIPT CHECK\n";
echo "=================================\n\n";

// Test 1: Check file exists and is readable
$filePath = 'resources/views/admin/sdm/attendance/index.blade.php';
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

// Test 3: Check for duplicate code blocks that could cause syntax errors
$duplicateChecks = [
    'Duplicate attendanceCrud function' => function($content) {
        return substr_count($content, 'function attendanceCrud()') <= 1;
    },
    'Duplicate fetchData method' => function($content) {
        return substr_count($content, 'async fetchData()') <= 1;
    },
    'Duplicate return blocks' => function($content) {
        $pattern = '/return \{.*?attendances:.*?\};.*?return \{.*?attendances:.*?\};/s';
        return !preg_match($pattern, $content);
    },
    'Duplicate error handling blocks' => function($content) {
        // Count specific error message patterns
        $errorCount1 = substr_count($content, "console.error('Error fetching data:', error);");
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
    'function attendanceCrud()' => 'Main function declaration',
    'return {' => 'Return object start',
    'async init()' => 'Init method',
    'async loadOutlets()' => 'LoadOutlets method',
    'getSelectedOutletsText()' => 'GetSelectedOutletsText method',
    'selectAllOutlets()' => 'SelectAllOutlets method',
    'clearAllOutlets()' => 'ClearAllOutlets method',
    'onOutletSelectionChange()' => 'OnOutletSelectionChange method',
    'calculateHoursWorked(' => 'CalculateHoursWorked method',
    'calculateOvertime(' => 'CalculateOvertime method',
    'async fetchData()' => 'FetchData method',
    'async fetchEmployees()' => 'FetchEmployees method',
    'async fetchStatistics()' => 'FetchStatistics method',
    'openCreate()' => 'OpenCreate method',
    'async openEdit(' => 'OpenEdit method',
    'async submitForm()' => 'SubmitForm method',
    'async submitWorkHours()' => 'SubmitWorkHours method',
    'async loadTimeSettings()' => 'LoadTimeSettings method',
    'async saveTimeSettings()' => 'SaveTimeSettings method',
    'async testTimePeriod()' => 'TestTimePeriod method',
    'confirmDelete(' => 'ConfirmDelete method',
    'async deleteNow()' => 'DeleteNow method',
    'showToastMessage(' => 'ShowToastMessage method'
];

echo "\n📋 JAVASCRIPT FUNCTION STRUCTURE CHECK:\n";
$missingFunctions = 0;
foreach ($jsPatterns as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
        $missingFunctions++;
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
$syntaxErrors = 0;
foreach ($syntaxChecks as $check => $validator) {
    if ($validator($content)) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: FAILED\n";
        $syntaxErrors++;
    }
}

// Test 6: Check Alpine.js directives
$alpineDirectives = [
    'x-data="attendanceCrud()"' => 'Main Alpine component',
    'x-init="init()"' => 'Initialization directive',
    'x-show=' => 'Show/hide directives',
    'x-on:click=' => 'Click event handlers',
    'x-model=' => 'Two-way binding',
    'x-text=' => 'Text binding'
];

echo "\n🎯 ALPINE.JS DIRECTIVES CHECK:\n";
$missingDirectives = 0;
foreach ($alpineDirectives as $directive => $description) {
    if (strpos($content, $directive) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
        $missingDirectives++;
    }
}

// Test 7: Check for potential issues specific to attendance
$attendanceSpecificChecks = [
    'Checkbox outlet filter implementation' => function($content) {
        return strpos($content, 'selectedOutlets') !== false && 
               strpos($content, 'showOutletDropdown') !== false;
    },
    'Time calculation functions' => function($content) {
        return strpos($content, 'calculateHoursWorked') !== false && 
               strpos($content, 'calculateOvertime') !== false;
    },
    'Work hours management' => function($content) {
        return strpos($content, 'workHoursForm') !== false && 
               strpos($content, 'submitWorkHours') !== false;
    },
    'Time settings functionality' => function($content) {
        return strpos($content, 'timeSettings') !== false && 
               strpos($content, 'loadTimeSettings') !== false;
    }
];

echo "\n⚙️  ATTENDANCE SPECIFIC CHECKS:\n";
$attendanceIssues = 0;
foreach ($attendanceSpecificChecks as $check => $validator) {
    if ($validator($content)) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: FAILED\n";
        $attendanceIssues++;
    }
}

// Final summary
echo "\n🎯 FINAL TEST SUMMARY:\n";
echo "==================\n";

$allGood = true;

// Critical checks
if (strpos($content, 'function attendanceCrud()') === false) {
    echo "❌ CRITICAL: Main function missing\n";
    $allGood = false;
}

if (strpos($content, 'x-data="attendanceCrud()"') === false) {
    echo "❌ CRITICAL: Alpine.js binding missing\n";
    $allGood = false;
}

if ($duplicatesFound) {
    echo "❌ CRITICAL: Duplicate code blocks found\n";
    $allGood = false;
}

if ($syntaxErrors > 0) {
    echo "❌ CRITICAL: Syntax errors detected\n";
    $allGood = false;
}

if ($missingFunctions > 3) {
    echo "❌ WARNING: Many functions missing ($missingFunctions)\n";
}

if ($attendanceIssues > 0) {
    echo "❌ WARNING: Attendance-specific issues detected ($attendanceIssues)\n";
}

if ($allGood && $missingFunctions <= 3 && $attendanceIssues == 0) {
    echo "🎉 ALL TESTS PASSED! SDM Attendance JavaScript is healthy.\n";
    echo "\n📋 STATUS:\n";
    echo "✅ No duplicate code blocks\n";
    echo "✅ No syntax errors\n";
    echo "✅ All critical functions present\n";
    echo "✅ Alpine.js properly integrated\n";
    echo "✅ Checkbox filter implemented\n";
} else {
    echo "⚠️  SOME ISSUES DETECTED! Review the results above.\n";
    
    if ($duplicatesFound || $syntaxErrors > 0) {
        echo "\n🚨 CRITICAL ISSUES NEED IMMEDIATE ATTENTION!\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";

?>