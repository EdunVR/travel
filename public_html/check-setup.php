<?php
/**
 * Debug Script - Cek Setup Laravel di Hostinger
 * Akses: https://hmtourtravel.com/check-setup.php
 * HAPUS FILE INI SETELAH SELESAI!
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Setup Check</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .check { margin: 15px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Laravel Setup Check - hmtourtravel.com</h1>";

// 1. Check PHP Version
echo "<div class='check " . (version_compare(PHP_VERSION, '8.2.0', '>=') ? 'success' : 'error') . "'>";
echo "<strong>1. PHP Version:</strong> " . PHP_VERSION;
if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo " ✓ OK (Minimum 8.2 required)";
} else {
    echo " ✗ TOO OLD (Minimum 8.2 required)";
}
echo "</div>";

// 2. Check Current Directory
echo "<div class='check info'>";
echo "<strong>2. Current Directory:</strong><br>";
echo "<code>" . __DIR__ . "</code>";
echo "</div>";

// 3. Check Parent Directory
$parentDir = dirname(__DIR__);
echo "<div class='check info'>";
echo "<strong>3. Parent Directory:</strong><br>";
echo "<code>" . $parentDir . "</code>";
echo "</div>";

// 4. Check laravel_app folder
$laravelAppPath = $parentDir . '/laravel_app';
echo "<div class='check " . (is_dir($laravelAppPath) ? 'success' : 'error') . "'>";
echo "<strong>4. Laravel App Folder:</strong><br>";
echo "<code>" . $laravelAppPath . "</code><br>";
if (is_dir($laravelAppPath)) {
    echo "✓ Folder EXISTS";
} else {
    echo "✗ Folder NOT FOUND!<br>";
    echo "<strong>Action:</strong> Buat folder 'laravel_app' di " . $parentDir;
}
echo "</div>";

// 5. Check vendor folder
$vendorPath = $laravelAppPath . '/vendor';
echo "<div class='check " . (is_dir($vendorPath) ? 'success' : 'error') . "'>";
echo "<strong>5. Vendor Folder:</strong><br>";
echo "<code>" . $vendorPath . "</code><br>";
if (is_dir($vendorPath)) {
    echo "✓ Folder EXISTS";
} else {
    echo "✗ Folder NOT FOUND!<br>";
    echo "<strong>Action:</strong> Upload folder 'vendor' ke laravel_app";
}
echo "</div>";

// 6. Check autoload.php
$autoloadPath = $vendorPath . '/autoload.php';
echo "<div class='check " . (file_exists($autoloadPath) ? 'success' : 'error') . "'>";
echo "<strong>6. Autoload File:</strong><br>";
echo "<code>" . $autoloadPath . "</code><br>";
if (file_exists($autoloadPath)) {
    echo "✓ File EXISTS";
} else {
    echo "✗ File NOT FOUND!";
}
echo "</div>";

// 7. Check bootstrap/app.php
$bootstrapPath = $laravelAppPath . '/bootstrap/app.php';
echo "<div class='check " . (file_exists($bootstrapPath) ? 'success' : 'error') . "'>";
echo "<strong>7. Bootstrap File:</strong><br>";
echo "<code>" . $bootstrapPath . "</code><br>";
if (file_exists($bootstrapPath)) {
    echo "✓ File EXISTS";
} else {
    echo "✗ File NOT FOUND!";
}
echo "</div>";

// 8. Check .env file
$envPath = $laravelAppPath . '/.env';
echo "<div class='check " . (file_exists($envPath) ? 'success' : 'error') . "'>";
echo "<strong>8. .env File:</strong><br>";
echo "<code>" . $envPath . "</code><br>";
if (file_exists($envPath)) {
    echo "✓ File EXISTS<br>";
    
    // Check .env content
    $envContent = file_get_contents($envPath);
    
    // Check APP_KEY
    if (preg_match('/APP_KEY=base64:(.+)/', $envContent, $matches)) {
        echo "<div style='margin-top:10px; padding:10px; background:#d4edda; border-radius:4px;'>";
        echo "✓ APP_KEY is set";
        echo "</div>";
    } else {
        echo "<div style='margin-top:10px; padding:10px; background:#fff3cd; border-radius:4px;'>";
        echo "⚠ APP_KEY is NOT set or empty<br>";
        echo "<strong>Action:</strong> Run: php artisan key:generate";
        echo "</div>";
    }
    
    // Check DB settings
    if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
        $dbHost = trim($matches[1]);
        echo "<div style='margin-top:10px; padding:10px; background:#d1ecf1; border-radius:4px;'>";
        echo "DB_HOST: <code>" . htmlspecialchars($dbHost) . "</code>";
        if ($dbHost === 'localhost') {
            echo " ✓ Correct for Hostinger";
        } elseif ($dbHost === '127.0.0.1') {
            echo " ⚠ Should be 'localhost' for Hostinger";
        }
        echo "</div>";
    }
    
} else {
    echo "✗ File NOT FOUND!<br>";
    echo "<strong>Action:</strong> Upload file .env ke laravel_app";
}
echo "</div>";

// 9. Check storage folder permissions
$storagePath = $laravelAppPath . '/storage';
echo "<div class='check " . (is_writable($storagePath) ? 'success' : 'warning') . "'>";
echo "<strong>9. Storage Folder:</strong><br>";
echo "<code>" . $storagePath . "</code><br>";
if (is_dir($storagePath)) {
    if (is_writable($storagePath)) {
        echo "✓ Folder EXISTS and WRITABLE";
    } else {
        echo "⚠ Folder EXISTS but NOT WRITABLE<br>";
        echo "<strong>Action:</strong> chmod 755 storage";
    }
} else {
    echo "✗ Folder NOT FOUND!";
}
echo "</div>";

// 10. Check bootstrap/cache permissions
$bootstrapCachePath = $laravelAppPath . '/bootstrap/cache';
echo "<div class='check " . (is_writable($bootstrapCachePath) ? 'success' : 'warning') . "'>";
echo "<strong>10. Bootstrap Cache:</strong><br>";
echo "<code>" . $bootstrapCachePath . "</code><br>";
if (is_dir($bootstrapCachePath)) {
    if (is_writable($bootstrapCachePath)) {
        echo "✓ Folder EXISTS and WRITABLE";
    } else {
        echo "⚠ Folder EXISTS but NOT WRITABLE<br>";
        echo "<strong>Action:</strong> chmod 755 bootstrap/cache";
    }
} else {
    echo "✗ Folder NOT FOUND!";
}
echo "</div>";

// 11. Check index.php content
echo "<div class='check info'>";
echo "<strong>11. Index.php Path Check:</strong><br>";
$indexContent = file_get_contents(__DIR__ . '/index.php');
if (strpos($indexContent, '../laravel_app/vendor/autoload.php') !== false) {
    echo "✓ Path to autoload.php looks correct";
} else {
    echo "⚠ Path to autoload.php might be wrong<br>";
    echo "Should contain: <code>../laravel_app/vendor/autoload.php</code>";
}
echo "</div>";

// Summary
echo "<div class='check info'>";
echo "<strong>📋 Summary & Next Steps:</strong><br><br>";

$errors = [];
if (!is_dir($laravelAppPath)) $errors[] = "Folder laravel_app tidak ada";
if (!is_dir($vendorPath)) $errors[] = "Folder vendor tidak ada";
if (!file_exists($envPath)) $errors[] = "File .env tidak ada";
if (!is_writable($storagePath)) $errors[] = "Folder storage tidak writable";

if (empty($errors)) {
    echo "<div style='padding:15px; background:#d4edda; border-radius:4px; margin-top:10px;'>";
    echo "<strong>✓ Setup looks good!</strong><br><br>";
    echo "Jika masih error 500, coba:<br>";
    echo "1. Akses: <a href='/'>https://hmtourtravel.com/</a><br>";
    echo "2. Jika masih error, cek error log di hPanel<br>";
    echo "3. Atau jalankan setup via SSH";
    echo "</div>";
} else {
    echo "<div style='padding:15px; background:#f8d7da; border-radius:4px; margin-top:10px;'>";
    echo "<strong>✗ Issues Found:</strong><br><ul>";
    foreach ($errors as $error) {
        echo "<li>" . $error . "</li>";
    }
    echo "</ul>";
    echo "<br><strong>Fix these issues first!</strong>";
    echo "</div>";
}
echo "</div>";

// PHP Extensions Check
echo "<div class='check info'>";
echo "<strong>12. Required PHP Extensions:</strong><br>";
$required = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
$missing = [];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ " . $ext . "<br>";
    } else {
        echo "✗ " . $ext . " (MISSING)<br>";
        $missing[] = $ext;
    }
}
if (!empty($missing)) {
    echo "<div style='margin-top:10px; padding:10px; background:#fff3cd; border-radius:4px;'>";
    echo "⚠ Missing extensions: " . implode(', ', $missing) . "<br>";
    echo "Contact Hostinger support to enable these.";
    echo "</div>";
}
echo "</div>";

echo "
        <div style='margin-top:30px; padding:15px; background:#fff3cd; border-radius:4px;'>
            <strong>🔒 IMPORTANT:</strong> HAPUS file ini setelah selesai debug!<br>
            File: <code>public_html/check-setup.php</code>
        </div>
    </div>
</body>
</html>";
?>
