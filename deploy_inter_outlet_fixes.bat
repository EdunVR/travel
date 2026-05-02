@echo off
echo ===================================
echo DEPLOYING INTER OUTLET FIXES
echo ===================================

echo.
echo 1. Running database migration for markup_percent column...
php artisan migrate --path=database/migrations/2024_12_23_000001_add_markup_percent_to_produk_table.php

echo.
echo 2. Testing all fixes...
php test_inter_outlet_fixes.php

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
echo FIXES APPLIED:
echo.
echo 1. HISTORY MODAL TABLE FIX:
echo    - Simplified layout for iframe usage
echo    - Fixed DataTable height to 400px
echo    - Removed complex flexbox that was causing issues
echo    - Table now displays properly without being cut off
echo.
echo 2. COA MODAL ERROR FIX:
echo    - Fixed getFilteredOutlets method error
echo    - Replaced with simple Outlet::where query
echo    - COA modal should now load without errors
echo.
echo 3. PRICE SETTINGS SEARCH FIX:
echo    - Fixed loadPriceProducts to trigger on modal open
echo    - Removed from init() to prevent unnecessary loading
echo    - Search functionality should now work properly
echo.
echo TESTING STEPS:
echo 1. Go to: http://localhost/MORRA/admin/penjualan/inter-outlet
echo 2. Click "Riwayat" - table should display properly
echo 3. Click "Setting COA" - should load without errors
echo 4. Click "Setting Harga" - should show products with working search
echo.
pause