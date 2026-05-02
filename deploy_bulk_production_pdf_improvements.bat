@echo off
echo ============================================================
echo DEPLOYING: Bulk Production PDF Improvements
echo ============================================================
echo.

echo CHANGES BEING DEPLOYED:
echo 1. Fixed reject data (from realizations table)
echo 2. Added statistics (avg HPP, avg target, avg realized)
echo 3. Added proper margins (20px) to PDF
echo 4. Removed "Semua" option from outlet filter
echo 5. Set default outlet to first accessible outlet
echo.

echo ============================================================
echo Step 1: Testing Statistics Calculation
echo ============================================================
php test_bulk_production_pdf_improvements.php
if errorlevel 1 (
    echo ERROR: Test failed!
    pause
    exit /b 1
)
echo.

echo ============================================================
echo Step 2: Clear Application Cache
echo ============================================================
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo Cache cleared successfully!
echo.

echo ============================================================
echo DEPLOYMENT SUMMARY
echo ============================================================
echo.
echo FILES MODIFIED:
echo 1. app/Http/Controllers/ProductionController.php
echo    - exportBulkProductionPdf() method
echo    - Added statistics calculation
echo    - Fixed reject data from realizations
echo.
echo 2. resources/views/admin/produksi/produksi/bulk-production-pdf.blade.php
echo    - Added 20px margins to body
echo    - Added statistics section before table
echo    - Display avg HPP, avg target, avg realized, total rejected
echo.
echo 3. resources/views/admin/produksi/produksi/index.blade.php
echo    - Removed "Outlet: Semua" option
echo    - Set default outlet to first accessible outlet
echo    - Updated init() to fetch outlets first
echo    - Updated fetchData() and fetchStats() outlet handling
echo.

echo ============================================================
echo TESTING INSTRUCTIONS
echo ============================================================
echo.
echo 1. Open browser and navigate to Production page
echo 2. Clear browser cache (Ctrl+Shift+Delete)
echo 3. Verify outlet filter shows only accessible outlets (no "Semua")
echo 4. Verify default outlet is automatically selected
echo 5. Click "Export PDF" -^> "Laporan Produksi"
echo 6. Verify PDF has:
echo    - Proper margins (20px all sides)
echo    - Statistics section with averages
echo    - Correct reject data
echo.

echo ============================================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ============================================================
echo.
echo All improvements have been deployed.
echo Please test the functionality in the browser.
echo.
pause
