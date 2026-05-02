<?php
/**
 * Database Connection Tester
 * Upload ke public_html dan akses via browser
 * 
 * ⚠️ HAPUS FILE INI SETELAH SELESAI!
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #28a745; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #0056b3; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type='text'], input[type='password'] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class='container'>";

echo "<h1>🔍 Database Connection Tester</h1>";

// Default values
$host = 'localhost';
$database = 'u127727849_morra';
$username = 'u127727849_morra';
$password = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $database = $_POST['database'] ?? 'u127727849_morra';
    $username = $_POST['username'] ?? 'u127727849_morra';
    $password = $_POST['password'] ?? '';
    
    echo "<div class='info'>";
    echo "<strong>Testing Connection:</strong><br>";
    echo "Host: <code>$host</code><br>";
    echo "Database: <code>$database</code><br>";
    echo "Username: <code>$username</code><br>";
    echo "Password: <code>" . (strlen($password) > 0 ? str_repeat('*', strlen($password)) : '[empty]') . "</code>";
    echo "</div>";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>";
        echo "✅ <strong>CONNECTION SUCCESSFUL!</strong><br><br>";
        echo "Database connection is working correctly!<br><br>";
        echo "<strong>Use these settings in your .env file:</strong><br>";
        echo "<code>DB_HOST=$host</code><br>";
        echo "<code>DB_DATABASE=$database</code><br>";
        echo "<code>DB_USERNAME=$username</code><br>";
        echo "<code>DB_PASSWORD=$password</code><br>";
        echo "</div>";
        
        // Get MySQL version
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "<div class='info'>";
        echo "<strong>MySQL Version:</strong> $version<br>";
        echo "</div>";
        
        // Count tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tableCount = count($tables);
        
        echo "<div class='info'>";
        echo "<strong>Number of tables:</strong> $tableCount<br>";
        if ($tableCount > 0) {
            echo "<br><strong>Tables found:</strong><br>";
            echo "<ul>";
            foreach (array_slice($tables, 0, 10) as $table) {
                echo "<li>$table</li>";
            }
            if ($tableCount > 10) {
                echo "<li>... and " . ($tableCount - 10) . " more tables</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
        
        echo "<div class='warning'>";
        echo "⚠️ <strong>NEXT STEPS:</strong><br>";
        echo "1. Update your <code>.env</code> file with the correct password<br>";
        echo "2. Run: <code>php artisan config:clear</code><br>";
        echo "3. Run: <code>php artisan migrate --force</code><br>";
        echo "4. <strong>DELETE THIS FILE!</strong> (test-db-connection.php)";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>";
        echo "❌ <strong>CONNECTION FAILED!</strong><br><br>";
        echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
        
        if (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "<strong>Possible causes:</strong><br>";
            echo "• Wrong password<br>";
            echo "• User doesn't have access to this database<br>";
            echo "• User doesn't exist<br><br>";
            echo "<strong>Solutions:</strong><br>";
            echo "1. Check password in Hostinger Control Panel → MySQL Databases<br>";
            echo "2. Reset password for user <code>$username</code><br>";
            echo "3. Make sure user has access to database <code>$database</code>";
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "<strong>Database <code>$database</code> doesn't exist!</strong><br><br>";
            echo "Create the database first in phpMyAdmin or Hostinger Control Panel.";
        } else {
            echo "<strong>Check your database settings.</strong>";
        }
        echo "</div>";
    }
    
    echo "<hr style='margin: 20px 0;'>";
}

// Show form
echo "<form method='POST'>";
echo "<div class='form-group'>";
echo "<label>Database Host:</label>";
echo "<input type='text' name='host' value='" . htmlspecialchars($host) . "' required>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label>Database Name:</label>";
echo "<input type='text' name='database' value='" . htmlspecialchars($database) . "' required>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label>Database Username:</label>";
echo "<input type='text' name='username' value='" . htmlspecialchars($username) . "' required>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label>Database Password:</label>";
echo "<input type='password' name='password' value='" . htmlspecialchars($password) . "' placeholder='Enter database password'>";
echo "<small style='color: #666;'>Leave empty if no password</small>";
echo "</div>";

echo "<button type='submit'>🔍 Test Connection</button>";
echo "</form>";

echo "<div class='warning' style='margin-top: 20px;'>";
echo "⚠️ <strong>SECURITY WARNING:</strong><br>";
echo "This file is for testing only. <strong>DELETE IT</strong> after you find the correct password!<br>";
echo "File location: <code>/home/u127727849/domains/hmtourtravel.com/public_html/test-db-connection.php</code>";
echo "</div>";

echo "<div class='info' style='margin-top: 20px;'>";
echo "<strong>💡 Tips:</strong><br>";
echo "• Check password in Hostinger Control Panel → MySQL Databases<br>";
echo "• Or check in phpMyAdmin → User accounts<br>";
echo "• Make sure DB_HOST is <code>localhost</code> not <code>127.0.0.1</code><br>";
echo "• Password is case-sensitive!";
echo "</div>";

echo "</div></body></html>";
?>
