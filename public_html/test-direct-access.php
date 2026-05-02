<?php
// Test Direct Access - Simulate index.php
// Akses: https://hmtourtravel.com/test-direct-access.php

echo "<h1>🧪 Test Direct Access (Simulate index.php)</h1>";
echo "<p>This simulates what happens when you access the main site</p>";
echo "<hr>";

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('LARAVEL_START', microtime(true));

$laravelPath = __DIR__ . '/../laravel_app';

echo "<h2>Step 1: Check Maintenance Mode</h2>";
$maintenancePath = $laravelPath . '/storage/framework/maintenance.php';
if (file_exists($maintenancePath)) {
    echo "⚠️ Maintenance mode is ON<br>";
    echo "File: <code>$maintenancePath</code><br>";
} else {
    echo "✓ No maintenance mode<br>";
}

echo "<br><h2>Step 2: Load Autoloader</h2>";
$autoloadPath = $laravelPath . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("❌ Autoload not found: $autoloadPath");
}

try {
    require $autoloadPath;
    echo "✓ Autoloader loaded<br>";
} catch (Exception $e) {
    die("❌ Error loading autoloader: " . $e->getMessage());
}

echo "<br><h2>Step 3: Load Bootstrap</h2>";
$bootstrapPath = $laravelPath . '/bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    die("❌ Bootstrap not found: $bootstrapPath");
}

try {
    $app = require $bootstrapPath;
    echo "✓ Bootstrap loaded<br>";
    echo "App class: " . get_class($app) . "<br>";
} catch (Exception $e) {
    die("❌ Error loading bootstrap: " . $e->getMessage());
}

echo "<br><h2>Step 4: Make Kernel</h2>";
try {
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✓ Kernel created<br>";
    echo "Kernel class: " . get_class($kernel) . "<br>";
} catch (Exception $e) {
    echo "❌ Error making kernel: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    
    echo "<br><h2>🔍 Debug Info:</h2>";
    echo "App environment: ";
    try {
        echo $app->environment();
    } catch (Exception $e2) {
        echo "ERROR: " . $e2->getMessage();
    }
    echo "<br>";
    
    echo "<br><h2>⚠️ SOLUTION:</h2>";
    echo "<p>The error happens when creating the Kernel. This is usually because:</p>";
    echo "<ol>";
    echo "<li>Config cache is corrupted</li>";
    echo "<li>Bootstrap cache is corrupted</li>";
    echo "<li>.env file has issues</li>";
    echo "</ol>";
    
    echo "<p><strong>FIX via SSH:</strong></p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "ssh u127727849@hmtourtravel.com\n";
    echo "cd /home/u127727849/domains/hmtourtravel.com/laravel_app\n\n";
    echo "# Delete ALL cache files\n";
    echo "rm -f bootstrap/cache/config.php\n";
    echo "rm -f bootstrap/cache/routes-v7.php\n";
    echo "rm -f bootstrap/cache/services.php\n";
    echo "rm -f bootstrap/cache/packages.php\n\n";
    echo "# Clear Laravel cache\n";
    echo "php artisan config:clear\n";
    echo "php artisan cache:clear\n";
    echo "php artisan route:clear\n";
    echo "php artisan view:clear\n\n";
    echo "# Regenerate key\n";
    echo "php artisan key:generate --force\n";
    echo "</pre>";
    
    die();
}

echo "<br><h2>Step 5: Handle Request</h2>";
try {
    $request = \Illuminate\Http\Request::capture();
    echo "✓ Request captured<br>";
    echo "URL: " . $request->url() . "<br>";
    echo "Method: " . $request->method() . "<br>";
    
    $response = $kernel->handle($request);
    echo "✓ Request handled<br>";
    echo "Status: " . $response->getStatusCode() . "<br>";
    
    echo "<br><h2>✅ SUCCESS!</h2>";
    echo "<p style='color:green; font-size:18px;'><strong>Laravel is working correctly!</strong></p>";
    echo "<p>If you still see error on main site, the problem is likely:</p>";
    echo "<ul>";
    echo "<li>index.php file is wrong</li>";
    echo "<li>.htaccess is wrong</li>";
    echo "<li>Cache needs to be cleared</li>";
    echo "</ul>";
    
    echo "<br><h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Clear cache via SSH (see commands above)</li>";
    echo "<li>Make sure index.php is correct</li>";
    echo "<li>Make sure .htaccess is correct</li>";
    echo "<li>Access <a href='https://hmtourtravel.com'>https://hmtourtravel.com</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "❌ Error handling request: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH DEBUG!</strong></p>";
?>
