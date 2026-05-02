<?php

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FIXED ASSETS TABLE STRUCTURE ===\n\n";
    
    $stmt = $pdo->query("DESCRIBE fixed_assets");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-30s %-20s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    
    $stmt = $pdo->query("SELECT * FROM fixed_assets LIMIT 1");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sample) {
        foreach ($sample as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No data found\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}