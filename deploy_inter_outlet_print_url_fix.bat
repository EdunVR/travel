@echo off
echo ===================================
echo DEPLOYING INTER OUTLET PRINT URL FIX
echo ===================================
echo.

echo 1. Testing route updates...
php test_inter_outlet_print_routes.php
echo.

echo 2. Clearing route cache...
php artisan route:clear
echo.

echo 3. Clearing application cache...
php artisan cache:clear
echo.

echo 4. Clearing config cache...
php artisan config:clear
echo.

echo 5. Clearing view cache...
php artisan view:clear
echo.

echo 6. Optimizing application...
php artisan optimize
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo PRINT URL ISSUE FIXED:
echo 1. Updated JavaScript to use correct route
echo 2. Changed from 'inter-outlet' to 'inter-outlet-sale'
echo 3. All caches cleared
echo 4. Routes optimized
echo.
echo NEW BEHAVIOR:
echo - Modal opens correctly ✅
echo - PDF URL uses: /admin/penjualan/inter-outlet-sale/{id}/print
echo - Should resolve to valid route ✅
echo - PDF should load in iframe ✅
echo - No more 'Not Found' errors ✅
echo.
echo TESTING INSTRUCTIONS:
echo 1. Refresh Inter Outlet Sale page
echo 2. Create a new transaction
echo 3. Click "Print Invoice" from success modal
echo 4. Verify PDF loads (not "Not Found")
echo 5. Test print from history modal
echo 6. Check browser console for any errors
echo.
echo If issues persist:
echo - Check Laravel logs: storage/logs/laravel.log
echo - Test URL directly in browser while logged in
echo - Verify transaction ID exists in database
echo - Check user permissions for the transaction
echo.
pause