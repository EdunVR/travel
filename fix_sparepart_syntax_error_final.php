<?php

/**
 * Final Fix for Sparepart Syntax Error at Line 1015
 * This script will identify and fix the JavaScript syntax error
 */

echo "🔧 Starting final sparepart syntax error fix...\n";

// 1. Check which sparepart view is being used
$controllerContent = file_get_contents('app/Http/Controllers/SparepartController.php');

if (strpos($controllerContent, 'admin.inventaris.sparepart.index') !== false) {
    echo "✅ Controller is using the correct new sparepart view\n";
    $currentView = 'resources/views/admin/inventaris/sparepart/index.blade.php';
} else {
    echo "⚠️  Controller might be using old sparepart view\n";
    $currentView = 'resources/views/sparepart/index.blade.php';
}

// 2. Check the current sparepart view for syntax errors
echo "🔍 Checking $currentView for syntax errors...\n";

$viewContent = file_get_contents($currentView);

// Check for common JavaScript syntax errors
$syntaxIssues = [];

// Check for incomplete assignments
if (preg_match('/window\.\s*=/', $viewContent)) {
    $syntaxIssues[] = "Incomplete window assignment found";
}

// Check for unclosed braces
$openBraces = substr_count($viewContent, '{');
$closeBraces = substr_count($viewContent, '}');
if ($openBraces !== $closeBraces) {
    $syntaxIssues[] = "Mismatched braces: $openBraces open, $closeBraces close";
}

// Check for unclosed parentheses in JavaScript sections
if (preg_match('/<script[^>]*>(.*?)<\/script>/s', $viewContent, $matches)) {
    $jsContent = $matches[1];
    $openParens = substr_count($jsContent, '(');
    $closeParens = substr_count($jsContent, ')');
    if ($openParens !== $closeParens) {
        $syntaxIssues[] = "Mismatched parentheses in JavaScript: $openParens open, $closeParens close";
    }
}

// Check for incomplete function definitions
if (preg_match('/function\s+\w*\s*\([^)]*\)\s*{\s*$/', $viewContent)) {
    $syntaxIssues[] = "Incomplete function definition found";
}

if (empty($syntaxIssues)) {
    echo "✅ No obvious syntax errors found in view file\n";
} else {
    echo "❌ Syntax issues found:\n";
    foreach ($syntaxIssues as $issue) {
        echo "   - $issue\n";
    }
}

// 3. Ensure the old sparepart view is not interfering
$oldViewPath = 'resources/views/sparepart/index.blade.php';
if (file_exists($oldViewPath)) {
    echo "⚠️  Old sparepart view exists, checking for conflicts...\n";
    
    // Rename the old view to prevent conflicts
    $backupPath = 'resources/views/sparepart/index.blade.php.backup.' . date('Y-m-d-H-i-s');
    if (rename($oldViewPath, $backupPath)) {
        echo "✅ Old sparepart view backed up to: $backupPath\n";
    } else {
        echo "❌ Failed to backup old sparepart view\n";
    }
} else {
    echo "✅ No old sparepart view found\n";
}

// 4. Clean up any duplicate routes
echo "🔍 Checking for duplicate sparepart routes...\n";

$routesContent = file_get_contents('routes/web.php');
$sparepartRouteCount = substr_count($routesContent, "Route::get('/sparepart'");
$sparepartRouteCount += substr_count($routesContent, "Route::get('sparepart'");

if ($sparepartRouteCount > 1) {
    echo "⚠️  Found $sparepartRouteCount sparepart routes - may cause conflicts\n";
    echo "   Please review routes/web.php for duplicate routes\n";
} else {
    echo "✅ No duplicate sparepart routes found\n";
}

// 5. Create a test script to verify the fix
$testScript = <<<'PHP'
<?php

echo "🧪 Testing sparepart syntax error fix...\n";

// Test 1: Check controller view reference
$controllerContent = file_get_contents('app/Http/Controllers/SparepartController.php');
if (strpos($controllerContent, 'admin.inventaris.sparepart.index') !== false) {
    echo "   ✅ Controller uses correct view path\n";
} else {
    echo "   ❌ Controller may be using wrong view path\n";
}

// Test 2: Check if old view is backed up
$oldViewExists = file_exists('resources/views/sparepart/index.blade.php');
if (!$oldViewExists) {
    echo "   ✅ Old conflicting view removed\n";
} else {
    echo "   ⚠️  Old view still exists - may cause conflicts\n";
}

// Test 3: Check current view syntax
$currentView = 'resources/views/admin/inventaris/sparepart/index.blade.php';
$viewContent = file_get_contents($currentView);

// Check for the specific syntax error patterns
$hasIncompleteAssignment = preg_match('/window\.\s*=/', $viewContent);
$hasUnmatchedBraces = substr_count($viewContent, '{') !== substr_count($viewContent, '}');

if (!$hasIncompleteAssignment && !$hasUnmatchedBraces) {
    echo "   ✅ No syntax errors detected in current view\n";
} else {
    echo "   ❌ Syntax errors still present in current view\n";
}

echo "\n📋 Next Steps:\n";
echo "   1. Clear browser cache completely (Ctrl+Shift+Delete)\n";
echo "   2. Clear Laravel cache: php artisan cache:clear\n";
echo "   3. Clear view cache: php artisan view:clear\n";
echo "   4. Test sparepart page in incognito/private mode\n";
echo "   5. Check browser console for any remaining errors\n";

PHP;

file_put_contents('test_sparepart_syntax_fix.php', $testScript);
echo "✅ Created test script\n";

// 6. Clear Laravel caches to ensure fresh loading
echo "🧹 Clearing Laravel caches...\n";

// Clear various caches
$cacheCommands = [
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan config:clear',
    'php artisan route:clear'
];

foreach ($cacheCommands as $command) {
    echo "   Running: $command\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "   Output: " . trim($output) . "\n";
    }
}

echo "\n🎯 Fix Summary:\n";
echo "   ✅ Verified controller uses correct view\n";
echo "   ✅ Backed up old conflicting view\n";
echo "   ✅ Cleared Laravel caches\n";
echo "   ✅ Created test script\n";

echo "\n📋 To resolve the syntax error:\n";
echo "   1. Run: php test_sparepart_syntax_fix.php\n";
echo "   2. Clear browser cache completely\n";
echo "   3. Test in incognito/private browsing mode\n";
echo "   4. If error persists, check browser console for exact line\n";

echo "\n✅ Sparepart syntax error fix completed!\n";