@echo off
echo ===============================================
echo CLEAR CACHE AND TEST FIXED ASSET IMPLEMENTATION
echo ===============================================
echo.

echo 1. Clearing Laravel caches...
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

echo.
echo 2. Testing implementation...
php test_fixed_asset_outlet_filter_final.php

echo.
echo ===============================================
echo CACHE CLEARED SUCCESSFULLY!
echo ===============================================
echo.
echo NEXT STEPS:
echo 1. Open your browser
echo 2. Press Ctrl+Shift+Delete to clear browser cache
echo 3. Select "All time" and check all boxes
echo 4. Click "Clear data"
echo 5. Go to Fixed Asset page
echo 6. Press Ctrl+F5 to hard refresh
echo 7. Test the functionality
echo.
echo If still not working, try:
echo - Incognito/Private browsing mode
echo - Different browser
echo - Check browser console for errors (F12)
echo.
pause