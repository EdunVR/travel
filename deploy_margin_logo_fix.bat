@echo off
echo ========================================
echo DEPLOY MARGIN REPORT LOGO FIX
echo ========================================
echo.

echo [1/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [2/4] Ensuring storage link exists...
php artisan storage:link

echo.
echo [3/4] Testing logo fix...
php test_margin_logo_fix.php

echo.
echo [4/4] Deployment completed!
echo.
echo CHANGES APPLIED:
echo - Added HasCompanySettings trait to MarginReportController
echo - Using getCompanySettingsForPrint() method (same as inter-outlet)
echo - Added outlet session setting for proper company settings
echo - Added debug info to PDF template
echo.
echo TROUBLESHOOTING STEPS:
echo 1. Check if company settings exist in database
echo 2. Verify logo file exists in storage/app/public/
echo 3. Check debug info in exported PDF
echo 4. Compare with inter-outlet PDF export
echo.
echo TEST THE FOLLOWING:
echo 1. Go to Laporan Margin (admin/penjualan/margin)
echo 2. Select specific outlet (if multiple outlets exist)
echo 3. Click Export PDF
echo 4. Check PDF source for debug info
echo 5. Verify logo appears in header
echo.
pause