<?php
/**
 * Debug Error 500 - Tampilkan Error Langsung
 * Upload ke public_html dan akses via browser
 * 
 * ⚠️ HAPUS FILE INI SETELAH SELESAI DEBUG!
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug Error 500</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #28a745; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #0056b3; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class='container'>";

echo "<h1>🔍 Debug Error 500</h1>";

// Path ke Laravel
$laravelPath = '/home/u127727849/domains/hmtourtravel.com/laravel_app';

echo "<div class='section'>";
echo "<h2>1. Check Laravel Path</h2>";
if (is_dir($laravelPath)) {
    echo "<div class='success'>✅ Laravel path exists: <code>$laravelPath</code></div>";
} else {
    echo "<div class='error'>❌ Laravel path NOT found: <code>$laravelPath</code></div>";
    echo "<div class='warning'>Please check if the path is correct!</div>";
    echo "</div></div></body></html>";
    exit;
}
echo "</div>";

// Check storage/logs
echo "<div class='section'>";
echo "<h2>2. Check Storage/Logs Folder</h2>";
$logsPath = $laravelPath . '/storage/logs';
if (is_dir($logsPath)) {
    echo "<div class='success'>✅ Logs folder exists</div>";
    
    // Check writable
    if (is_writable($logsPath)) {
        echo "<div class='success'>✅ Logs folder is writable</div>";
    } else {
        echo "<div class='error'>❌ Logs folder is NOT writable</div>";
        echo "<div class='warning'>Permission: " . substr(sprintf('%o', fileperms($logsPath)), -4) . "</div>";
    }
    
    // Check laravel.log
    $logFile = $logsPath . '/laravel.log';
    if (file_exists($logFile)) {
        echo "<div class='info'>📄 laravel.log exists</div>";
        echo "<div class='info'>Size: " . filesize($logFile) . " bytes</div>";
        echo "<div class='info'>Last modified: " . date('Y-m-d H:i:s', filemtime($logFile)) . "</div>";
        
        if (is_writable($logFile)) {
            echo "<div class='success'>✅ laravel.log is writable</div>";
        } else {
            echo "<div class='error'>❌ laravel.log is NOT writable</div>";
            echo "<div class='warning'>Permission: " . substr(sprintf('%o', fileperms($logFile)), -4) . "</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ laravel.log does not exist yet</div>";
    }
} else {
    echo "<div class='error'>❌ Logs folder NOT found</div>";
}
echo "</div>";

// Check .env file
echo "<div class='section'>";
echo "<h2>3. Check .env File</h2>";
$envFile = $laravelPath . '/.env';
if (file_exists($envFile)) {
    echo "<div class='success'>✅ .env file exists</div>";
    
    // Read .env
    $envContent = file_get_contents($envFile);
    $envLines = explode("\n", $envContent);
    
    echo "<div class='info'><strong>Database Configuration:</strong></div>";
    echo "<pre>";
    foreach ($envLines as $line) {
        if (strpos($line, 'DB_') === 0 || strpos($line, 'APP_') === 0) {
            // Hide password
            if (strpos($line, 'DB_PASSWORD') === 0) {
                $parts = explode('=', $line, 2);
                if (isset($parts[1]) && !empty($parts[1])) {
                    echo "DB_PASSWORD=" . str_repeat('*', strlen($parts[1])) . "\n";
                } else {
                    echo "DB_PASSWORD=[EMPTY]\n";
                }
            } else {
                echo htmlspecialchars($line) . "\n";
            }
        }
    }
    echo "</pre>";
} else {
    echo "<div class='error'>❌ .env file NOT found</div>";
}
echo "</div>";

// Try to load Laravel and catch error
echo "<div class='section'>";
echo "<h2>4. Try Loading Laravel</h2>";

try {
    // Load autoloader
    $autoloadFile = $laravelPath . '/vendor/autoload.php';
    if (!file_exists($autoloadFile)) {
        throw new Exception("Autoload file not found: $autoloadFile");
    }
    
    require $autoloadFile;
    echo "<div class='success'>✅ Autoloader loaded</div>";
    
    // Load Laravel app
    $appFile = $laravelPath . '/bootstrap/app.php';
    if (!file_exists($appFile)) {
        throw new Exception("Bootstrap file not found: $appFile");
    }
    
    $app = require_once $appFile;
    echo "<div class='success'>✅ Laravel app loaded</div>";
    
    // Create kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<div class='success'>✅ Kernel created</div>";
    
    // Create request
    $request = Illuminate\Http\Request::capture();
    echo "<div class='success'>✅ Request captured</div>";
    
    // Handle request
    echo "<div class='info'>Attempting to handle request...</div>";
    $response = $kernel->handle($request);
    
    echo "<div class='success'>✅✅✅ Laravel loaded successfully!</div>";
    echo "<div class='info'>Response status: " . $response->getStatusCode() . "</div>";
    
    if ($response->getStatusCode() == 200) {
        echo "<div class='success'>🎉 No errors! Laravel is working!</div>";
        echo "<div class='warning'>If you still see error 500, check:<br>";
        echo "1. Browser cache (Ctrl+Shift+R)<br>";
        echo "2. .htaccess file<br>";
        echo "3. index.php file</div>";
    } else {
        echo "<div class='warning'>Response status is not 200. Check the response.</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ ERROR CAUGHT!</strong><br><br>";
    echo "<strong>Error Message:</strong><br>";
    echo htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br><br>";
    echo "<strong>Stack Trace:</strong><br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
    
    // Suggest solutions
    echo "<div class='warning'>";
    echo "<strong>💡 Possible Solutions:</strong><br><br>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "• <strong>Database password is wrong!</strong><br>";
        echo "• Check DB_PASSWORD in .env file<br>";
        echo "• Reset password in Hostinger Control Panel<br><br>";
    }
    
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "• <strong>PDO MySQL driver not installed!</strong><br>";
        echo "• Contact Hostinger support to enable PDO MySQL<br><br>";
    }
    
    if (strpos($e->getMessage(), 'Permission denied') !== false) {
        echo "• <strong>Permission issue!</strong><br>";
        echo "• Run: chmod -R 775 storage bootstrap/cache<br>";
        echo "• Run: chown -R u127727849:u127727849 storage bootstrap/cache<br><br>";
    }
    
    if (strpos($e->getMessage(), 'Class') !== false && strpos($e->getMessage(), 'not found') !== false) {
        echo "• <strong>Class not found!</strong><br>";
        echo "• Run: composer install --no-dev<br>";
        echo "• Or re-upload vendor folder<br><br>";
    }
    
    echo "</div>";
}

echo "</div>";

// Check PHP version
echo "<div class='section'>";
echo "<h2>5. PHP Information</h2>";
echo "<div class='info'>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "</div>";

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
echo "<div class='info'><strong>Required PHP Extensions:</strong></div>";
echo "<ul>";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li style='color: green;'>✅ $ext</li>";
    } else {
        echo "<li style='color: red;'>❌ $ext (MISSING!)</li>";
    }
}
echo "</ul>";
echo "</div>";

// Show last 50 lines of laravel.log if exists
$logFile = $laravelPath . '/storage/logs/laravel.log';
if (file_exists($logFile) && filesize($logFile) > 0) {
    echo "<div class='section'>";
    echo "<h2>6. Last 50 Lines of Laravel Log</h2>";
    
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    
    echo "<pre style='max-height: 400px; overflow-y: auto;'>";
    echo htmlspecialchars(implode('', $lastLines));
    echo "</pre>";
    echo "</div>";
}

echo "<div class='warning' style='margin-top: 20px;'>";
echo "⚠️ <strong>SECURITY WARNING:</strong><br>";
echo "This file shows sensitive information. <strong>DELETE IT</strong> after debugging!<br>";
echo "File location: <code>/home/u127727849/domains/hmtourtravel.com/public_html/debug-error.php</code>";
echo "</div>";

echo "</div></body></html>";
?>
