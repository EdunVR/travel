@echo off
echo ===================================
echo POS Authentication Fix Deployment
echo ===================================

echo.
echo 1. Clearing application cache...
php artisan cache:clear

echo.
echo 2. Clearing config cache...
php artisan config:clear

echo.
echo 3. Clearing route cache...
php artisan route:clear

echo.
echo 4. Clearing view cache...
php artisan view:clear

echo.
echo 5. Clearing session cache...
php artisan session:flush

echo.
echo 6. Optimizing application...
php artisan optimize

echo.
echo 7. Testing POS authentication...
php test_pos_authentication_session_fix.php

echo.
echo ===================================
echo Deployment Complete!
echo ===================================
echo.
echo Changes made:
echo - Fixed session domain configuration (.env)
echo - Added session keep-alive mechanism
echo - Added CSRF token refresh functionality
echo - Improved 401 error handling with redirect to login
echo - Added credentials: 'same-origin' to all fetch requests
echo.
echo Please test the POS system:
echo 1. Login to the system
echo 2. Go to POS page
echo 3. Try changing outlets
echo 4. Check browser console for any errors
echo 5. Verify no 401 errors occur
echo.
pause