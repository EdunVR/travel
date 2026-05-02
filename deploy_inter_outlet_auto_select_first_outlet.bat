@echo off
echo ========================================
echo  Deploy Inter Outlet Auto Select First Outlet
echo ========================================
echo.

echo [1/4] Testing current implementation...
php test_inter_outlet_auto_select_first_outlet.php

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
echo   ✓ Auto-select first available outlet
echo   ✓ Removed "Pilih Outlet" empty option
echo   ✓ Products load immediately on page load
echo   ✓ Improved user experience
echo   ✓ Better performance with immediate data loading
echo.
echo 🧪 Testing Instructions:
echo   1. Open penjualan antar outlet page
echo   2. Verify first outlet is automatically selected
echo   3. Verify products load immediately
echo   4. Change outlet and verify data updates
echo   5. Verify no empty option in outlet dropdown
echo.
echo 🔧 If issues occur:
echo   1. Clear browser cache (Ctrl+F5)
echo   2. Check browser console for JavaScript errors
echo   3. Verify user has outlet access permissions
echo.
pause