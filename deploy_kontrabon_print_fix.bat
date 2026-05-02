@echo off
echo ========================================
echo KONTRA BON - PRINT DETAIL FIX
echo ========================================
echo.
echo Bug Fix: Print hanya piutang yang dicentang
echo Date: 2026-02-09
echo Version: 1.1.1
echo.

echo [1/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/4] Verifying controller...
if exist "app\Http\Controllers\Admin\KontraBonController.php" (
    echo ✓ KontraBonController found
) else (
    echo ✗ KontraBonController NOT found
    goto :error
)

echo.
echo [3/4] Testing print method...
php artisan route:list --name=kontrabon.print

echo.
echo [4/4] Fix Summary
echo ========================================
echo.
echo Bug Fixed:
echo - Print menampilkan semua piutang (FIXED)
echo - Total di tabel tidak sesuai (FIXED)
echo.
echo Changes:
echo - Method print() sekarang ambil dari detail kontra bon
echo - Method dataKontraBon() hitung total dari detail
echo - PDF hanya tampilkan piutang yang dipilih user
echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next Steps:
echo 1. Test: Buat kontra bon dengan centang 1 piutang
echo 2. Verify: Detail hanya 1 piutang
echo 3. Verify: Print PDF hanya 1 piutang
echo 4. Verify: Total di tabel sesuai
echo.
echo Documentation:
echo - KONTRABON_PRINT_DETAIL_FIX_COMPLETE.md
echo - QUICK_TEST_KONTRABON_PRINT_FIX.md
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
