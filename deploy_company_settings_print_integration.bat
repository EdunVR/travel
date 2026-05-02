@echo off
echo ===================================
echo DEPLOYING COMPANY SETTINGS PRINT INTEGRATION
echo ===================================

echo.
echo 1. Clearing application cache...
php artisan cache:clear

echo.
echo 2. Clearing config cache...
php artisan config:clear

echo.
echo 3. Clearing view cache...
php artisan view:clear

echo.
echo 4. Clearing route cache...
php artisan route:clear

echo.
echo 5. Optimizing application...
php artisan optimize

echo.
echo 6. Testing company settings integration...
php test_company_settings_print_integration.php

echo.
echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo NEXT STEPS:
echo 1. Test kontrabon print functionality
echo 2. Verify company information displays correctly
echo 3. Check different outlets have correct company settings
echo.
pause