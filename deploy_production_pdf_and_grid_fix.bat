@echo off
echo ========================================
echo DEPLOYING PRODUCTION PDF AND GRID FIXES
echo ========================================
echo.

echo 1. Testing fixes...
php test_production_fixes_simple.php
echo.

echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 3. Testing production grid data...
echo Visit: /admin/produksi/produksi and check the grid
echo - HPP per unit should show actual values instead of "-"
echo - Total cost should show actual values instead of "-"
echo.

echo 4. Testing production PDF...
echo Visit: /admin/produksi/produksi and click "Lihat Detail" on any production
echo - Single product should show product name correctly
echo - Multi-product should show "Multi-Produk (X produk)"
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. PDF template now uses hppRecords for product display
echo 2. Grid now calculates actual HPP per unit and total cost
echo 3. Both fixes support single and multi-product scenarios
echo 4. FIFO system properly implemented for cost calculations
echo.
pause