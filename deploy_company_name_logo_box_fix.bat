@echo off
echo ========================================
echo  DEPLOY: Company Name Logo Box Fix
echo ========================================
echo.
echo This script will deploy the company name logo box fix.
echo.
echo Changes to be applied:
echo - Logo box will show company name instead of outlet name
echo - Phone number will show company phone instead of outlet phone
echo - Both invoice "Ikuti POS" template and POS nota besar will be consistent
echo.
pause

echo.
echo [1/2] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [2/2] Optimizing application...
php artisan config:cache
php artisan view:cache

echo.
echo ========================================
echo  DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Company name logo box fix has been deployed successfully.
echo.
echo Changes applied:
echo - Invoice print template "Ikuti POS" now uses company name
echo - POS nota besar now uses company name
echo - Both templates now use company phone number
echo.
echo How to test:
echo 1. Print invoice with "Ikuti POS" template
echo 2. Print POS nota besar
echo 3. Verify logo box shows company name (not outlet name)
echo 4. Verify phone shows company phone (not outlet phone)
echo.
echo Files modified:
echo - resources/views/admin/penjualan/invoice/print.blade.php
echo - resources/views/admin/penjualan/pos/nota_besar.blade.php
echo.
echo Note: Make sure company settings are configured properly:
echo Admin Panel ^> Pengaturan ^> Pengaturan Perusahaan
echo.
pause