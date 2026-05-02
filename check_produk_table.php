<?php

// Simple database check for produk table
$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING PRODUK TABLE STRUCTURE ===\n\n";
    
    // Show table structure
    echo "Table structure:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM produk");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    // Check for markup column
    echo "\nLooking for markup-related columns:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM produk");
    $markupFound = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (stripos($row['Field'], 'markup') !== false) {
            echo "Found markup column: {$row['Field']}\n";
            $markupFound = true;
        }
    }
    
    if (!$markupFound) {
        echo "No markup column found in produk table\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}