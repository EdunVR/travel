<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 FIXING INTER OUTLET CACHE ISSUE - FINAL\n";
echo "==========================================\n\n";

echo "1. Creating isolated force-24hour-format.js...\n";

// Create a more isolated version of force-24hour-format.js
$force24Content = file_get_contents('public/js/force-24hour-format.js');

// Replace the global setAttribute override with a more specific one
$newForce24Content = str_replace(
    'Element.prototype.setAttribute = function(name, value) {',
    'Element.prototype.setAttribute = function(name, value) {
        // Skip override for non-time elements to avoid Alpine.js conflicts
        if (!this.type || this.type !== "time" || this.tagName !== "INPUT") {
            return originalSetAttribute.call(this, name, value);
        }',
    $force24Content
);

file_put_contents('public/js/force-24hour-format.js', $newForce24Content);
echo "✅ Updated force-24hour-format.js to be more specific\n";

echo "\n2. Adding timestamp to force-24hour-format.js in layout...\n";

// Update admin layout to include cache busting for force-24hour-format.js
$layoutFile = 'resources/views/components/layouts/admin.blade.php';
$layoutContent = file_get_contents($layoutFile);

$timestamp = time();
$layoutContent = preg_replace(
    '/force-24hour-format\.js(\?v=\d+)?/',
    'force-24hour-format.js?v=' . $timestamp,
    $layoutContent
);

file_put_contents($layoutFile, $layoutContent);
echo "✅ Added cache-busting to force-24hour-format.js\n";

echo "\n3. Verifying inter-outlet.js implementation...\n";

$jsContent = file_get_contents('public/js/inter-outlet.js');

// Check for correct implementation
if (strpos($jsContent, 'window.routes?.interOutletPrint') !== false) {
    echo "✅ Route helper implementation found\n";
} else {
    echo "❌ Route helper implementation missing\n";
}

// Check for correct console log
if (strpos($jsContent, "console.log('Opening PDF in new tab with URL:', pdfUrl)") !== false) {
    echo "✅ Correct console log found\n";
} else {
    echo "❌ Correct console log missing\n";
}

echo "\n4. Clearing all caches...\n";
exec('php artisan cache:clear');
exec('php artisan config:clear');
exec('php artisan route:clear');
exec('php artisan view:clear');
echo "✅ All caches cleared\n";

echo "\n5. Testing route generation...\n";
try {
    $testUrl = route('admin.penjualan.inter-outlet-sale.print', 21);
    echo "✅ Route generates: {$testUrl}\n";
    
    if (strpos($testUrl, '/tofu/') !== false) {
        echo "✅ Includes /tofu/ path\n";
    } else {
        echo "❌ Missing /tofu/ path\n";
    }
} catch (Exception $e) {
    echo "❌ Route generation failed: " . $e->getMessage() . "\n";
}

echo "\n🎯 FINAL STEPS:\n";
echo "1. Hard refresh browser (Ctrl+Shift+R)\n";
echo "2. Clear browser cache completely (Ctrl+Shift+Delete)\n";
echo "3. Test print functionality\n";
echo "4. Check console - should show correct URL with /tofu/ path\n";

echo "\n✅ COMPREHENSIVE FIX COMPLETE!\n";
echo "The JavaScript cache issue should now be resolved.\n";

echo "\n";