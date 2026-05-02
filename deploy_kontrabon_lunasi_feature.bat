@echo off
echo ========================================
echo DEPLOY KONTRABON LUNASI FEATURE
echo ========================================

echo.
echo [1/4] Running migrations...
php artisan migrate --force

echo.
echo [2/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [3/4] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [4/4] Testing kontrabon functionality...
php test_kontrabon_lunasi_feature.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED!
echo ========================================
echo.
echo CHANGES MADE:
echo - Fixed total hutang calculation in print view
echo - Added lunasi button in kontrabon index
echo - Added lunasi method in controller
echo - Added status display for lunas kontrabon
echo - Updated database structure for kontrabon penjualan
echo.
echo TESTING:
echo 1. Go to admin/penjualan/kontrabon
echo 2. Create a new kontrabon
echo 3. Check the print view shows correct total
echo 4. Click lunasi button to mark as paid
echo 5. Verify status changes to "Lunas"
echo 6. Print again to see "STATUS: LUNAS"
echo.
pause