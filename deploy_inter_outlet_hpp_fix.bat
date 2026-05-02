@echo off
echo ===================================
echo DEPLOYING INTER-OUTLET HPP FIX
echo ===================================
echo.

echo 1. Running migration...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migration failed
    pause
    exit /b 1
)
echo Migration completed successfully
echo.

echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo Cache cleared
echo.

echo 3. Running test...
php test_inter_outlet_hpp_fix.php
echo.

echo 4. Fixing HPP data...
php fix_inter_outlet_hpp_data.php
echo.

echo 5. Final test...
php test_inter_outlet_hpp_fix.php
echo.

echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo Next steps:
echo 1. Access margin report to verify the fix
echo 2. Check inter-outlet items for HPP status
echo 3. Monitor logs for any issues
echo.
pause