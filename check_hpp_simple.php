<?php

// Simple database check without Laravel facades
$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING HPP_PRODUK TABLE STRUCTURE ===\n\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'hpp_produk'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "hpp_produk table does not exist\n";
        
        // Look for alternative tables
        echo "\nLooking for HPP-related tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if (stripos($row[0], 'hpp') !== false || stripos($row[0], 'cost') !== false) {
                echo "Found: {$row[0]}\n";
            }
        }
        exit;
    }
    
    echo "hpp_produk table exists\n\n";
    
    // Show table structure
    echo "Table structure:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM hpp_produk");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    // Show sample data
    echo "\nSample data:\n";
    $stmt = $pdo->query("SELECT * FROM hpp_produk LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode($row) . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}