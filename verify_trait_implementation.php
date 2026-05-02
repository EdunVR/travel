<?php

/**
 * Verify HasOutletFilter trait implementation
 */

echo "=== VERIFY TRAIT IMPLEMENTATION ===\n\n";

$controllers = [
    'app/Http/Controllers/FinanceAccountantController.php',
    'app/Http/Controllers/FinanceDashboardController.php',
    'app/Http/Controllers/BankReconciliationController.php',
    'app/Http/Controllers/CompanyBankAccountController.php',
    'app/Http/Controllers/AdminDashboardController.php',
    'app/Http/Controllers/CrmDashboardController.php',
    'app/Http/Controllers/SalesDashboardController.php',
    'app/Http/Controllers/MarginReportController.php',
    'app/Http/Controllers/SalesReportController.php',
    'app/Http/Controllers/PosController.php',
    'app/Http/Controllers/SalesManagementController.php',
    'app/Http/Controllers/PurchaseManagementController.php',
    'app/Http/Controllers/InventoriController.php',
    'app/Http/Controllers/TransferGudangController.php',
    'app/Http/Controllers/ServiceController.php',
    'app/Http/Controllers/CustomerTypeController.php',
    'app/Http/Controllers/AttendanceManagementController.php',
    'app/Http/Controllers/UserManagementController.php',
    'app/Http/Controllers/ChatController.php',
];

$success = 0;
$failed = 0;

foreach ($controllers as $controller) {
    $name = basename($controller, '.php');
    
    if (!file_exists($controller)) {
        echo "✗ $name - File not found\n";
        $failed++;
        continue;
    }
    
    $content = file_get_contents($controller);
    
    // Check for trait usage (either format)
    $hasTrait = (
        strpos($content, 'use \App\Traits\HasOutletFilter;') !== false ||
        strpos($content, 'use App\Traits\HasOutletFilter;') !== false ||
        preg_match('/use\s+.*HasOutletFilter/', $content)
    );
    
    if ($hasTrait) {
        echo "✓ $name\n";
        $success++;
    } else {
        echo "✗ $name - Trait not found\n";
        $failed++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Success: $success/" . count($controllers) . "\n";
echo "Failed: $failed/" . count($controllers) . "\n";

if ($failed === 0) {
    echo "\n✅ ALL CONTROLLERS HAVE TRAIT IMPLEMENTED!\n";
} else {
    echo "\n⚠️ Some controllers still need trait implementation\n";
}

echo "\n=== DONE ===\n";
