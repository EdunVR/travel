<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 FORCE CLEAR JAVASCRIPT CACHE\n";
echo "===============================\n\n";

// Check current file
$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $fileTime = filemtime($jsFile);
    echo "Current file timestamp: " . date('Y-m-d H:i:s', $fileTime) . "\n";
    
    // Check if it contains the correct implementation
    $content = file_get_contents($jsFile);
    
    if (strpos($content, 'window.routes?.interOutletPrint') !== false) {
        echo "✅ File contains route helper implementation\n";
    } else {
        echo "❌ File missing route helper implementation\n";
    }
    
    if (strpos($content, "console.log('Opening PDF in new tab with URL:', pdfUrl)") !== false) {
        echo "✅ File contains correct console log\n";
    } else {
        echo "❌ File missing correct console log\n";
    }
} else {
    echo "❌ JavaScript file not found\n";
}

echo "\n🔄 Adding cache-busting version to view file...\n";

// Update the view file to include cache busting
$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
$viewContent = file_get_contents($viewFile);

// Add version parameter to script tag
$timestamp = time();
$newScriptTag = '<script src="{{ asset(\'js/inter-outlet.js?v=' . $timestamp . '\') }}"></script>';

// Replace the existing script tag
$viewContent = preg_replace(
    '/<script src="{{ asset\(\'js\/inter-outlet\.js.*?\'\) }}"><\/script>/',
    $newScriptTag,
    $viewContent
);

file_put_contents($viewFile, $viewContent);

echo "✅ Added cache-busting version: ?v={$timestamp}\n";

echo "\n🧹 Clearing all caches...\n";
exec('php artisan cache:clear');
exec('php artisan config:clear');
exec('php artisan route:clear');
exec('php artisan view:clear');

echo "✅ All caches cleared\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Hard refresh browser (Ctrl+Shift+R)\n";
echo "2. Clear browser cache completely\n";
echo "3. Check browser console - should show correct URL\n";
echo "4. Test print functionality\n";

echo "\n";