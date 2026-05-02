<?php
// Test Database Connection
// Akses: https://hmtourtravel.com/test-database.php

echo "<h1>🗄️ Test Database Connection</h1>";

$laravelPath = __DIR__ . '/../laravel_app';
$envPath = $laravelPath . '/.env';

if (!file_exists($envPath)) {
    die("❌ .env file not found!");
}

// Parse .env file
$envContent = file_get_contents($envPath);
$envVars = [];

foreach (explode("\n", $envContent) as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value);
    }
}

echo "<h2>Database Settings from .env:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr><th>Key</th><th>Value</th></tr>";
echo "<tr><td>DB_HOST</td><td>" . ($envVars['DB_HOST'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>DB_PORT</td><td>" . ($envVars['DB_PORT'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>DB_DATABASE</td><td>" . ($envVars['DB_DATABASE'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>DB_USERNAME</td><td>" . ($envVars['DB_USERNAME'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>DB_PASSWORD</td><td>" . (isset($envVars['DB_PASSWORD']) && !empty($envVars['DB_PASSWORD']) ? '[HIDDEN - Length: ' . strlen($envVars['DB_PASSWORD']) . ']' : 'NOT SET') . "</td></tr>";
echo "</table>";

echo "<br><h2>Test Connection:</h2>";

$host = $envVars['DB_HOST'] ?? 'localhost';
$port = $envVars['DB_PORT'] ?? '3306';
$database = $envVars['DB_DATABASE'] ?? '';
$username = $envVars['DB_USERNAME'] ?? '';
$password = $envVars['DB_PASSWORD'] ?? '';

if (empty($database) || empty($username)) {
    echo "<p style='color:red;'>❌ Database credentials not set in .env</p>";
    die();
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color:green; font-size:18px;'><strong>✓✓✓ DATABASE CONNECTION SUCCESSFUL!</strong></p>";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: <strong>$version</strong><br>";
    
    // Get database name
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Connected to: <strong>$dbName</strong><br>";
    
    // Count tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Number of tables: <strong>" . count($tables) . "</strong><br>";
    
    if (count($tables) > 0) {
        echo "<br><h3>Tables in database:</h3>";
        echo "<ul style='columns:3; font-size:12px;'>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check if migrations table exists
        if (in_array('migrations', $tables)) {
            $migrationCount = $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
            echo "<br><p>✓ Migrations table exists with <strong>$migrationCount</strong> migrations</p>";
        } else {
            echo "<br><p style='color:orange;'>⚠️ Migrations table NOT found. You need to run: <code>php artisan migrate --force</code></p>";
        }
        
        // Check if users table exists
        if (in_array('users', $tables)) {
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "<p>✓ Users table exists with <strong>$userCount</strong> users</p>";
        } else {
            echo "<p style='color:orange;'>⚠️ Users table NOT found. Run migrations!</p>";
        }
    } else {
        echo "<br><p style='color:orange;'><strong>⚠️ Database is EMPTY!</strong></p>";
        echo "<p>You need to run migrations:</p>";
        echo "<pre style='background:#f5f5f5; padding:10px;'>";
        echo "ssh u127727849@hmtourtravel.com\n";
        echo "cd /home/u127727849/domains/hmtourtravel.com/laravel_app\n";
        echo "php artisan migrate --force\n";
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red; font-size:18px;'><strong>❌ DATABASE CONNECTION FAILED!</strong></p>";
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    
    echo "<br><h3>Common Issues:</h3>";
    echo "<ul>";
    echo "<li><strong>Access denied:</strong> Wrong username or password</li>";
    echo "<li><strong>Unknown database:</strong> Database doesn't exist in phpMyAdmin</li>";
    echo "<li><strong>Can't connect:</strong> Wrong host (use 'localhost' not '127.0.0.1')</li>";
    echo "</ul>";
    
    echo "<br><h3>Fix Steps:</h3>";
    echo "<ol>";
    echo "<li>Login to Hostinger phpMyAdmin</li>";
    echo "<li>Check if database '<strong>$database</strong>' exists</li>";
    echo "<li>Check if user '<strong>$username</strong>' has access to that database</li>";
    echo "<li>Make sure DB_HOST in .env is '<strong>localhost</strong>' not '127.0.0.1'</li>";
    echo "</ol>";
}

echo "<br><hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH DEBUG!</strong></p>";
?>
