@echo off
echo ========================================
echo BULK PRODUCTION PDF FINAL CLEANUP
echo ========================================
echo.

echo Step 1: Clearing view cache...
php artisan view:clear
if %errorlevel% neq 0 (
    echo ERROR: Failed to clear view cache
    pause
    exit /b 1
)
echo View cache cleared successfully
echo.

echo Step 2: Running test...
php test_bulk_production_pdf_final_cleanup.php
if %errorlevel% neq 0 (
    echo ERROR: Test failed
    pause
    exit /b 1
)
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo Changes applied:
echo   1. Removed "Filter yang Diterapkan" section
echo   2. Removed "Total Data" summary section
echo   3. Changed table header color to gray (#f0f0f0)
echo   4. Fixed logo path (storage_path for DOMPDF)
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to Produksi module
echo 2. Click "Export PDF" dropdown
echo 3. Select "Laporan Produksi Bulk"
echo 4. Verify:
echo    - No filter section displayed
echo    - No total data summary
echo    - Table headers are gray (not blue)
echo    - Company logo displays correctly
echo    - Statistics section still shows
echo.
pause
