@echo off
echo ===================================
echo DEPLOYING PERMINTAAN BARANG MODAL FIXES
echo ===================================

echo.
echo 1. Testing fixes...
php test_permintaan_barang_modal_fixes_final.php

echo.
echo 2. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 3. Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo ===================================
echo DEPLOYMENT COMPLETE!
echo ===================================
echo.
echo FIXES APPLIED:
echo - Fixed Alpine.js modal communication errors
echo - Updated closeModal() functions to use $dispatch
echo - Added proper event listeners to main component
echo - Fixed showApprovalModal and showRejectModal errors
echo - Improved modal data loading and refresh
echo.
echo TESTING CHECKLIST:
echo 1. Open permintaan barang page
echo 2. Click detail button - should open without errors
echo 3. Click close button - should close without 'showEditModal' errors
echo 4. Test edit, approval, reject buttons from detail modal
echo 5. Verify data refreshes after modal actions
echo.
echo All Alpine.js errors should now be resolved!
echo ===================================

pause