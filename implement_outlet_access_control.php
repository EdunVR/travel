<?php

/**
 * Script untuk mengimplementasikan Outlet Access Control
 * Menambahkan HasOutletFilter trait ke controllers yang membutuhkan
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== OUTLET ACCESS CONTROL IMPLEMENTATION ===\n\n";

// Daftar controllers yang perlu diimplementasikan
$controllers = [
    // Priority 1: Finance Module
    'app/Http/Controllers/FinanceAccountantController.php',
    'app/Http/Controllers/FinanceDashboardController.php',
    'app/Http/Controllers/BankReconciliationController.php',
    'app/Http/Controllers/CompanyBankAccountController.php',
    
    // Priority 2: Dashboard & Reports
    'app/Http/Controllers/AdminDashboardController.php',
    'app/Http/Controllers/CrmDashboardController.php',
    'app/Http/Controllers/SalesDashboardController.php',
    'app/Http/Controllers/MarginReportController.php',
    'app/Http/Controllers/SalesReportController.php',
    
    // Priority 3: Sales & Purchase
    'app/Http/Controllers/PosController.php',
    'app/Http/Controllers/SalesManagementController.php',
    'app/Http/Controllers/PurchaseManagementController.php',
    
    // Priority 4: Inventory & Production
    'app/Http/Controllers/InventoriController.php',
    'app/Http/Controllers/TransferGudangController.php',
    
    // Priority 5: Service & CRM
    'app/Http/Controllers/ServiceController.php',
    'app/Http/Controllers/CustomerTypeController.php',
    
    // Priority 6: SDM
    'app/Http/Controllers/AttendanceManagementController.php',
    
    // Priority 7: Others
    'app/Http/Controllers/UserManagementController.php',
    'app/Http/Controllers/ChatController.php',
];

$implemented = 0;
$skipped = 0;
$errors = 0;

foreach ($controllers as $controllerPath) {
    echo "Processing: $controllerPath\n";
    
    if (!file_exists($controllerPath)) {
        echo "  ✗ File not found\n";
        $errors++;
        continue;
    }
    
    $content = file_get_contents($controllerPath);
    
    // Check if already has trait
    if (strpos($content, 'use HasOutletFilter;') !== false) {
        echo "  ✓ Already has HasOutletFilter trait\n";
        $skipped++;
        continue;
    }
    
    // Add trait after class declaration
    if (preg_match('/class\s+\w+\s+extends\s+Controller\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPosition = $matches[0][1] + strlen($matches[0][0]);
        
        $traitCode = "\n    use \\App\\Traits\\HasOutletFilter;\n";
        
        $newContent = substr_replace($content, $traitCode, $insertPosition, 0);
        
        // Add use statement at top if not exists
        if (strpos($newContent, 'use App\\Traits\\HasOutletFilter;') === false) {
            // Find position after namespace
            if (preg_match('/namespace\s+[^;]+;/', $newContent, $nsMatches, PREG_OFFSET_CAPTURE)) {
                $nsPosition = $nsMatches[0][1] + strlen($nsMatches[0][0]);
                $useStatement = "\n\nuse App\\Traits\\HasOutletFilter;";
                $newContent = substr_replace($newContent, $useStatement, $nsPosition, 0);
            }
        }
        
        file_put_contents($controllerPath, $newContent);
        echo "  ✓ HasOutletFilter trait added\n";
        $implemented++;
    } else {
        echo "  ✗ Could not find class declaration\n";
        $errors++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Implemented: $implemented\n";
echo "Skipped (already has trait): $skipped\n";
echo "Errors: $errors\n";
echo "Total: " . count($controllers) . "\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Review each controller's methods\n";
echo "2. Update getOutlets() methods to use getAccessibleOutlets()\n";
echo "3. Update queries to filter by accessible outlets\n";
echo "4. Test with different user roles\n";

echo "\n=== DONE ===\n";
