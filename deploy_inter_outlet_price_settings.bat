@echo off
echo ===================================
echo DEPLOYING INTER OUTLET PRICE SETTINGS
echo ===================================

echo.
echo 1. Running database migration for markup_percent column...
php artisan migrate --path=database/migrations/2024_12_23_000001_add_markup_percent_to_produk_table.php

echo.
echo 2. Testing price settings functionality...
php test_inter_outlet_price_settings.php

echo.
echo 3. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 4. Caching routes and config...
php artisan route:cache
php artisan config:cache

echo.
echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo WHAT WAS IMPLEMENTED:
echo - History modal now full screen height
echo - Removed "Tambah Transaksi" button from history
echo - Added "Setting Harga" button to main page
echo - Created price settings modal with HPP, markup, and final price
echo - Added markup_percent column to produk table
echo - Implemented single and bulk price update functionality
echo.
echo TESTING:
echo 1. Go to: http://localhost/MORRA/admin/penjualan/inter-outlet
echo 2. Click "Riwayat" - should open full screen modal
echo 3. Click "Setting Harga" - should open price settings modal
echo 4. Test markup calculation and price updates
echo.
pause