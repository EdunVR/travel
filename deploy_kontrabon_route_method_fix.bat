@echo off
echo === KONTRA BON ROUTE METHOD FIX DEPLOYMENT ===
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

echo.
echo 2. Testing the route method fix...
php test_kontrabon_route_method_fix.php

echo.
echo 3. Deployment complete!
echo Navigate to: /admin/penjualan/kontrabon
echo.
echo Expected results:
echo ✅ No 405 Method Not Allowed errors
echo ✅ AJAX requests use GET method
echo ✅ Piutang tab shows data immediately
echo ✅ List Kontra Bon tab works correctly
echo ✅ Outlet filter functions properly
echo ✅ No JavaScript console errors
echo.

pause