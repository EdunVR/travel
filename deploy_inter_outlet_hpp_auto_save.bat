@echo off
echo ===================================
echo DEPLOY INTER-OUTLET HPP AUTO-SAVE
echo ===================================
echo.

echo 1. Testing implementation...
php test_inter_outlet_hpp_auto_save.php
echo.

echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Running migrations (if needed)...
php artisan migrate --force
echo.

echo 4. Testing a sample transaction creation...
echo    (This will be done manually through the interface)
echo.

echo ===================================
echo DEPLOYMENT COMPLETE
echo ===================================
echo.
echo NEXT STEPS:
echo 1. Go to Inter-Outlet Sale page
echo 2. Create a new transaction
echo 3. Check if data_hpp column is populated automatically
echo 4. Verify margin report shows correct HPP data
echo.
pause