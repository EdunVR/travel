<?php

echo "=== CHECKING HPP_PRODUK TABLE STRUCTURE ===\n\n";

try {
    // Connect to database using PDO
    $host = 'localhost';
    $dbname = 'demo';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table structure
    echo "HPP_PRODUK TABLE COLUMNS:\n";
    $stmt = $pdo->query("DESCRIBE hpp_produk");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']} - {$column['Default']}\n";
    }
    
    // Sample data
    echo "\nSAMPLE DATA (first 3 records):\n";
    $stmt = $pdo->query("SELECT * FROM hpp_produk LIMIT 3");
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($samples)) {
        $firstRow = $samples[0];
        echo "Available columns: " . implode(', ', array_keys($firstRow)) . "\n\n";
        
        foreach ($samples as $i => $sample) {
            echo "Record " . ($i + 1) . ":\n";
            foreach ($sample as $key => $value) {
                echo "  $key: $value\n";
            }
            echo "\n";
        }
    } else {
        echo "No sample data found\n";
    }
    
    echo "✅ TABLE STRUCTURE CHECK COMPLETED\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}