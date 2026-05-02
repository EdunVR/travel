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
