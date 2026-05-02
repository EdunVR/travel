@echo off
echo ========================================
echo DEPLOY: Payroll COA Outlet Filter
echo ========================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared
echo.

echo [2/5] Optimizing routes...
php artisan route:cache
echo ✓ Routes cached
echo.

echo [3/5] Optimizing config...
php artisan config:cache
echo ✓ Config cached
echo.

echo [4/5] Optimizing views...
php artisan view:cache
echo ✓ Views cached
echo.

echo [5/5] Running test script...
php test_payroll_coa_outlet_filter.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Open browser and go to Setting COA Payroll
echo 2. Select an outlet
echo 3. Verify accounts load correctly
echo 4. Verify only leaf accounts are shown
echo 5. Test save functionality
echo.
echo Documentation: PAYROLL_COA_OUTLET_FILTER_COMPLETE.md
echo.

pause
