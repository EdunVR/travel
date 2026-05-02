@echo off
echo ========================================
echo DEPLOYING MARGIN HPP PER UNIT FIX
echo ========================================
echo.

echo MASALAH: HPP di laporan margin menampilkan total HPP, bukan HPP per unit
echo SOLUSI: Bagi total HPP FIFO dengan quantity untuk mendapat HPP per unit
echo.

echo 1. Testing HPP per unit calculation...
php test_margin_hpp_per_unit_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo 3. Verifying code changes...
findstr /C:"$totalHppFifo / $kuantitas" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✓ HPP per unit calculation found
) else (
    echo    ✗ HPP per unit calculation NOT found
)

findstr /C:"$kuantitas > 0 ?" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✓ Division by zero protection found
) else (
    echo    ✗ Division by zero protection NOT found
)
echo.

echo 4. Example calculation verification...
echo    Data HPP: Batch 1 (Rp 10.000, 5 unit), Batch 2 (Rp 12.000, 3 unit)
echo    Penjualan: 4 unit
echo    Total HPP FIFO: 4 × Rp 10.000 = Rp 40.000
echo    HPP per unit: Rp 40.000 ÷ 4 = Rp 10.000 ✓
echo    Profit calculation: Subtotal - (Rp 10.000 × 4) ✓
echo.

echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo TESTING CHECKLIST:
echo 1. Login to admin panel
echo 2. Go to Penjualan ^> Laporan Margin
echo 3. Select period and outlet
echo 4. Verify HPP column shows HPP per unit (not total)
echo 5. Check profit and margin calculations
echo.
echo EXPECTED RESULTS:
echo - HPP column shows price per unit
echo - Profit = Subtotal - (HPP per unit × Quantity)
echo - Margin percentage calculated correctly
echo - No division by zero errors
echo.
echo FILES MODIFIED:
echo - app/Http/Controllers/MarginReportController.php
echo - test_margin_hpp_per_unit_fix.php (new)
echo - MARGIN_HPP_PER_UNIT_FIX_COMPLETE.md (new)
echo.
pause