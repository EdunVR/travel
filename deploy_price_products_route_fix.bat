@echo off
echo ===================================
echo DEPLOYING PRICE PRODUCTS ROUTE FIX
echo ===================================

echo.
echo 1. Testing route fix...
php test_price_products_route.php

echo.
echo 2. Clearing route cache...
php artisan route:clear

echo.
echo 3. Caching routes with correct order...
php artisan route:cache

echo.
echo 4. Clearing other caches...
php artisan config:clear
php artisan view:clear

echo.
echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo ROUTE ORDER FIX APPLIED:
echo.
echo PROBLEM:
echo - Route /inter-outlet/price-products was returning 404
echo - This was because /inter-outlet/{id} was defined BEFORE it
echo - Laravel was matching "price-products" as an {id} parameter
echo.
echo SOLUTION:
echo - Moved all price settings routes BEFORE parameterized routes
echo - Route order is now:
echo   1. /inter-outlet/price-products (specific)
echo   2. /inter-outlet/update-price (specific)  
echo   3. /inter-outlet/bulk-update-prices (specific)
echo   4. /inter-outlet/{id} (parameterized - catches everything else)
echo.
echo TESTING:
echo 1. Go to: http://localhost/MORRA/admin/penjualan/inter-outlet
echo 2. Click "Setting Harga" button
echo 3. Modal should now load products without 404 error
echo 4. Search functionality should work properly
echo.
pause