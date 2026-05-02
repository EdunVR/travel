@echo off
echo Testing Add Stock Endpoint with CURL
echo =====================================
echo.

set PRODUCT_ID=12
set BASE_URL=https://group.dahana-boiler.com/MORRA
set ENDPOINT=%BASE_URL%/admin/inventaris/produk/%PRODUCT_ID%/add-stock

echo Testing URL: %ENDPOINT%
echo.

echo 1. Testing without authentication (should get 302 redirect to login)
curl -X POST "%ENDPOINT%" -H "Content-Type: application/json" -d "{\"jumlah\": 5, \"hpp\": 25000}" -v

echo.
echo.
echo 2. Testing with invalid data (should get validation errors)
echo Note: You need to be logged in and have CSRF token for this to work properly
echo.

echo To test properly:
echo 1. Login to the application in browser
echo 2. Go to /admin/inventaris/produk
echo 3. Open browser developer tools
echo 4. Try to add stock and check the network tab
echo 5. Look for the actual request being made

pause