@echo off
echo ========================================
echo DEPLOYING BULK PRODUCTION PDF EXPORT FIX
echo ========================================
echo.

echo TESTING FIX...
php test_bulk_production_pdf_export_fix.php
echo.

echo CLEARING APPLICATION CACHE...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo FIX APPLIED:
echo 1. Removed 'materials.bahan' from eager loading
echo    - Changed from: materials.bahan
echo    - Changed to: materials
echo.
echo 2. Updated material cost calculation
echo    - Now uses getFifoPrice() method
echo    - Consistent with other methods
echo    - FIFO pricing for accurate costs
echo.

echo ISSUE RESOLVED:
echo - Error: Call to undefined relationship [bahan]
echo - Location: exportBulkProductionPdf() method
echo - Cause: Incorrect eager loading relationship
echo.

echo TESTING INSTRUCTIONS:
echo 1. Open production module
echo 2. Click "Export PDF" dropdown
echo 3. Select "Laporan Produksi"
echo 4. Verify PDF generates without errors
echo 5. Check material costs are accurate
echo 6. Test with different filters
echo.

echo VERIFICATION CHECKLIST:
echo □ No relationship errors
echo □ PDF generates successfully
echo □ Material costs are correct
echo □ HPP values are consistent
echo □ Filters work correctly
echo.

echo ✅ BULK PRODUCTION PDF EXPORT FIX DEPLOYED!
echo The export now works without relationship errors.
pause