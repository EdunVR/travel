<?php

echo "=== Checking Payroll Table Structure ===\n";

try {
    // Check if payroll table exists
    $tables = DB::select("SHOW TABLES LIKE 'payroll%'");
    
    if (empty($tables)) {
        echo "⚠ No payroll tables found\n";
        
        // Check for alternative table names
        $allTables = DB::select("SHOW TABLES");
        $payrollRelated = [];
        
        foreach ($allTables as $table) {
            $tableName = array_values((array)$table)[0];
            if (stripos($tableName, 'payroll') !== false || 
                stripos($tableName, 'gaji') !== false || 
                stripos($tableName, 'salary') !== false) {
                $payrollRelated[] = $tableName;
            }
        }
        
        if (!empty($payrollRelated)) {
            echo "Found payroll-related tables:\n";
            foreach ($payrollRelated as $table) {
                echo "- $table\n";
            }
        }
    } else {
        echo "Found payroll tables:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- $tableName\n";
            
            // Get table structure
            $columns = DB::select("DESCRIBE $tableName");
            echo "  Columns:\n";
            foreach ($columns as $column) {
                echo "    - {$column->Field} ({$column->Type})\n";
            }
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check Payroll model
$modelPath = 'app/Models/Payroll.php';
if (file_exists($modelPath)) {
    echo "✓ Payroll model exists\n";
    
    $content = file_get_contents($modelPath);
    
    // Check for table name
    if (preg_match('/protected \$table = [\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        echo "Model uses table: {$matches[1]}\n";
    } else {
        echo "Model uses default table name: payrolls\n";
    }
    
    // Check for fillable fields
    if (preg_match('/protected \$fillable = \[(.*?)\]/s', $content, $matches)) {
        echo "Fillable fields found\n";
    }
    
} else {
    echo "✗ Payroll model not found\n";
}

echo "\n=== Payroll Table Check Complete ===\n";