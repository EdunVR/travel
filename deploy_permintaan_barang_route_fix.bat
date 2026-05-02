@echo off
echo ===================================
echo DEPLOYING PERMINTAAN BARANG ROUTE FIX
echo ===================================

echo.
echo 1. Testing route fixes...
php test_permintaan_barang_route_fix.php

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
echo ROUTE FIX DEPLOYMENT COMPLETE!
echo ===================================
echo.
echo FIXES APPLIED:
echo - Fixed 405 Method Not Allowed error
echo - Edit modal now uses correct 'update' route
echo - Detail modal uses 'show' route for data loading
echo - All modals use proper named routes
echo - Added parameter replacement for dynamic IDs
echo.
echo TESTING CHECKLIST:
echo 1. Clear browser cache completely
echo 2. Open browser console (F12)
echo 3. Click edit button and make changes
echo 4. Should NOT see 405 error when saving
echo 5. Should see success message and data refresh
echo 6. Test detail, approve, reject, PDF functions
echo.
echo All route errors should now be resolved!
echo ===================================

pause