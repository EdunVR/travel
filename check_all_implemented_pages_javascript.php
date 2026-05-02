<?php

echo "🔧 CHECK ALL IMPLEMENTED PAGES JAVASCRIPT\n";
echo "========================================\n\n";

// List of implemented pages to check
$pages = [
    'Finance Journal' => 'resources/views/admin/finance/jurnal/index.blade.php',
    'Sales Invoice' => 'resources/views/admin/penjualan/invoice/index.blade.php',
    'Inventaris Produk' => 'resources/views/admin/inventaris/produk/index.blade.php',
    'Service History' => 'resources/views/admin/service/history/index.blade.php',
    'SDM Attendance' => 'resources/views/admin/sdm/attendance/index.blade.php'
];

$results = [];

foreach ($pages as $pageName => $filePath) {
    echo "🔍 Checking: $pageName\n";
    echo str_repeat("-", 40) . "\n";
    
    $result = [
        'name' => $pageName,
        'file' => $filePath,
        'exists' => false,
        'size' => 0,
        'has_function' => false,
        'has_alpine' => false,
        'has_checkbox' => false,
        'syntax_ok' => false,
        'duplicate_check' => false,
        'status' => 'unknown'
    ];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        $result['status'] = 'missing';
        $results[] = $result;
        continue;
    }
    
    $result['exists'] = true;
    
    // Read file content
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Cannot read file content\n";
        $result['status'] = 'unreadable';
        $results[] = $result;
        continue;
    }
    
    $result['size'] = strlen($content);
    echo "✅ File loaded (" . number_format($result['size']) . " bytes)\n";
    
    // Check for main JavaScript function
    $functionPatterns = [
        'Finance Journal' => 'function journalsManagement()',
        'Sales Invoice' => 'function invoicePenjualan()',
        'Inventaris Produk' => 'function produkCrud()',
        'Service History' => 'function historyData()',
        'SDM Attendance' => 'function attendanceCrud()'
    ];
    
    $functionPattern = $functionPatterns[$pageName] ?? 'function ';
    if (strpos($content, $functionPattern) !== false) {
        $result['has_function'] = true;
        echo "✅ Main JavaScript function found\n";
    } else {
        echo "❌ Main JavaScript function missing\n";
    }
    
    // Check Alpine.js integration
    $alpinePatterns = [
        'Finance Journal' => 'x-data="journalsManagement()"',
        'Sales Invoice' => 'x-data="invoicePenjualan()"',
        'Inventaris Produk' => 'x-data="produkCrud()"',
        'Service History' => 'x-data="historyData()"',
        'SDM Attendance' => 'x-data="attendanceCrud()"'
    ];
    
    $alpinePattern = $alpinePatterns[$pageName] ?? 'x-data=';
    if (strpos($content, $alpinePattern) !== false) {
        $result['has_alpine'] = true;
        echo "✅ Alpine.js integration found\n";
    } else {
        echo "❌ Alpine.js integration missing\n";
    }
    
    // Check checkbox filter implementation
    if (strpos($content, 'selectedOutlets') !== false && 
        strpos($content, 'showOutletDropdown') !== false &&
        strpos($content, 'getSelectedOutletsText') !== false) {
        $result['has_checkbox'] = true;
        echo "✅ Checkbox filter implemented\n";
    } else {
        echo "❌ Checkbox filter missing\n";
    }
    
    // Check syntax (basic checks)
    $openBraces = substr_count($content, '{');
    $closeBraces = substr_count($content, '}');
    $openParens = substr_count($content, '(');
    $closeParens = substr_count($content, ')');
    
    if ($openBraces === $closeBraces && $openParens === $closeParens) {
        $result['syntax_ok'] = true;
        echo "✅ Basic syntax checks passed\n";
    } else {
        echo "❌ Syntax errors detected (braces: $openBraces/$closeBraces, parens: $openParens/$closeParens)\n";
    }
    
    // Check for duplicate code patterns (common issue)
    $duplicatePatterns = [
        '/} catch \(error\) {.*?console\.error.*?} catch \(error\) {.*?console\.error/s',
        '/async fetchData\(\).*?async fetchData\(\)/s',
        '/return \{.*?return \{/s'
    ];
    
    $hasDuplicates = false;
    foreach ($duplicatePatterns as $pattern) {
        if (preg_match($pattern, $content)) {
            $hasDuplicates = true;
            break;
        }
    }
    
    if (!$hasDuplicates) {
        $result['duplicate_check'] = true;
        echo "✅ No duplicate code blocks detected\n";
    } else {
        echo "❌ Duplicate code blocks detected\n";
    }
    
    // Determine overall status
    if ($result['has_function'] && $result['has_alpine'] && $result['has_checkbox'] && 
        $result['syntax_ok'] && $result['duplicate_check']) {
        $result['status'] = 'healthy';
        echo "🎉 Status: HEALTHY\n";
    } else if ($result['has_function'] && $result['has_alpine'] && $result['syntax_ok']) {
        $result['status'] = 'good';
        echo "✅ Status: GOOD (minor issues)\n";
    } else if (!$result['syntax_ok'] || !$result['duplicate_check']) {
        $result['status'] = 'critical';
        echo "🚨 Status: CRITICAL (needs immediate attention)\n";
    } else {
        $result['status'] = 'needs_work';
        echo "⚠️  Status: NEEDS WORK\n";
    }
    
    $results[] = $result;
    echo "\n";
}

// Summary report
echo "📊 SUMMARY REPORT\n";
echo "================\n\n";

$statusCounts = [
    'healthy' => 0,
    'good' => 0,
    'needs_work' => 0,
    'critical' => 0,
    'missing' => 0,
    'unreadable' => 0
];

foreach ($results as $result) {
    $statusCounts[$result['status']]++;
    
    $statusIcon = [
        'healthy' => '🎉',
        'good' => '✅',
        'needs_work' => '⚠️ ',
        'critical' => '🚨',
        'missing' => '❌',
        'unreadable' => '💥'
    ];
    
    echo $statusIcon[$result['status']] . " " . $result['name'] . " - " . strtoupper($result['status']) . "\n";
    
    if ($result['status'] === 'critical') {
        echo "   🔧 Issues: ";
        $issues = [];
        if (!$result['syntax_ok']) $issues[] = "Syntax errors";
        if (!$result['duplicate_check']) $issues[] = "Duplicate code";
        if (!$result['has_function']) $issues[] = "Missing function";
        if (!$result['has_alpine']) $issues[] = "Missing Alpine.js";
        echo implode(", ", $issues) . "\n";
    }
}

echo "\n📈 STATISTICS:\n";
echo "Healthy: " . $statusCounts['healthy'] . "\n";
echo "Good: " . $statusCounts['good'] . "\n";
echo "Needs Work: " . $statusCounts['needs_work'] . "\n";
echo "Critical: " . $statusCounts['critical'] . "\n";
echo "Missing: " . $statusCounts['missing'] . "\n";
echo "Unreadable: " . $statusCounts['unreadable'] . "\n";

echo "\n🎯 RECOMMENDATIONS:\n";
if ($statusCounts['critical'] > 0) {
    echo "🚨 URGENT: Fix critical issues immediately!\n";
    echo "   - Check syntax errors and duplicate code\n";
    echo "   - Test pages in browser console\n";
}

if ($statusCounts['needs_work'] > 0) {
    echo "⚠️  TODO: Complete missing implementations\n";
    echo "   - Add missing checkbox filters\n";
    echo "   - Verify Alpine.js integration\n";
}

if ($statusCounts['healthy'] === count($results)) {
    echo "🎉 EXCELLENT: All pages are healthy!\n";
    echo "   - Ready to continue with next implementations\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Check completed at: " . date('Y-m-d H:i:s') . "\n";

?>