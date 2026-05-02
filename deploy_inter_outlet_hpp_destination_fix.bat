@echo off
echo ===================================
echo DEPLOY INTER-OUTLET HPP DESTINATION FIX
echo ===================================
echo.

echo 1. Testing current state...
php test_inter_outlet_hpp_destination_fix.php
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

echo ===================================
echo DEPLOYMENT COMPLETE
echo ===================================
echo.
echo NEXT STEPS:
echo 1. Create a new inter-outlet transaction
echo 2. Verify HPP in destination outlet = selling price from source outlet
echo 3. Check margin report for correct calculations
echo.
echo EXPECTED BEHAVIOR:
echo - Source outlet selling price becomes destination outlet HPP
echo - No more 0 HPP in destination outlet
echo - Accurate margin calculations in reports
echo.
pause