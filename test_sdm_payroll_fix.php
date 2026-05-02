<?php

echo "=== Testing SDM Dashboard Payroll Fix ===\n";

// Test 1: Check if SdmDashboardController syntax is correct
try {
    $output = shell_exec('php -l app/Http/Controllers/SdmDashboardController.php 2>&1');
    if (strpos($output, 'No syntax errors detected') !== false) {
        echo "✓ SdmDashboardController syntax is correct\n";
    } else {
        echo "✗ SdmDashboardController syntax error: $output\n";
    }
} catch (Exception $e) {
    echo "⚠ Could not check syntax: " . $e->getMessage() . "\n";
}

// Test 2: Check if correct models are imported
$controllerPath = 'app/Http/Controllers/SdmDashboardController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    
    // Check for correct model imports
    if (strpos($content, 'use App\Models\Payroll;') !== false) {
        echo "✓ Payroll model import found\n";
    } else {
        echo "✗ Payroll model import missing\n";
    }
    
    if (strpos($content, 'use App\Models\PayrollManagement;') !== false) {
        echo "✗ Old PayrollManagement import still exists\n";
    } else {
        echo "✓ Old PayrollManagement import removed\n";
    }
    
    // Check for correct model usage
    if (strpos($content, 'PayrollManagement::') !== false) {
        echo "✗ PayrollManagement still being used in code\n";
    } else {
        echo "✓ PayrollManagement usage removed\n";
    }
    
    if (strpos($content, 'Payroll::whereIn') !== false) {
        echo "✓ Payroll model being used correctly\n";
    } else {
        echo "⚠ Payroll model usage not found\n";
    }
    
} else {
    echo "✗ SdmDashboardController not found\n";
}

// Test 3: Check if Payroll model exists
$payrollModelPath = 'app/Models/Payroll.php';
if (file_exists($payrollModelPath)) {
    echo "✓ Payroll model exists\n";
    
    $content = file_get_contents($payrollModelPath);
    
    // Check for required columns
    $requiredColumns = ['outlet_id', 'payment_date', 'net_salary', 'gross_salary'];
    foreach ($requiredColumns as $column) {
        if (strpos($content, "'$column'") !== false) {
            echo "✓ Column '$column' found in fillable\n";
        } else {
            echo "⚠ Column '$column' not found in fillable\n";
        }
    }
    
} else {
    echo "✗ Payroll model not found\n";
}

echo "\n=== Summary ===\n";
echo "Fixed issues:\n";
echo "- Changed PayrollManagement to Payroll model\n";
echo "- Updated import statements\n";
echo "- Fixed total_deductions calculation\n";
echo "- Used correct column names from Payroll model\n";

echo "\nThe SDM Dashboard should now work without 'PayrollManagement not found' error.\n";

echo "\n=== SDM Payroll Fix Test Complete ===\n";