<?php

// Simple test untuk cek database structure
$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING FIXED ASSET DRAFT STATUS ===\n\n";
    
    // Test 1: Check table structure
    echo "1. Checking fixed_assets table structure...\n";
    
    $stmt = $pdo->query("DESCRIBE fixed_assets");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $foundColumns = [];
    foreach ($columns as $column) {
        $foundColumns[] = $column['Field'];
        if (in_array($column['Field'], ['status', 'activated_at', 'activated_by'])) {
            echo "   ✓ Found column: " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    }
    
    // Check if required columns exist
    $requiredColumns = ['status', 'activated_at', 'activated_by'];
    $missingColumns = array_diff($requiredColumns, $foundColumns);
    
    if (empty($missingColumns)) {
        echo "   ✓ All required columns exist\n";
    } else {
        echo "   ✗ Missing columns: " . implode(', ', $missingColumns) . "\n";
    }
    
    // Test 2: Check current data
    echo "\n2. Checking existing data...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM fixed_assets");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM fixed_assets GROUP BY status");
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Total fixed assets: $total\n";
    foreach ($statusCounts as $status) {
        echo "   Status '{$status['status']}': {$status['count']} assets\n";
    }
    
    // Test 3: Test insert draft asset
    echo "\n3. Testing draft asset creation...\n";
    
    $testData = [
        'outlet_id' => 1,
        'book_id' => 1,
        'code' => 'TEST-DRAFT-' . time(),
        'name' => 'Test Draft Asset',
        'category' => 'equipment',
        'location' => 'Test Location',
        'acquisition_date' => date('Y-m-d'),
        'acquisition_cost' => 1000000,
        'salvage_value' => 100000,
        'useful_life' => 5,
        'depreciation_method' => 'straight_line',
        'asset_account_id' => 20,
        'depreciation_expense_account_id' => 49,
        'accumulated_depreciation_account_id' => 24,
        'payment_account_id' => 5,
        'accumulated_depreciation' => 0,
        'book_value' => 1000000,
        'status' => 'draft',
        'created_by' => 2,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $columns = implode(', ', array_keys($testData));
    $placeholders = ':' . implode(', :', array_keys($testData));
    
    $sql = "INSERT INTO fixed_assets ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute($testData)) {
        $insertId = $pdo->lastInsertId();
        echo "   ✓ Successfully created draft asset with ID: $insertId\n";
        
        // Verify the asset was created with draft status
        $stmt = $pdo->prepare("SELECT status FROM fixed_assets WHERE id = ?");
        $stmt->execute([$insertId]);
        $status = $stmt->fetch(PDO::FETCH_ASSOC)['status'];
        
        if ($status === 'draft') {
            echo "   ✓ Asset created with draft status\n";
        } else {
            echo "   ✗ Asset created with status: $status (expected: draft)\n";
        }
        
        // Clean up test data
        $stmt = $pdo->prepare("DELETE FROM fixed_assets WHERE id = ?");
        $stmt->execute([$insertId]);
        echo "   ✓ Test data cleaned up\n";
        
    } else {
        echo "   ✗ Failed to create test asset\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "Database structure is ready for draft status implementation!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}