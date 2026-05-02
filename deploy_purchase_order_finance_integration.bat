@echo off
echo ========================================
echo Deploying Purchase Order Finance Integration
echo ========================================
echo.

echo Step 1: Clear application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 2: Optimize application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo Step 3: Check file permissions...
if exist "app\Http\Controllers\PurchaseManagementController.php" (
    echo ✓ PurchaseManagementController.php - Found
) else (
    echo ✗ PurchaseManagementController.php - Missing
)

if exist "app\Http\Controllers\FinanceAccountantController.php" (
    echo ✓ FinanceAccountantController.php - Found
) else (
    echo ✗ FinanceAccountantController.php - Missing
)

if exist "resources\views\admin\pembelian\purchase-order\print-invoice.blade.php" (
    echo ✓ print-invoice.blade.php - Found
) else (
    echo ✗ print-invoice.blade.php - Missing
)

if exist "resources\views\admin\finance\neraca-saldo\pdf.blade.php" (
    echo ✓ neraca-saldo pdf.blade.php - Found
) else (
    echo ✗ neraca-saldo pdf.blade.php - Missing
)

echo.
echo Step 4: Verify HasCompanySettings trait...
php artisan tinker --execute="
try {
    \$controller = new App\Http\Controllers\PurchaseManagementController(new App\Services\JournalEntryService());
    if (method_exists(\$controller, 'getCompanySettingsForPrint')) {
        echo 'HasCompanySettings trait - OK' . PHP_EOL;
    } else {
        echo 'HasCompanySettings trait - MISSING' . PHP_EOL;
    }
    
    \$financeController = new App\Http\Controllers\FinanceAccountantController();
    if (method_exists(\$financeController, 'getCompanySettingsForPrint')) {
        echo 'FinanceAccountantController HasCompanySettings - OK' . PHP_EOL;
    } else {
        echo 'FinanceAccountantController HasCompanySettings - MISSING' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Step 5: Test company settings retrieval...
php artisan tinker --execute="
try {
    \$settings = DB::table('company_settings')->first();
    if (\$settings) {
        echo 'Company settings found in database' . PHP_EOL;
        echo 'Company name: ' . (\$settings->company_name ?? 'Not set') . PHP_EOL;
    } else {
        echo 'No company settings found - using defaults' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error accessing company settings: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo Deployment Summary:
echo ========================================
echo.
echo Changes Applied:
echo ✓ Added company settings to PurchaseManagementController print methods
echo ✓ Updated Purchase Order print templates with company branding
echo ✓ Integrated company settings in Finance PDF exports:
echo   - Trial Balance (Neraca Saldo)
echo   - Profit & Loss Report
echo   - Balance Sheet (Neraca)
echo   - Fixed Assets Report
echo ✓ Added company logo support in all PDF exports
echo.

echo Next Steps:
echo 1. Test Purchase Order print functionality
echo 2. Test Finance report PDF exports
echo 3. Verify company logo and information display
echo 4. Check all print templates for consistency
echo.

echo ========================================
echo Deployment Complete
echo ========================================
pause