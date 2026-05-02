@echo off
echo ========================================
echo DEPLOYING INTER OUTLET OUTLET ACCESS FIXES
echo ========================================

echo.
echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo FIXES DEPLOYED:
echo ✓ Outlet Filtering - Only show user's accessible outlets
echo ✓ History Actions - Added Accept and Delete buttons for pending transactions
echo ✓ COA Settings - Save button visible with outlet-based filtering
echo ✓ Access Control - Validate outlet access in all controller methods
echo ✓ Routes - Added DELETE route for transaction deletion
echo.
echo BROWSER TESTING CHECKLIST:
echo 1. Login as non-super admin user
echo 2. Check outlet dropdown shows only accessible outlets
echo 3. Open 'Riwayat' - should show filtered transactions with action buttons
echo 4. Test Accept/Delete buttons for pending transactions
echo 5. Open 'Setting COA' - should show filtered outlets with save button
echo 6. Test outlet changes reload accounts properly
echo 7. Verify access control prevents unauthorized actions
echo.
pause