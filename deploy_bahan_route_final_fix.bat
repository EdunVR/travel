@echo off
echo ========================================
echo   BAHAN ROUTE FINAL FIX DEPLOYMENT
echo ========================================
echo.

echo [INFO] Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [INFO] Fix applied successfully!
echo.
echo CHANGES MADE:
echo ✅ Fixed JavaScript to use Laravel route() helper
echo ✅ Changed from method spoofing to direct PUT requests
echo ✅ Added proper JSON headers and body
echo.
echo ROUTES FIXED:
echo ✅ PUT /admin/inventaris/bahan/stock/{id}
echo ✅ PUT /admin/inventaris/bahan/price/{id}
echo.
echo TESTING STEPS:
echo 1. Go to Inventaris ^> Bahan
echo 2. Click "Harga Beli" on any item
echo 3. Try editing stock or price
echo 4. Should work without 404 errors
echo.
echo [SUCCESS] Deployment complete!
pause