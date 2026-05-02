@echo off
echo ===================================
echo FIXING INTER OUTLET PRINT URL ISSUE
echo ===================================
echo.

echo 1. Clearing route cache...
php artisan route:clear
echo.

echo 2. Clearing application cache...
php artisan cache:clear
echo.

echo 3. Clearing config cache...
php artisan config:clear
echo.

echo 4. Clearing view cache...
php artisan view:clear
echo.

echo 5. Optimizing application...
php artisan optimize
echo.

echo 6. Listing inter-outlet routes...
php artisan route:list --name=inter-outlet
echo.

echo ===================================
echo CACHE CLEARING COMPLETED!
echo ===================================
echo.
echo TROUBLESHOOTING STEPS:
echo.
echo 1. VERIFY TRANSACTION EXISTS:
echo    - Go to Inter Outlet Sale page
echo    - Create a new transaction
echo    - Note the transaction ID from success message
echo.
echo 2. TEST URL DIRECTLY:
echo    - Open browser and login to admin
echo    - Go to: /admin/penjualan/inter-outlet/{ID}/print
echo    - Replace {ID} with actual transaction ID
echo.
echo 3. CHECK BROWSER CONSOLE:
echo    - Open Developer Tools (F12)
echo    - Go to Network tab
echo    - Try print function and see actual URL called
echo    - Check HTTP status code (should be 200, not 404)
echo.
echo 4. VERIFY USER PERMISSIONS:
echo    - Make sure user is logged in
echo    - Check if user has access to the transaction
echo    - Verify outlet access permissions
echo.
echo 5. CHECK LARAVEL LOGS:
echo    - Look at storage/logs/laravel.log
echo    - Check for any error messages
echo.
echo EXPECTED WORKING URL FORMAT:
echo /admin/penjualan/inter-outlet/{transaction_id}/print
echo.
echo Example: /admin/penjualan/inter-outlet/123/print
echo.
pause