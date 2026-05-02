<?php

echo "=== Checking Recruitment Table Structure ===\n";

try {
    // Check if we can connect to database
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=demo', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n";
    
    // Check if recruitments table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'recruitments'");
    if ($stmt->rowCount() > 0) {
        echo "✓ 'recruitments' table exists\n";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE recruitments");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check for outlet-related columns
        $outletColumns = [];
        foreach ($columns as $column) {
            if (strpos(strtolower($column['Field']), 'outlet') !== false) {
                $outletColumns[] = $column['Field'];
            }
        }
        
        if (!empty($outletColumns)) {
            echo "\n✓ Outlet-related columns found: " . implode(', ', $outletColumns) . "\n";
        } else {
            echo "\n⚠ No outlet-related columns found\n";
        }
        
    } else {
        echo "✗ 'recruitments' table does not exist\n";
        
        // Check for alternative table names
        $stmt = $pdo->query("SHOW TABLES LIKE '%recruit%'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($tables)) {
            echo "Found similar tables: " . implode(', ', $tables) . "\n";
        }
    }
    
    // Also check other SDM-related tables
    echo "\n=== Checking Other SDM Tables ===\n";
    
    $sdmTables = ['attendances', 'payroll_managements', 'kontrak_kerjas', 'performance_appraisals'];
    
    foreach ($sdmTables as $tableName) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() > 0) {
            echo "✓ '$tableName' table exists\n";
            
            // Check for outlet columns
            $stmt = $pdo->query("DESCRIBE $tableName");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $outletColumns = [];
            foreach ($columns as $column) {
                if (strpos(strtolower($column['Field']), 'outlet') !== false) {
                    $outletColumns[] = $column['Field'];
                }
            }
            
            if (!empty($outletColumns)) {
                echo "  - Outlet columns: " . implode(', ', $outletColumns) . "\n";
            } else {
                echo "  - No outlet columns found\n";
            }
        } else {
            echo "✗ '$tableName' table does not exist\n";
        }
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration in .env file\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Recruitment Table Structure Check Complete ===\n";