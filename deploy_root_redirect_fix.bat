@echo off
echo ========================================
echo DEPLOYING ROOT URL REDIRECT FIX
echo ========================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo 3. Testing root URL redirect...
php test_root_redirect_fix.php

echo.
echo 4. Checking session configuration...
php artisan tinker --execute="echo 'Session driver: ' . config('session.driver') . PHP_EOL; echo 'Session path: ' . config('session.path') . PHP_EOL; echo 'Session lifetime: ' . config('session.lifetime') . ' minutes' . PHP_EOL;"

echo.
echo ========================================
echo ROOT URL REDIRECT FIX DEPLOYED
echo ========================================
echo.
echo FIXES APPLIED:
echo - Fixed session path configuration
echo - Added proper error handling for root URL
echo - Implemented 405 Method Not Allowed handling
echo - Added health check endpoint
echo - Enhanced .htaccess rules
echo - Created session conflict prevention middleware
echo.
echo TESTING STEPS:
echo 1. Open browser and navigate to base URL
echo 2. Should redirect to login page automatically
echo 3. No 405 Method Not Allowed errors should occur
echo 4. Session handling should work properly
echo 5. Test with different HTTP methods
echo.
echo If issues persist:
echo - Clear browser cache and cookies
echo - Check server error logs
echo - Verify .htaccess is properly loaded
echo - Test session functionality after login
echo.
pause