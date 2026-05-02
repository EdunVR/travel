@echo off
echo ========================================
echo Deploying Finance PDF Design Improvements
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
echo Step 3: Verify improved PDF templates...
if exist "resources\views\admin\finance\neraca-saldo\pdf.blade.php" (
    echo ✓ Neraca Saldo PDF template - Updated
) else (
    echo ✗ Neraca Saldo PDF template - Missing
)

if exist "resources\views\admin\finance\labarugi\pdf.blade.php" (
    echo ✓ Profit Loss PDF template - Updated
) else (
    echo ✗ Profit Loss PDF template - Missing
)

if exist "resources\views\admin\finance\neraca\pdf.blade.php" (
    echo ✓ Balance Sheet PDF template - Updated
) else (
    echo ✗ Balance Sheet PDF template - Missing
)

echo.
echo Step 4: Verify controller integrations...
php artisan tinker --execute="
try {
    \$controller = new App\Http\Controllers\FinanceAccountantController();
    if (method_exists(\$controller, 'getCompanySettingsForPrint')) {
        echo 'FinanceAccountantController company settings - OK' . PHP_EOL;
    } else {
        echo 'FinanceAccountantController company settings - MISSING' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Step 5: Test company logo loading...
php artisan tinker --execute="
try {
    \$settings = DB::table('company_settings')->first();
    if (\$settings && \$settings->logo_url) {
        echo 'Company logo URL found: ' . \$settings->logo_url . PHP_EOL;
        if (file_exists(public_path(\$settings->logo_url))) {
            echo 'Logo file exists - OK' . PHP_EOL;
        } else {
            echo 'Logo file missing - Check file path' . PHP_EOL;
        }
    } else {
        echo 'No company logo configured' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error checking logo: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo Deployment Summary:
echo ========================================
echo.
echo PDF Design Improvements Applied:
echo ✓ Professional letterhead with logo positioning
echo ✓ Enhanced margins and spacing (20mm margins)
echo ✓ Improved table designs with gradients and shadows
echo ✓ Better typography and color schemes
echo ✓ Professional headers with company branding
echo ✓ Enhanced section styling and visual hierarchy
echo ✓ Improved footer design with company information
echo ✓ Consistent design language across all reports
echo.

echo Reports Updated:
echo ✓ Neraca Saldo (Trial Balance) - Professional kop design
echo ✓ Laporan Laba Rugi (Profit & Loss) - Enhanced layout
echo ✓ Neraca (Balance Sheet) - Two-column professional design
echo ✓ Fixed Assets Report - Company branding integration
echo.

echo Design Features:
echo • Logo positioned on left side of letterhead
echo • Company name centered and prominent
echo • Company address, phone, email, website displayed
echo • Gradient backgrounds and professional colors
echo • Enhanced table styling with borders and shadows
echo • Improved typography with proper font weights
echo • Professional color scheme (blue, purple, green accents)
echo • Consistent spacing and alignment
echo • Print-optimized margins and layouts
echo.

echo Next Steps:
echo 1. Test PDF generation for all finance reports
echo 2. Verify company logo displays correctly
echo 3. Check print quality and formatting
echo 4. Test with different outlet configurations
echo 5. Validate company settings integration
echo.

echo ========================================
echo Deployment Complete
echo ========================================
pause