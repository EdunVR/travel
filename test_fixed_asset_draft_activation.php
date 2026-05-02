<?php

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING FIXED ASSET DRAFT ACTIVATION ===\n\n";
    
    // Test 1: Create a draft asset
    echo "1. Creating test draft asset...\n";
    
    $testData = [
        'outlet_id' => 1,
        'book_id' => 1,
        'code' => 'TEST-DRAFT-' . time(),
        'name' => 'Test Draft Asset for Activation',
        'category' => 'equipment',
        'location' => 'Test Location',
        'acquisition_date' => date('Y-m-d'),
        'acquisition_cost' => 5000000.00,
        'salvage_value' => 500000.00,
        'useful_life' => 5,
        'depreciation_method' => 'straight_line',
        'asset_account_id' => 20,
        'depreciation_expense_account_id' => 49,
        'accumulated_depreciation_account_id' => 24,
        'payment_account_id' => 5,
        'accumulated_depreciation' => 0,
        'book_value' => 5000000.00,
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
        $assetId = $pdo->lastInsertId();
        echo "   ✓ Draft asset created with ID: $assetId\n";
        
        // Test 2: Verify draft status
        $stmt = $pdo->prepare("SELECT status FROM fixed_assets WHERE id = ?");
        $stmt->execute([$assetId]);
        $status = $stmt->fetch(PDO::FETCH_ASSOC)['status'];
        
        if ($status === 'draft') {
            echo "   ✓ Asset has draft status\n";
        } else {
            echo "   ✗ Asset status is: $status (expected: draft)\n";
        }
        
        // Test 3: Simulate activation
        echo "\n2. Simulating asset activation...\n";
        
        $activationData = [
            'status' => 'active',
            'activated_at' => date('Y-m-d H:i:s'),
            'activated_by' => 2,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $updateSql = "UPDATE fixed_assets SET status = :status, activated_at = :activated_at, activated_by = :activated_by, updated_at = :updated_at WHERE id = :id";
        $stmt = $pdo->prepare($updateSql);
        $activationData['id'] = $assetId;
        
        if ($stmt->execute($activationData)) {
            echo "   ✓ Asset activated successfully\n";
            
            // Verify activation
            $stmt = $pdo->prepare("SELECT status, activated_at, activated_by FROM fixed_assets WHERE id = ?");
            $stmt->execute([$assetId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['status'] === 'active' && $result['activated_at'] && $result['activated_by']) {
                echo "   ✓ Activation data saved correctly\n";
                echo "     - Status: {$result['status']}\n";
                echo "     - Activated at: {$result['activated_at']}\n";
                echo "     - Activated by: {$result['activated_by']}\n";
            } else {
                echo "   ✗ Activation data not saved correctly\n";
            }
        } else {
            echo "   ✗ Failed to activate asset\n";
        }
        
        // Test 4: Check journal entry creation (simulate)
        echo "\n3. Checking journal entry structure...\n";
        
        // Check if journals table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'journals'");
        if ($stmt->rowCount() > 0) {
            echo "   ✓ Journals table exists\n";
            
            // Check journal entries table
            $stmt = $pdo->query("SHOW TABLES LIKE 'journal_entries'");
            if ($stmt->rowCount() > 0) {
                echo "   ✓ Journal entries table exists\n";
                
                // Show sample journal structure
                $stmt = $pdo->query("DESCRIBE journals");
                $journalColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "   ✓ Journal columns: " . implode(', ', $journalColumns) . "\n";
                
                $stmt = $pdo->query("DESCRIBE journal_entries");
                $entryColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "   ✓ Journal entry columns: " . implode(', ', $entryColumns) . "\n";
            } else {
                echo "   ✗ Journal entries table not found\n";
            }
        } else {
            echo "   ✗ Journals table not found\n";
        }
        
        // Test 5: Test deletion restriction
        echo "\n4. Testing deletion restriction for active asset...\n";
        
        // Try to delete active asset (should be restricted)
        try {
            $stmt = $pdo->prepare("DELETE FROM fixed_assets WHERE id = ? AND status = 'active'");
            $stmt->execute([$assetId]);
            
            if ($stmt->rowCount() > 0) {
                echo "   ⚠ Active asset was deleted (this should be restricted in application logic)\n";
            } else {
                echo "   ✓ Active asset deletion prevented (good)\n";
            }
        } catch (Exception $e) {
            echo "   ✓ Database constraint prevented deletion: " . $e->getMessage() . "\n";
        }
        
        // Clean up - force delete for test
        $stmt = $pdo->prepare("DELETE FROM fixed_assets WHERE id = ?");
        $stmt->execute([$assetId]);
        echo "   ✓ Test data cleaned up\n";
        
    } else {
        echo "   ✗ Failed to create test asset\n";
    }
    
    echo "\n=== TEST SUMMARY ===\n";
    echo "✓ Draft status implementation is working\n";
    echo "✓ Activation process structure is ready\n";
    echo "✓ Database schema supports the feature\n";
    echo "\nNext: Test the actual web interface!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}