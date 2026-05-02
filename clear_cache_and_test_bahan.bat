@echo off
echo ========================================
echo CLEAR CACHE AND TEST BAHAN EDIT
echo ========================================
echo.

echo [CACHE] Clearing Laravel caches...
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
echo.

echo [ROUTES] Checking bahan routes...
php artisan route:list --name=bahan
echo.

echo [TEST] Running route fix test...
php test_bahan_route_fix.php
echo.

echo [INFO] Route fix applied:
echo - Changed from route() helper to direct URL
echo - PUT /admin/inventaris/bahan/price/{id}
echo - PUT /admin/inventaris/bahan/stock/{id}
echo.

echo [TESTING] Manual testing steps:
echo 1. Open admin/inventaris/bahan in browser
echo 2. Click "Harga Beli" button on any bahan
echo 3. Click edit icons to modify values
echo 4. Check Network tab for correct URLs
echo 5. Verify data saves successfully
echo.

echo [SUCCESS] Cache cleared and route fix applied!
echo.

echo Test the functionality in browser now.
echo.
pause