<?php

/**
 * Script untuk update getOutlets() methods
 * Mengganti Outlet::active() dengan $this->getAccessibleOutlets()
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATE GET OUTLETS METHODS ===\n\n";

// Daftar controllers yang perlu diupdate
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
];

$updated = 0;
$skipped = 0;

foreach ($controllers as $controllerPath) {
    echo "Processing: $controllerPath\n";
    
    if (!file_exists($controllerPath)) {
        echo "  ✗ File not found\n";
        continue;
    }
    
    $content = file_get_contents($controllerPath);
    $originalContent = $content;
    
    // Pattern 1: Replace Outlet::active()->orderBy()->get()
    $content = preg_replace(
        '/Outlet::active\(\)\s*->orderBy\([^)]+\)\s*->get\(\[([^\]]*)\]\)/',
        '$this->getAccessibleOutlets()',
        $content
    );
    
    // Pattern 2: Replace Outlet::active()->get()
    $content = preg_replace(
        '/Outlet::active\(\)\s*->get\(\[([^\]]*)\]\)/',
        '$this->getAccessibleOutlets()',
        $content
    );
    
    // Pattern 3: Replace Outlet::all()
    $content = preg_replace(
        '/Outlet::all\(\)/',
        '$this->getAccessibleOutlets()',
        $content
    );
    
    // Pattern 4: Replace $outlets = Outlet::...
    $content = preg_replace(
        '/\$outlets\s*=\s*Outlet::(active|all)\(\)[^;]*;/',
        '$outlets = $this->getAccessibleOutlets();',
        $content
    );
    
    if ($content !== $originalContent) {
        file_put_contents($controllerPath, $content);
        echo "  ✓ getOutlets() method updated\n";
        $updated++;
    } else {
        echo "  - No changes needed\n";
        $skipped++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Updated: $updated\n";
echo "Skipped: $skipped\n";
echo "Total: " . count($controllers) . "\n";

echo "\n=== DONE ===\n";
