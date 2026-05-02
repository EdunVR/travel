@echo off
echo ========================================
echo QC TOFU MENTAH PDF LOGO FIX
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
php test_qc_tofu_mentah_logo_fix.php
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
echo   - Fixed logo path in QC Tofu Mentah PDF
echo   - Changed from public_path to storage_path
echo   - Logo now displays correctly in PDF
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to Produksi module
echo 2. Click "Export PDF" dropdown
echo 3. Select "QC Egg Tofu Mentah"
echo 4. Verify logo displays correctly in PDF
echo 5. Compare with Laporan Produksi Bulk (should match)
echo.
pause
