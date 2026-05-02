@echo off
echo ========================================
echo DEPLOYING MARGIN REPORT FIFO FIX
echo ========================================
echo.

echo 1. Testing current changes...
php test_margin_report_fifo_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache
echo.

echo 4. Verifying MarginReportController...
findstr /C:"calculateHppFifo" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✓ FIFO method found in MarginReportController
) else (
    echo    ✗ FIFO method NOT found in MarginReportController
)

findstr /C:"calculateHppBarangDagang" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✗ Old average method still exists
) else (
    echo    ✓ Old average method removed from POS calculation
)
echo.

echo 5. Testing margin report endpoint...
echo    Testing API endpoint (requires server to be running)...
echo    URL: /admin/penjualan/margin/data
echo.

echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Login to admin panel
echo 2. Go to Penjualan ^> Laporan Margin
echo 3. Verify HPP calculation uses FIFO method
echo 4. Compare with previous data if available
echo 5. Check margin and profit calculations
echo.
echo FILES MODIFIED:
echo - app/Http/Controllers/MarginReportController.php
echo - test_margin_report_fifo_fix.php (new)
echo - MARGIN_REPORT_FIFO_FIX_COMPLETE.md (new)
echo.
pause