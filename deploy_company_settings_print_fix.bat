@echo off
echo ===================================
echo DEPLOYING COMPANY SETTINGS PRINT FIX
echo ===================================

echo.
echo 1. Testing company settings logo fix...
php test_company_settings_logo_fix.php

echo.
echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo 3. Testing Inter Outlet Sale print with logo...
echo Visit: http://localhost/MORRA/admin/penjualan/inter-outlet
echo Then click "Print" on any transaction to test logo display

echo.
echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo WHAT WAS FIXED:
echo - Updated HasCompanySettings trait to use logo_url accessor
echo - Fixed print template to use proper logo URL format
echo - Logo should now display correctly in print templates
echo.
echo NEXT STEPS:
echo 1. Test print functionality in Inter Outlet Sales
echo 2. Verify logo displays correctly
echo 3. Check other print templates if needed
echo.
pause