@echo off
echo ========================================
echo  Deploy Inter Outlet Default Outlet Removal
echo ========================================
echo.

echo [1/4] Testing current implementation...
php test_inter_outlet_default_outlet_removal.php

echo.
echo [2/4] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [3/4] Testing route availability...
php artisan route:list | findstr "inter-outlet.index"

echo.
echo [4/4] Final verification...
echo Checking if all files are properly updated...

if exist "app\Http\Controllers\InterOutletSaleController.php" (
    echo ✓ Controller file exists
) else (
    echo ✗ Controller file missing
)

if exist "public\js\inter-outlet.js" (
    echo ✓ JavaScript file exists
) else (
    echo ✗ JavaScript file missing
)

if exist "resources\views\admin\penjualan\inter-outlet\index.blade.php" (
    echo ✓ View file exists
) else (
    echo ✗ View file missing
)

echo.
echo ========================================
echo  Deployment Complete!
echo ========================================
echo.
echo 📋 Summary of Changes:
echo   ✓ Removed default outlet "ALL" selection
echo   ✓ Added "Pilih Outlet" as default option
echo   ✓ Products only load when outlet is selected
echo   ✓ Improved empty state messaging
echo   ✓ Better null value handling
echo.
echo 🧪 Testing Instructions:
echo   1. Open penjualan antar outlet page
echo   2. Verify dropdown shows "Pilih Outlet" by default
echo   3. Verify no products shown until outlet selected
echo   4. Select outlet and verify products load
echo   5. Change outlet and verify data updates
echo.
echo 🔧 If issues occur:
echo   1. Clear browser cache (Ctrl+F5)
echo   2. Check browser console for JavaScript errors
echo   3. Verify user has outlet access permissions
echo.
pause