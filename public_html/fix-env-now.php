<?php
// FIX .env Configuration
// Akses: https://hmtourtravel.com/fix-env-now.php

echo "<h1>🔧 Fix .env Configuration</h1>";

$laravelPath = __DIR__ . '/../laravel_app';
$envPath = $laravelPath . '/.env';
$envExamplePath = $laravelPath . '/.env.example';

echo "<h2>Step 1: Check .env File</h2>";

if (!file_exists($envPath)) {
    echo "❌ .env file NOT FOUND!<br>";
    
    if (file_exists($envExamplePath)) {
        echo "✓ .env.example found. Copying to .env...<br>";
        if (copy($envExamplePath, $envPath)) {
            echo "✓ .env created from .env.example<br>";
        } else {
            echo "❌ Failed to copy .env.example<br>";
        }
    } else {
        echo "❌ .env.example also not found!<br>";
    }
} else {
    echo "✓ .env file exists<br>";
}

echo "<br><h2>Step 2: Check .env Content</h2>";

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check APP_KEY
    echo "<h3>APP_KEY:</h3>";
    if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches)) {
        $appKey = trim($matches[1]);
        if (empty($appKey) || $appKey === 'base64:' || $appKey === '') {
            echo "❌ APP_KEY is EMPTY or INVALID<br>";
            echo "<strong>ACTION NEEDED: Generate APP_KEY</strong><br>";
            
            // Try to generate APP_KEY
            echo "<br><h3>Generating APP_KEY...</h3>";
            $newKey = 'base64:' . base64_encode(random_bytes(32));
            
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $newKey, $envContent);
            
            if (file_put_contents($envPath, $envContent)) {
                echo "✓ APP_KEY generated and saved!<br>";
                echo "New APP_KEY: <code>" . htmlspecialchars($newKey) . "</code><br>";
            } else {
                echo "❌ Failed to save APP_KEY<br>";
            }
        } else {
            echo "✓ APP_KEY is set (length: " . strlen($appKey) . ")<br>";
        }
    } else {
        echo "❌ APP_KEY line not found in .env<br>";
        echo "<strong>Adding APP_KEY...</strong><br>";
        
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent .= "\nAPP_KEY=" . $newKey . "\n";
        
        if (file_put_contents($envPath, $envContent)) {
            echo "✓ APP_KEY added!<br>";
        }
    }
    
    // Check DB settings
    echo "<br><h3>Database Settings:</h3>";
    
    $dbSettings = [
        'DB_HOST' => 'localhost',
        'DB_DATABASE' => 'u127727849_hmtour',
        'DB_USERNAME' => 'u127727849_hmtour'
    ];
    
    foreach ($dbSettings as $key => $expectedValue) {
        if (preg_match('/^' . $key . '=(.*)$/m', $envContent, $matches)) {
            $value = trim($matches[1]);
            echo "$key: <code>$value</code>";
            
            if ($key === 'DB_HOST' && $value === '127.0.0.1') {
                echo " ⚠️ Should be 'localhost'<br>";
                echo "<strong>Fixing...</strong><br>";
                
                $envContent = preg_replace('/^DB_HOST=.*$/m', 'DB_HOST=localhost', $envContent);
                file_put_contents($envPath, $envContent);
                echo "✓ Fixed to 'localhost'<br>";
            } else {
                echo " ✓<br>";
            }
        } else {
            echo "❌ $key not found<br>";
        }
    }
    
    // Check APP_ENV
    echo "<br><h3>Environment:</h3>";
    if (preg_match('/^APP_ENV=(.*)$/m', $envContent, $matches)) {
        $appEnv = trim($matches[1]);
        echo "APP_ENV: <code>$appEnv</code>";
        
        if ($appEnv === 'local') {
            echo " ⚠️ Should be 'production' for live server<br>";
            echo "<strong>Fixing...</strong><br>";
            
            $envContent = preg_replace('/^APP_ENV=.*$/m', 'APP_ENV=production', $envContent);
            file_put_contents($envPath, $envContent);
            echo "✓ Changed to 'production'<br>";
        } else {
            echo " ✓<br>";
        }
    }
    
    // Check APP_DEBUG
    if (preg_match('/^APP_DEBUG=(.*)$/m', $envContent, $matches)) {
        $appDebug = trim($matches[1]);
        echo "APP_DEBUG: <code>$appDebug</code>";
        
        if ($appDebug === 'true') {
            echo " ⚠️ Should be 'false' for production<br>";
            echo "<strong>Fixing...</strong><br>";
            
            $envContent = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=false', $envContent);
            file_put_contents($envPath, $envContent);
            echo "✓ Changed to 'false'<br>";
        } else {
            echo " ✓<br>";
        }
    }
}

echo "<br><h2>Step 3: Clear Config Cache</h2>";

// Try to clear config cache
$cacheFile = $laravelPath . '/bootstrap/cache/config.php';
if (file_exists($cacheFile)) {
    if (unlink($cacheFile)) {
        echo "✓ Config cache cleared<br>";
    } else {
        echo "⚠️ Could not delete config cache (might need SSH)<br>";
    }
} else {
    echo "✓ No config cache to clear<br>";
}

echo "<br><h2>Step 4: Test Laravel Load Again</h2>";

try {
    $vendorPath = $laravelPath . '/vendor/autoload.php';
    $bootstrapPath = $laravelPath . '/bootstrap/app.php';
    
    if (!file_exists($vendorPath)) {
        throw new Exception("Vendor autoload not found!");
    }
    
    require $vendorPath;
    $app = require $bootstrapPath;
    
    echo "✓✓✓ <strong style='color:green;'>LARAVEL LOADED SUCCESSFULLY!</strong><br>";
    echo "<br>Laravel Version: " . app()->version() . "<br>";
    echo "Environment: " . app()->environment() . "<br>";
    
    echo "<br><h2>✅ NEXT STEPS:</h2>";
    echo "<ol>";
    echo "<li>Access your site: <a href='https://hmtourtravel.com' target='_blank'>https://hmtourtravel.com</a></li>";
    echo "<li>If you see error, run migrations via SSH:<br>";
    echo "<code>cd /home/u127727849/domains/hmtourtravel.com/laravel_app && php artisan migrate --force</code></li>";
    echo "<li>Clear cache via SSH:<br>";
    echo "<code>php artisan config:cache && php artisan route:cache && php artisan view:cache</code></li>";
    echo "<li><strong>DELETE this file after success!</strong></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>Still Error:</strong><br>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    
    echo "<br><h2>⚠️ MANUAL FIX NEEDED:</h2>";
    echo "<p>You need to access via SSH and run:</p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "ssh u127727849@hmtourtravel.com\n";
    echo "cd /home/u127727849/domains/hmtourtravel.com/laravel_app\n";
    echo "php artisan key:generate --force\n";
    echo "php artisan config:clear\n";
    echo "php artisan cache:clear\n";
    echo "</pre>";
}

echo "<br><hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH SELESAI!</strong></p>";
?>
