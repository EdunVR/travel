@echo off
echo ========================================
echo DEPLOY KONTRABON TOTAL HUTANG FIX
echo ========================================

echo.
echo [1/3] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/3] Testing calculation...
php fix_kontrabon_total_hutang.php

echo.
echo [3/3] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETED!
echo ========================================
echo.
echo CHANGES MADE:
echo - Fixed total hutang calculation in dataKontraBon method
echo - Fixed total hutang calculation in print method  
echo - Updated print view to use calculated totalHutang
echo - Total hutang now calculated from piutang records
echo - Handles cases with and without kontrabon details
echo.
echo TESTING:
echo 1. Go to admin/penjualan/kontrabon
echo 2. Check "List Kontra Bon" tab - total should show correct values
echo 3. Click print on any kontrabon - total hutang should be correct
echo 4. Create new kontrabon and verify calculations
echo.
pause