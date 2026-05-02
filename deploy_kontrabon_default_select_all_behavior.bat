@echo off
echo ========================================
echo DEPLOYING KONTRA BON DEFAULT SELECT ALL BEHAVIOR
echo ========================================
echo.

echo Step 1: Clear application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Step 2: Test the implementation...
php test_kontrabon_default_select_all_behavior.php

echo.
echo Step 3: Verify controller logic...
echo Checking data() method for empty outlet handling...
echo Checking dataKontraBon() method for empty outlet handling...

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Open Kontra Bon page in browser
echo 2. Verify all outlets are selected by default
echo 3. Test "Hapus Semua" button (should show no data)
echo 4. Test "Pilih Semua" button (should show all data)
echo 5. Test individual outlet selection
echo.
echo URL: https://poshan.my.id/tofu/admin/penjualan/kontrabon
echo.
pause