<?php
// Test Laravel Load - Bypass .htaccess
// Akses: https://hmtourtravel.com/test-laravel-load.php

echo "<h1>🧪 Test Laravel Load</h1>";
echo "<p>Test ini bypass .htaccess untuk cek apakah Laravel bisa load</p>";
echo "<hr>";

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = __DIR__ . '/../laravel_app';

echo "<h2>1. Path Check</h2>";
echo "Laravel Path: <code>$laravelPath</code><br>";
echo "Exists: " . (is_dir($laravelPath) ? '✓ YES' : '✗ NO') . "<br><br>";

echo "<h2>2. Vendor Check</h2>";
$vendorPath = $laravelPath . '/vendor/autoload.php';
echo "Vendor Path: <code>$vendorPath</code><br>";
echo "Exists: " . (file_exists($vendorPath) ? '✓ YES' : '✗ NO') . "<br><br>";

echo "<h2>3. Bootstrap Check</h2>";
$bootstrapPath = $laravelPath . '/bootstrap/app.php';
echo "Bootstrap Path: <code>$bootstrapPath</code><br>";
echo "Exists: " . (file_exists($bootstrapPath) ? '✓ YES' : '✗ NO') . "<br><br>";

echo "<h2>4. .env Check</h2>";
$envPath = $laravelPath . '/.env';
echo ".env Path: <code>$envPath</code><br>";
echo "Exists: " . (file_exists($envPath) ? '✓ YES' : '✗ NO') . "<br>";

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check APP_KEY
    if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
        $appKey = trim($matches[1]);
        if (empty($appKey) || $appKey === 'base64:') {
            echo "APP_KEY: ✗ EMPTY or INVALID<br>";
        } else {
            echo "APP_KEY: ✓ SET (length: " . strlen($appKey) . ")<br>";
        }
    } else {
        echo "APP_KEY: ✗ NOT FOUND<br>";
    }
    
    // Check DB settings
    if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
        $dbHost = trim($matches[1]);
        echo "DB_HOST: <code>$dbHost</code>";
        if ($dbHost === '127.0.0.1') {
            echo " ⚠️ Should be 'localhost' for Hostinger<br>";
        } else {
            echo " ✓<br>";
        }
    }
    
    if (preg_match('/DB_DATABASE=(.+)/', $envContent, $matches)) {
        echo "DB_DATABASE: <code>" . trim($matches[1]) . "</code><br>";
    }
}
echo "<br>";

echo "<h2>5. Try Load Laravel</h2>";
try {
    if (!file_exists($vendorPath)) {
        throw new Exception("Vendor autoload not found! Upload folder 'vendor' ke laravel_app");
    }
    
    echo "Loading autoload...<br>";
    require $vendorPath;
    echo "✓ Autoload loaded<br>";
    
    if (!file_exists($bootstrapPath)) {
        throw new Exception("Bootstrap file not found!");
    }
    
    echo "Loading bootstrap...<br>";
    $app = require $bootstrapPath;
    echo "✓ Bootstrap loaded<br>";
    
    echo "<br><strong style='color:green;'>✓✓✓ LARAVEL BERHASIL LOAD!</strong><br>";
    echo "<p>Ini berarti masalahnya ada di .htaccess atau routing</p>";
    
    // Try to get Laravel version
    echo "<br><h3>Laravel Info:</h3>";
    echo "Laravel Version: " . app()->version() . "<br>";
    echo "Environment: " . app()->environment() . "<br>";
    
} catch (Exception $e) {
    echo "<br><strong style='color:red;'>✗✗✗ ERROR LOADING LARAVEL</strong><br>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    echo "<pre style='background:#f5f5f5; padding:10px; font-size:12px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h2>6. PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH DEBUG!</strong></p>";
?>
