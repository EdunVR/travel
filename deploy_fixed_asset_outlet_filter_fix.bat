@echo off
echo ========================================
echo DEPLOYING FIXED ASSET OUTLET FILTER FIX
echo ========================================
echo.

echo Step 1: Testing outlet filter fix...
php test_fixed_asset_outlet_filter_fix.php
if %errorlevel% neq 0 (
    echo ERROR: Test failed
    pause
    exit /b 1
)
echo.

echo Step 2: Clearing application cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo Cache cleared successfully.
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo.
echo 1. DOWNLOAD TEMPLATE 404 ERROR:
echo    - Fixed JavaScript URLs to use Laravel route helpers
echo    - Replaced baseUrl variable with proper route() calls
echo    - All import/export URLs now use correct routes
echo.
echo 2. OUTLET FILTER FUNCTIONALITY:
echo    - Added outlet filter dropdown with "Semua Outlet" option
echo    - Book filter now shows "Semua Buku" instead of "Semua"
echo    - Books display with outlet names for clarity
echo    - Dynamic filtering based on outlet selection
echo.
echo 3. MODAL DROPDOWN ENHANCEMENT:
echo    - Modal book dropdown updates based on outlet filter
echo    - Auto-selects single book for selected outlet
echo    - Shows appropriate books for multi-book outlets
echo    - Maintains existing auto-selection logic
echo.
echo 4. JAVASCRIPT IMPROVEMENTS:
echo    - Added outlet change handler
echo    - Created updateModalBookDropdown() function
echo    - Enhanced setDefaultBookId() integration
echo    - Fixed all route URL references
echo.
echo TESTING INSTRUCTIONS:
echo.
echo DOWNLOAD TEMPLATE TEST:
echo 1. Click "Download Template" button
echo 2. Verify Excel file downloads without 404 error
echo 3. Check browser network tab shows correct URL
echo.
echo OUTLET FILTER TEST:
echo 1. Select "Dahana" in outlet filter
echo 2. Verify book filter shows only "BUKU DAHANA 2026"
echo 3. Select "PBU" in outlet filter  
echo 4. Verify book filter shows "Buku Test" and "BUKU KOSONG"
echo 5. Select "Semua Outlet"
echo 6. Verify book filter shows all books with outlet names
echo.
echo MODAL DROPDOWN TEST:
echo 1. Select "Dahana" outlet in filter
echo 2. Click "Tambah Aktiva Tetap"
echo 3. Verify modal shows only Dahana book auto-selected
echo 4. Close modal, select "PBU" outlet
echo 5. Open modal again, verify shows PBU books
echo 6. Test with "Semua Outlet" selection
echo.
echo TROUBLESHOOTING:
echo - Check browser console for JavaScript errors
echo - Verify Laravel logs for route or controller errors
echo - Check network tab for failed requests
echo - Ensure outlet data is properly loaded
echo.
pause