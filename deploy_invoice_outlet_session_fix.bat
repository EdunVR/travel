@echo off
echo === DEPLOYING INVOICE OUTLET SESSION FIX ===
echo.

echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing the fix...
php test_invoice_outlet_session_fix.php

echo.
echo 3. Fix Summary:
echo    - Fixed Alpine.js error "ALL is not defined"
echo    - Changed selectedOutlet initialization to use @json()
echo    - This properly quotes string values in JavaScript
echo.

echo 4. Manual Testing Steps:
echo    - Open invoice page: /admin/penjualan/invoice
echo    - Check browser console for Alpine.js errors
echo    - Test outlet switching functionality
echo    - Verify no "ALL is not defined" error
echo.

echo === DEPLOYMENT COMPLETE ===
pause