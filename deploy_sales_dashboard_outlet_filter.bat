@echo off
echo ========================================
echo SALES DASHBOARD OUTLET FILTER DEPLOYMENT
echo ========================================
echo.

echo Step 1: Running tests...
php test_sales_dashboard_outlet_filter.php
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Tests failed!
    echo Please fix issues before deploying.
    pause
    exit /b 1
)
echo   - Tests passed
echo.

echo Step 2: Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo   - Cache cleared
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo Sales Dashboard is now filtered by outlet access!
echo.
echo NEXT STEPS:
echo 1. Test with superadmin user
echo 2. Test with single-outlet user
echo 3. Test with multi-outlet user
echo 4. Test outlet switching
echo 5. Verify all data is filtered correctly
echo.
echo DOCUMENTATION:
echo - Read: SALES_DASHBOARD_OUTLET_FILTER_COMPLETE.md
echo - Test: php test_sales_dashboard_outlet_filter.php
echo.

pause
