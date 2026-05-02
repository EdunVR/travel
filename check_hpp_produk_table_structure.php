<?php

echo "=== CHECKING HPP_PRODUK TABLE STRUCTURE ===\n\n";

try {
    // Check if we can connect to database
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=demo", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check if hpp_produk table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'hpp_produk'");
    if ($stmt->rowCount() == 0) {
        echo "❌ hpp_produk table does not exist!\n";
        exit(1);
    }
    
    echo "✅ hpp_produk table exists\n\n";
    
    // Get table structure
    echo "📋 HPP_PRODUK TABLE STRUCTURE:\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE hpp_produk");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %-10s %-10s %-10s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    echo "\n";
    
    // Check for specific columns that the controller expects
    $expectedColumns = ['id', 'id_produk', 'stok', 'hpp', 'keterangan', 'created_at', 'updated_at'];
    $actualColumns = array_column($columns, 'Field');
    
    echo "🔍 COLUMN VALIDATION:\n";
    echo "=" . str_repeat("=", 30) . "\n";
    
    foreach ($expectedColumns as $expectedCol) {
        if (in_array($expectedCol, $actualColumns)) {
            echo "✅ $expectedCol - EXISTS\n";
        } else {
            echo "❌ $expectedCol - MISSING\n";
        }
    }
    
    echo "\n";
    
    // Check sample data
    echo "📊 SAMPLE DATA (first 5 records):\n";
    echo "=" . str_repeat("=", 40) . "\n";
    
    $stmt = $pdo->query("SELECT * FROM hpp_produk LIMIT 5");
    $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sampleData)) {
        echo "No data found in hpp_produk table\n";
    } else {
        // Print headers
        $headers = array_keys($sampleData[0]);
        echo implode(" | ", array_map(function($h) { return str_pad($h, 12); }, $headers)) . "\n";
        echo str_repeat("-", count($headers) * 15) . "\n";
        
        // Print data
        foreach ($sampleData as $row) {
            echo implode(" | ", array_map(function($v) { 
                return str_pad(substr($v ?? 'NULL', 0, 12), 12); 
            }, $row)) . "\n";
        }
    }
    
    echo "\n";
    
    // Check if there are any records with the ID from the error (31148)
    echo "🔍 CHECKING RECORD ID 31148 (from error):\n";
    echo "=" . str_repeat("=", 40) . "\n";
    
    $stmt = $pdo->prepare("SELECT * FROM hpp_produk WHERE id = ?");
    $stmt->execute([31148]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($record) {
        echo "✅ Record found:\n";
        foreach ($record as $key => $value) {
            echo "   $key: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "❌ Record with ID 31148 not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

?>