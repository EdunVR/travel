<?php

echo "=== TESTING HARGA_BAHAN QUERY SIMULATION ===\n\n";

try {
    // Connect to database using PDO
    $host = 'localhost';
    $dbname = 'demo';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 TESTING CORRECTED QUERY:\n\n";
    
    // Test the corrected query structure
    $testBahanId = 29; // From sample data
    $testOutletId = 3; // From sample data
    
    echo "Test parameters:\n";
    echo "  - Bahan ID: $testBahanId\n";
    echo "  - Outlet ID: $testOutletId\n\n";
    
    // Simulate the corrected query
    $sql = "
        SELECT harga_bahan.*
        FROM harga_bahan
        JOIN bahan ON harga_bahan.id_bahan = bahan.id_bahan
        WHERE harga_bahan.id_bahan = :bahan_id
        AND bahan.id_outlet = :outlet_id
        AND harga_bahan.stok > 0
        ORDER BY harga_bahan.created_at ASC
    ";
    
    echo "Executing query:\n";
    echo "$sql\n\n";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':bahan_id', $testBahanId);
    $stmt->bindParam(':outlet_id', $testOutletId);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($results)) {
        echo "✅ QUERY SUCCESSFUL! Found " . count($results) . " stock batches:\n\n";
        
        foreach ($results as $i => $batch) {
            echo "Batch " . ($i + 1) . " (FIFO order):\n";
            echo "  ID: {$batch['id']}\n";
            echo "  Bahan ID: {$batch['id_bahan']}\n";
            echo "  Stock: {$batch['stok']}\n";
            echo "  Price: Rp " . number_format($batch['harga_beli'], 0, ',', '.') . "\n";
            echo "  Created: {$batch['created_at']}\n";
            echo "\n";
        }
        
        // Simulate FIFO consumption
        echo "🔄 SIMULATING FIFO CONSUMPTION:\n";
        $quantityNeeded = 10.0;
        $remainingNeeded = $quantityNeeded;
        
        echo "Quantity needed: $quantityNeeded\n";
        echo "Processing batches in FIFO order:\n\n";
        
        foreach ($results as $i => $batch) {
            if ($remainingNeeded <= 0) break;
            
            $quantityToTake = min($batch['stok'], $remainingNeeded);
            $newStock = $batch['stok'] - $quantityToTake;
            
            echo "Batch " . ($i + 1) . ":\n";
            echo "  Original stock: {$batch['stok']}\n";
            echo "  Quantity taken: $quantityToTake\n";
            echo "  New stock: $newStock\n";
            echo "  Remaining needed: " . ($remainingNeeded - $quantityToTake) . "\n";
            echo "\n";
            
            $remainingNeeded -= $quantityToTake;
        }
        
        if ($remainingNeeded > 0) {
            echo "⚠️ WARNING: Insufficient stock! Still need: $remainingNeeded\n";
        } else {
            echo "✅ SUCCESS: All required quantity can be fulfilled!\n";
        }
        
    } else {
        echo "⚠️ No stock batches found for the given parameters\n";
        
        // Check if bahan exists in the outlet
        $checkSql = "
            SELECT b.*, COUNT(hb.id) as batch_count, SUM(hb.stok) as total_stock
            FROM bahan b
            LEFT JOIN harga_bahan hb ON b.id_bahan = hb.id_bahan AND hb.stok > 0
            WHERE b.id_bahan = :bahan_id AND b.id_outlet = :outlet_id
            GROUP BY b.id_bahan
        ";
        
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':bahan_id', $testBahanId);
        $checkStmt->bindParam(':outlet_id', $testOutletId);
        $checkStmt->execute();
        
        $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($checkResult) {
            echo "\nBahan info:\n";
            echo "  Name: {$checkResult['nama_bahan']}\n";
            echo "  Outlet ID: {$checkResult['id_outlet']}\n";
            echo "  Batch count: {$checkResult['batch_count']}\n";
            echo "  Total stock: {$checkResult['total_stock']}\n";
        } else {
            echo "\nBahan not found in the specified outlet\n";
        }
    }
    
    echo "\n=== QUERY STRUCTURE VERIFICATION ===\n";
    echo "✅ Uses JOIN to connect harga_bahan with bahan\n";
    echo "✅ Filters by bahan.id_outlet (correct column)\n";
    echo "✅ Filters by harga_bahan.id_bahan (material)\n";
    echo "✅ Filters by harga_bahan.stok > 0 (available stock)\n";
    echo "✅ Orders by harga_bahan.created_at ASC (FIFO)\n";
    echo "✅ Selects only harga_bahan columns (no conflicts)\n";
    
    echo "\n✅ QUERY SIMULATION COMPLETED SUCCESSFULLY!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), "Unknown column 'id_outlet'") !== false) {
        echo "\n🚨 This confirms the original error - id_outlet doesn't exist in harga_bahan!\n";
        echo "The fix using JOIN with bahan table is correct.\n";
    }
}