<?php

require_once 'vendor/autoload.php';

// Check actual journal_entries table structure
echo "=== CHECKING JOURNAL_ENTRIES TABLE STRUCTURE ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=demo', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Describing journal_entries table:\n";
    $stmt = $pdo->query("DESCRIBE journal_entries");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Available columns:\n";
    foreach ($columns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : '';
        echo "     - {$column['Field']} ({$column['Type']}) {$nullable} {$default}\n";
    }
    
    echo "\n2. Checking for specific fields:\n";
    $requiredFields = [
        'id', 'book_id', 'reference_number', 'description', 'transaction_date',
        'outlet_id', 'source_type', 'source_id', 'transaction_number',
        'reference_type', 'status', 'total_debit', 'total_credit', 'created_by'
    ];
    
    $existingFields = array_column($columns, 'Field');
    
    foreach ($requiredFields as $field) {
        if (in_array($field, $existingFields)) {
            echo "     ✓ {$field} EXISTS\n";
        } else {
            echo "     ✗ {$field} MISSING\n";
        }
    }
    
    echo "\n3. Describing journal_entry_details table:\n";
    $stmt = $pdo->query("DESCRIBE journal_entry_details");
    $detailColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Available columns:\n";
    foreach ($detailColumns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : '';
        echo "     - {$column['Field']} ({$column['Type']}) {$nullable} {$default}\n";
    }
    
    echo "\n4. Sample data from journal_entries (if any):\n";
    $stmt = $pdo->query("SELECT * FROM journal_entries LIMIT 3");
    $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($sampleData)) {
        echo "   Found " . count($sampleData) . " sample records:\n";
        foreach ($sampleData as $index => $record) {
            echo "     Record " . ($index + 1) . ":\n";
            foreach ($record as $field => $value) {
                $displayValue = $value !== null ? $value : 'NULL';
                echo "       {$field}: {$displayValue}\n";
            }
            echo "\n";
        }
    } else {
        echo "   No records found in journal_entries table\n";
    }
    
} catch (PDOException $e) {
    echo "   ✗ Database connection error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";