<?php

echo "=== CHECKING HARGA_BAHAN TABLE STRUCTURE ===\n\n";

try {
    // Connect to database using PDO
    $host = 'localhost';
    $dbname = 'demo';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table structure
    echo "HARGA_BAHAN TABLE COLUMNS:\n";
    $stmt = $pdo->query("DESCRIBE harga_bahan");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']} - {$column['Default']}\n";
    }
    
    // Sample data
    echo "\nSAMPLE DATA (first 3 records):\n";
    $stmt = $pdo->query("SELECT * FROM harga_bahan LIMIT 3");
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
    
    // Check relationship with bahan table
    echo "CHECKING RELATIONSHIP WITH BAHAN TABLE:\n";
    $stmt = $pdo->query("
        SELECT hb.*, b.nama_bahan, b.id_outlet as bahan_outlet
        FROM harga_bahan hb 
        LEFT JOIN bahan b ON hb.id_bahan = b.id_bahan 
        LIMIT 3
    ");
    $relationships = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($relationships)) {
        foreach ($relationships as $i => $rel) {
            echo "Relationship " . ($i + 1) . ":\n";
            echo "  harga_bahan.id_bahan: {$rel['id_bahan']}\n";
            echo "  bahan.nama_bahan: {$rel['nama_bahan']}\n";
            echo "  bahan.id_outlet: {$rel['bahan_outlet']}\n";
            echo "  harga_bahan.stok: {$rel['stok']}\n";
            echo "\n";
        }
    }
    
    echo "✅ TABLE STRUCTURE CHECK COMPLETED\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}