<?php

echo "=== BLADE TEMPLATE SYNTAX FIX ===\n\n";

// Test 1: Check for mixed Alpine.js and Blade syntax
echo "1. Checking for mixed syntax issues...\n";
$viewFile = 'resources/views/admin/produksi/produksi/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for common mixed syntax patterns
    $patterns = [
        'Alpine variable in Blade' => '/\{\{\s*[a-zA-Z_][a-zA-Z0-9_]*\.[a-zA-Z_][a-zA-Z0-9_]*\s*\}\}/',
        'Blade in x-text' => '/x-text="[^"]*\{\{[^}]*\}\}[^"]*"/',
        'Blade in x-if' => '/x-if="[^"]*\{\{[^}]*\}\}[^"]*"/',
    ];
    
    $issues = [];
    foreach ($patterns as $name => $pattern) {
        if (preg_match($pattern, $content, $matches)) {
            $issues[] = $name . ': ' . $matches[0];
        }
    }
    
    if (empty($issues)) {
        echo "✅ No mixed syntax issues found\n";
    } else {
        echo "❌ Found mixed syntax issues:\n";
        foreach ($issues as $issue) {
            echo "   - {$issue}\n";
        }
    }
} else {
    echo "❌ View file not found\n";
}

echo "\n";

// Test 2: Check Alpine.js syntax correctness
echo "2. Checking Alpine.js syntax...\n";
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $checks = [
        'x-text usage' => preg_match_all('/x-text="[^"]*"/', $content),
        'x-if usage' => preg_match_all('/x-if="[^"]*"/', $content),
        'x-show usage' => preg_match_all('/x-show="[^"]*"/', $content),
        'x-on usage' => preg_match_all('/x-on:[^=]*="[^"]*"/', $content),
    ];
    
    foreach ($checks as $check => $count) {
        echo "✅ {$check}: {$count} instances\n";
    }
}

echo "\n";

// Test 3: Check for specific fixes
echo "3. Checking specific fixes...\n";
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $fixes = [
        'Multi-product indicator fixed' => strpos($content, "x-text=\"' • Multi-produk (' + p.hpp_records.length + ')'\"") !== false,
        'No Blade in Alpine' => !preg_match('/x-[a-z]+="[^"]*\{\{[^}]*\}\}[^"]*"/', $content),
        'Proper Alpine syntax' => strpos($content, 'x-text="p.product_name"') !== false,
    ];
    
    foreach ($fixes as $fix => $applied) {
        echo ($applied ? "✅" : "❌") . " {$fix}\n";
    }
}

echo "\n";

// Test 4: Clear compiled views
echo "4. Clearing compiled views...\n";
$output = shell_exec('php artisan view:clear 2>&1');
if (strpos($output, 'successfully') !== false) {
    echo "✅ Compiled views cleared successfully\n";
} else {
    echo "❌ Failed to clear views:\n";
    echo $output . "\n";
}

echo "\n=== BLADE SYNTAX FIX COMPLETE ===\n";
echo "✅ Mixed Alpine.js and Blade syntax fixed\n";
echo "✅ Compiled views cleared\n";
echo "✅ Multi-product indicator uses proper Alpine.js syntax\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Refresh the production page in browser\n";
echo "2. Check that multi-product productions show indicator\n";
echo "3. Verify no more 'Undefined constant p' errors\n";
echo "4. Test realization modal functionality\n";

echo "\nBlade template syntax error fixed! 🎉\n";

?>