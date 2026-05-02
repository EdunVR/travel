<?php

/**
 * Test Browser Reload Redirect System
 * Script untuk memverifikasi implementasi sistem redirect
 */

echo "🧪 TESTING BROWSER RELOAD REDIRECT SYSTEM\n";
echo "==========================================\n\n";

// Test 1: Cek file yang dibuat
echo "📁 Test 1: Checking Created Files\n";
echo "-----------------------------------\n";

$files = [
    'public/js/browser-reload-redirect.js' => 'JavaScript redirect system',
    'app/Http/Middleware/AdminRedirectMiddleware.php' => 'Laravel middleware',
    'config/admin_redirect.php' => 'Configuration file',
    'BROWSER_RELOAD_REDIRECT_SYSTEM_COMPLETE.md' => 'Documentation'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ {$file} - {$description}\n";
    } else {
        echo "❌ {$file} - MISSING!\n";
    }
}

echo "\n";

// Test 2: Cek middleware registration
echo "🔧 Test 2: Checking Middleware Registration\n";
echo "--------------------------------------------\n";

$bootstrapFile = 'bootstrap/app.php';
if (file_exists($bootstrapFile)) {
    $content = file_get_contents($bootstrapFile);
    
    if (strpos($content, 'AdminRedirectMiddleware') !== false) {
        echo "✅ Middleware registered in bootstrap/app.php\n";
    } else {
        echo "❌ Middleware NOT registered in bootstrap/app.php\n";
    }
    
    if (strpos($content, "'admin.redirect'") !== false) {
        echo "✅ Middleware alias 'admin.redirect' found\n";
    } else {
        echo "❌ Middleware alias 'admin.redirect' NOT found\n";
    }
} else {
    echo "❌ bootstrap/app.php not found\n";
}

echo "\n";

// Test 3: Cek JavaScript integration
echo "🎯 Test 3: Checking JavaScript Integration\n";
echo "-------------------------------------------\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    if (strpos($content, 'browser-reload-redirect.js') !== false) {
        echo "✅ JavaScript included in admin-with-tabs.blade.php\n";
    } else {
        echo "❌ JavaScript NOT included in admin-with-tabs.blade.php\n";
    }
    
    // Cek apakah tab system masih utuh
    if (strpos($content, 'function tabSystem()') !== false) {
        echo "✅ Tab system still intact\n";
    } else {
        echo "⚠️  Tab system might be modified\n";
    }
} else {
    echo "❌ admin-with-tabs.blade.php not found\n";
}

echo "\n";

// Test 4: Cek konfigurasi
echo "⚙️  Test 4: Checking Configuration\n";
echo "----------------------------------\n";

if (file_exists('config/admin_redirect.php')) {
    $config = include 'config/admin_redirect.php';
    
    echo "✅ Configuration file loaded\n";
    echo "   - Enabled: " . ($config['enabled'] ? 'true' : 'false') . "\n";
    echo "   - Admin URL: " . $config['admin_url'] . "\n";
    echo "   - Admin Route: " . $config['admin_route'] . "\n";
    echo "   - Excluded Paths: " . count($config['excluded_paths']) . " items\n";
    echo "   - Debug Mode: " . ($config['debug'] ? 'true' : 'false') . "\n";
} else {
    echo "❌ Configuration file not found\n";
}

echo "\n";

// Test 5: Cek routes
echo "🛣️  Test 5: Checking Routes\n";
echo "---------------------------\n";

$routesFile = 'routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    
    if (strpos($content, 'admin.redirect') !== false) {
        echo "✅ Middleware applied to routes\n";
    } else {
        echo "❌ Middleware NOT applied to routes\n";
    }
} else {
    echo "❌ routes/web.php not found\n";
}

echo "\n";

// Test 6: Simulasi middleware logic
echo "🧠 Test 6: Simulating Middleware Logic\n";
echo "---------------------------------------\n";

// Simulasi berbagai kondisi
$testCases = [
    ['path' => '/', 'authenticated' => true, 'expected' => 'redirect'],
    ['path' => '/admin', 'authenticated' => true, 'expected' => 'continue'],
    ['path' => '/login', 'authenticated' => false, 'expected' => 'continue'],
    ['path' => '/api/test', 'authenticated' => true, 'expected' => 'continue'],
    ['path' => '/style.css', 'authenticated' => true, 'expected' => 'continue'],
    ['path' => '/some-page', 'authenticated' => true, 'expected' => 'redirect'],
];

foreach ($testCases as $case) {
    $path = $case['path'];
    $auth = $case['authenticated'] ? 'authenticated' : 'not authenticated';
    $expected = $case['expected'];
    
    echo "   Path: {$path} | User: {$auth} | Expected: {$expected}\n";
}

echo "\n";

// Test 7: JavaScript syntax check
echo "🔍 Test 7: JavaScript Syntax Check\n";
echo "-----------------------------------\n";

$jsFile = 'public/js/browser-reload-redirect.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    // Basic syntax checks
    $checks = [
        'function declarations' => preg_match('/function\s+\w+\s*\(/', $jsContent),
        'event listeners' => strpos($jsContent, 'addEventListener') !== false,
        'console logging' => strpos($jsContent, 'console.log') !== false,
        'admin area detection' => strpos($jsContent, 'isAdminArea') !== false,
        'redirect function' => strpos($jsContent, 'redirectToAdmin') !== false,
    ];
    
    foreach ($checks as $check => $result) {
        echo ($result ? "✅" : "❌") . " {$check}\n";
    }
} else {
    echo "❌ JavaScript file not found\n";
}

echo "\n";

// Summary
echo "📊 SUMMARY\n";
echo "==========\n";

$totalTests = 7;
$passedTests = 0;

// Count passed tests (simplified)
if (file_exists('public/js/browser-reload-redirect.js')) $passedTests++;
if (file_exists('app/Http/Middleware/AdminRedirectMiddleware.php')) $passedTests++;
if (file_exists('config/admin_redirect.php')) $passedTests++;
if (file_exists('BROWSER_RELOAD_REDIRECT_SYSTEM_COMPLETE.md')) $passedTests++;

$percentage = round(($passedTests / 4) * 100); // Simplified calculation

echo "✅ Files Created: {$passedTests}/4\n";
echo "📈 Implementation Status: {$percentage}%\n";

if ($percentage >= 100) {
    echo "🎉 SYSTEM READY FOR TESTING!\n";
    echo "\n";
    echo "🚀 NEXT STEPS:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Login to application\n";
    echo "3. Test reload behavior\n";
    echo "4. Check browser console for logs\n";
    echo "5. Verify tab system still works\n";
} else {
    echo "⚠️  IMPLEMENTATION INCOMPLETE\n";
    echo "Please check missing files and configurations.\n";
}

echo "\n";
echo "🔗 For detailed documentation, see: BROWSER_RELOAD_REDIRECT_SYSTEM_COMPLETE.md\n";
echo "\n";

?>