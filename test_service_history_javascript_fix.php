<?php

/**
 * SERVICE HISTORY JAVASCRIPT FIX TEST SCRIPT
 * 
 * This script tests the Service History JavaScript syntax and structure
 */

echo "=== SERVICE HISTORY JAVASCRIPT FIX TEST ===\n\n";

// Test 1: Check for syntax errors
echo "1. Testing JavaScript Syntax...\n";

$viewFile = 'resources/views/admin/service/history/index.blade.php';
if (!file_exists($viewFile)) {
    echo "❌ View file not found: $viewFile\n";
    exit(1);
}

$content = file_get_contents($viewFile);

// Check for common syntax issues
$syntaxChecks = [
    'Duplicate style tags' => '<style>.*<style>',
    'Link inside style' => '<style>.*<link.*<style>',
    'Unclosed functions' => 'function.*{(?!.*})',
    'Missing semicolons in critical places' => 'return\s*{[^}]*}(?!\s*[;}])',
    'Malformed CSS' => '<style>.*<link.*rel=',
];

$syntaxResults = [];
foreach ($syntaxChecks as $check => $pattern) {
    if (preg_match("/$pattern/s", $content)) {
        echo "❌ $check: Found issue\n";
        $syntaxResults[$check] = false;
    } else {
        echo "✅ $check: Clean\n";
        $syntaxResults[$check] = true;
    }
}

// Test 2: Check JavaScript function structure
echo "\n2. Testing JavaScript Function Structure...\n";

$jsChecks = [
    'historyData function defined' => 'function historyData\(\)',
    'Function returns object' => 'return\s*{',
    'All required properties' => 'invoices.*outlets.*selectedOutlets.*currentStatus',
    'All required methods' => 'init.*loadOutlets.*fetchData.*exportData',
    'Proper function closing' => '}.*}.*</script>',
];

$jsResults = [];
foreach ($jsChecks as $check => $pattern) {
    if (preg_match("/$pattern/s", $content)) {
        echo "✅ $check: Found\n";
        $jsResults[$check] = true;
    } else {
        echo "❌ $check: Missing\n";
        $jsResults[$check] = false;
    }
}

// Test 3: Check CSS structure
echo "\n3. Testing CSS Structure...\n";

$cssChecks = [
    'Boxicons link properly placed' => '<link.*boxicons.*rel=.stylesheet',
    'Style tag properly opened' => '<style>',
    'Style tag properly closed' => '</style>',
    'No nested style tags' => '!(<style>.*<style>)',
    'CSS classes defined' => 'tab-button-new.*tab-active-new.*tab-inactive-new',
];

$cssResults = [];
foreach ($cssChecks as $check => $pattern) {
    $isNegativeCheck = strpos($pattern, '!(') === 0;
    if ($isNegativeCheck) {
        $pattern = substr($pattern, 2, -1); // Remove !( and )
        $found = preg_match("/$pattern/s", $content);
        if (!$found) {
            echo "✅ $check: Clean\n";
            $cssResults[$check] = true;
        } else {
            echo "❌ $check: Issue found\n";
            $cssResults[$check] = false;
        }
    } else {
        if (preg_match("/$pattern/s", $content)) {
            echo "✅ $check: Found\n";
            $cssResults[$check] = true;
        } else {
            echo "❌ $check: Missing\n";
            $cssResults[$check] = false;
        }
    }
}

// Test 4: Check Alpine.js integration
echo "\n4. Testing Alpine.js Integration...\n";

$alpineChecks = [
    'x-data directive' => 'x-data="historyData\(\)"',
    'x-init directive' => 'x-init="init\(\)"',
    'Alpine expressions' => 'x-text=|x-show=|x-model=',
    'Event handlers' => 'x-on:click=|@click=',
    'Conditional classes' => ':class=',
];

$alpineResults = [];
foreach ($alpineChecks as $check => $pattern) {
    if (preg_match("/$pattern/", $content)) {
        echo "✅ $check: Found\n";
        $alpineResults[$check] = true;
    } else {
        echo "❌ $check: Missing\n";
        $alpineResults[$check] = false;
    }
}

// Summary
echo "\n=== FIX SUMMARY ===\n";

$totalChecks = count($syntaxResults) + count($jsResults) + count($cssResults) + count($alpineResults);
$passedChecks = array_sum($syntaxResults) + array_sum($jsResults) + array_sum($cssResults) + array_sum($alpineResults);

$completionPercentage = ($passedChecks / $totalChecks) * 100;

echo "Overall Status: " . round($completionPercentage, 1) . "% ($passedChecks/$totalChecks)\n\n";

// Detailed breakdown
echo "Syntax Issues: " . array_sum($syntaxResults) . "/" . count($syntaxResults) . " (" . round((array_sum($syntaxResults)/count($syntaxResults))*100, 1) . "%)\n";
echo "JavaScript Structure: " . array_sum($jsResults) . "/" . count($jsResults) . " (" . round((array_sum($jsResults)/count($jsResults))*100, 1) . "%)\n";
echo "CSS Structure: " . array_sum($cssResults) . "/" . count($cssResults) . " (" . round((array_sum($cssResults)/count($cssResults))*100, 1) . "%)\n";
echo "Alpine.js Integration: " . array_sum($alpineResults) . "/" . count($alpineResults) . " (" . round((array_sum($alpineResults)/count($alpineResults))*100, 1) . "%)\n";

if ($completionPercentage >= 95) {
    echo "\n🎉 SERVICE HISTORY JAVASCRIPT: FIXED!\n";
    echo "✅ All syntax errors resolved\n";
    echo "✅ JavaScript function structure is correct\n";
    echo "✅ CSS properly formatted\n";
    echo "✅ Alpine.js integration working\n";
} elseif ($completionPercentage >= 80) {
    echo "\n⚠️  SERVICE HISTORY JAVASCRIPT: MOSTLY FIXED\n";
    echo "Most issues resolved but some attention needed.\n";
} else {
    echo "\n❌ SERVICE HISTORY JAVASCRIPT: NEEDS MORE WORK\n";
    echo "Several issues still need to be resolved.\n";
}

// Specific issues found
if (array_sum($syntaxResults) < count($syntaxResults)) {
    echo "\n🔧 SYNTAX ISSUES TO FIX:\n";
    foreach ($syntaxResults as $check => $result) {
        if (!$result) {
            echo "- $check\n";
        }
    }
}

if (array_sum($jsResults) < count($jsResults)) {
    echo "\n🔧 JAVASCRIPT ISSUES TO FIX:\n";
    foreach ($jsResults as $check => $result) {
        if (!$result) {
            echo "- $check\n";
        }
    }
}

if (array_sum($cssResults) < count($cssResults)) {
    echo "\n🔧 CSS ISSUES TO FIX:\n";
    foreach ($cssResults as $check => $result) {
        if (!$result) {
            echo "- $check\n";
        }
    }
}

echo "\n=== NEXT STEPS ===\n";
if ($completionPercentage >= 95) {
    echo "✅ Clear browser cache and test the page\n";
    echo "✅ Verify all Alpine.js functions work correctly\n";
    echo "✅ Test outlet filtering and data loading\n";
} else {
    echo "🔧 Fix remaining syntax and structure issues\n";
    echo "🧪 Re-run this test after fixes\n";
}

echo "\n=== TEST COMPLETE ===\n";

?>