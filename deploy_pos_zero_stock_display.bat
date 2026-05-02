@echo off
echo ========================================
echo   POS Zero Stock Display Deployment
echo ========================================
echo.

echo 1. Testing POS Zero Stock Feature...
php test_pos_zero_stock.php
echo.

echo 2. Clearing POS cache...
curl -X POST "http://localhost/MORRA_POSHAN/admin/penjualan/pos/clear-cache?outlet_id=all" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.

echo 3. Warming up cache with new data...
curl -X POST "http://localhost/MORRA_POSHAN/admin/penjualan/pos/warm-cache" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.

echo 4. Testing POS products API...
curl -X GET "http://localhost/MORRA_POSHAN/admin/penjualan/pos/products?outlet_id=1" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.

echo ========================================
echo   Deployment Complete!
echo ========================================
echo.
echo CHANGES APPLIED:
echo - Products with stock 0 now visible in POS
echo - Visual indicators for out-of-stock items
echo - Modal notification prevents adding zero stock
echo - Improved user experience
echo.
echo NEXT STEPS:
echo 1. Test POS interface in browser
echo 2. Try clicking on zero stock products
echo 3. Verify modal notification appears
echo 4. Check visual indicators work correctly
echo.
pause