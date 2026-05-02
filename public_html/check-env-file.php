<?php
// Check .env File Content
// Akses: https://hmtourtravel.com/check-env-file.php

echo "<h1>🔍 Check .env File</h1>";

$laravelPath = __DIR__ . '/../laravel_app';
$envPath = $laravelPath . '/.env';

if (!file_exists($envPath)) {
    echo "<h2 style='color:red;'>❌ .env FILE NOT FOUND!</h2>";
    echo "<p>Path: <code>$envPath</code></p>";
    echo "<p><strong>ACTION:</strong> Upload file .env ke folder laravel_app</p>";
    exit;
}

echo "<h2>✓ .env File Found</h2>";
echo "<p>Path: <code>$envPath</code></p>";
echo "<p>Size: " . filesize($envPath) . " bytes</p>";
echo "<p>Last Modified: " . date('Y-m-d H:i:s', filemtime($envPath)) . "</p>";

echo "<hr>";

$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);

echo "<h2>📋 .env Content (Sensitive data hidden)</h2>";

echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
echo "<tr><th>Key</th><th>Value</th><th>Status</th></tr>";

$criticalKeys = [
    'APP_NAME',
    'APP_ENV',
    'APP_KEY',
    'APP_DEBUG',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD'
];

foreach ($lines as $line) {
    $line = trim($line);
    
    // Skip comments and empty lines
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    
    // Parse key=value
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Only show critical keys
        if (in_array($key, $criticalKeys)) {
            $status = '✓';
            $statusColor = 'green';
            
            // Hide sensitive values
            if (in_array($key, ['APP_KEY', 'DB_PASSWORD'])) {
                if (empty($value)) {
                    $displayValue = '<span style="color:red;">EMPTY!</span>';
                    $status = '❌';
                    $statusColor = 'red';
                } else {
                    $displayValue = '<span style="color:gray;">[HIDDEN - Length: ' . strlen($value) . ']</span>';
                }
            } else {
                $displayValue = htmlspecialchars($value);
                
                // Check for issues
                if (empty($value)) {
                    $status = '⚠️';
                    $statusColor = 'orange';
                    $displayValue = '<span style="color:red;">EMPTY</span>';
                }
                
                // Specific checks
                if ($key === 'APP_KEY' && ($value === 'base64:' || strlen($value) < 20)) {
                    $status = '❌';
                    $statusColor = 'red';
                    $displayValue .= ' <strong style="color:red;">INVALID!</strong>';
                }
                
                if ($key === 'DB_HOST' && $value === '127.0.0.1') {
                    $status = '⚠️';
                    $statusColor = 'orange';
                    $displayValue .= ' <strong style="color:orange;">Should be "localhost"</strong>';
                }
                
                if ($key === 'APP_ENV' && $value === 'local') {
                    $status = '⚠️';
                    $statusColor = 'orange';
                    $displayValue .= ' <strong style="color:orange;">Should be "production"</strong>';
                }
                
                if ($key === 'APP_DEBUG' && $value === 'true') {
                    $status = '⚠️';
                    $statusColor = 'orange';
                    $displayValue .= ' <strong style="color:orange;">Should be "false" for production</strong>';
                }
            }
            
            echo "<tr>";
            echo "<td><strong>$key</strong></td>";
            echo "<td>$displayValue</td>";
            echo "<td style='color:$statusColor;'>$status</td>";
            echo "</tr>";
        }
    }
}

echo "</table>";

echo "<hr>";

echo "<h2>🎯 Summary</h2>";

$issues = [];

// Check APP_KEY
if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches)) {
    $appKey = trim($matches[1]);
    if (empty($appKey) || $appKey === 'base64:' || strlen($appKey) < 20) {
        $issues[] = "APP_KEY is empty or invalid";
    }
} else {
    $issues[] = "APP_KEY not found in .env";
}

// Check DB_HOST
if (preg_match('/^DB_HOST=(.*)$/m', $envContent, $matches)) {
    $dbHost = trim($matches[1]);
    if ($dbHost === '127.0.0.1') {
        $issues[] = "DB_HOST should be 'localhost' not '127.0.0.1'";
    }
}

// Check APP_ENV
if (preg_match('/^APP_ENV=(.*)$/m', $envContent, $matches)) {
    $appEnv = trim($matches[1]);
    if ($appEnv === 'local') {
        $issues[] = "APP_ENV should be 'production' for live server";
    }
}

// Check APP_DEBUG
if (preg_match('/^APP_DEBUG=(.*)$/m', $envContent, $matches)) {
    $appDebug = trim($matches[1]);
    if ($appDebug === 'true') {
        $issues[] = "APP_DEBUG should be 'false' for production";
    }
}

if (empty($issues)) {
    echo "<p style='color:green; font-size:18px;'><strong>✓✓✓ All settings look good!</strong></p>";
    echo "<p>Next step: Access <a href='fix-env-now.php'>fix-env-now.php</a> to test Laravel load</p>";
} else {
    echo "<p style='color:red; font-size:18px;'><strong>❌ Issues Found:</strong></p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li style='color:red;'>$issue</li>";
    }
    echo "</ul>";
    
    echo "<p><strong>FIX NOW:</strong> Access <a href='fix-env-now.php'>fix-env-now.php</a> to auto-fix these issues</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH DEBUG!</strong></p>";
?>
