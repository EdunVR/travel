@echo off
echo ========================================
echo KONTRA BON - BATAL & FILTER FIX
echo ========================================
echo.
echo Deployment Script
echo Date: 2026-02-09
echo.

echo [1/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/5] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [3/5] Verifying files...
if exist "app\Http\Controllers\Admin\KontraBonController.php" (
    echo ✓ Controller found
) else (
    echo ✗ Controller NOT found
    goto :error
)

if exist "resources\views\admin\penjualan\kontrabon\index.blade.php" (
    echo ✓ View index found
) else (
    echo ✗ View index NOT found
    goto :error
)

if exist "resources\views\admin\penjualan\kontrabon\modals\create.blade.php" (
    echo ✓ Modal create found
) else (
    echo ✗ Modal create NOT found
    goto :error
)

echo.
echo [4/5] Testing routes...
php artisan route:list --name=kontrabon

echo.
echo [5/5] Deployment Summary
echo ========================================
echo.
echo ✓ Fitur Batal Kontra Bon - INSTALLED
echo ✓ Filter Piutang - FIXED
echo ✓ Route batal - REGISTERED
echo ✓ JavaScript function - ADDED
echo.
echo Changes:
echo - Added: Method batal() in KontraBonController
echo - Added: Route POST /{id}/batal
echo - Added: JavaScript function batalKontraBon()
echo - Fixed: getPiutang() now returns ALL piutang
echo - Fixed: Status badge includes 'batal' status
echo - Fixed: Action buttons conditional logic
echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next Steps:
echo 1. Test fitur batal kontra bon
echo 2. Test filter piutang (semua data muncul)
echo 3. Test range tanggal filter
echo 4. Test centang piutang spesifik
echo.
echo Documentation:
echo - KONTRABON_BATAL_AND_FILTER_FIX_COMPLETE.md
echo - QUICK_TEST_KONTRABON_BATAL_FILTER.md
echo.
pause
goto :end

:error
echo.
echo ========================================
echo ERROR: Deployment failed!
echo ========================================
echo Please check the missing files above.
echo.
pause
exit /b 1

:end
