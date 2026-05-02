@echo off
echo ========================================
echo  DEPLOY SESSION FIX - Production Ready
echo ========================================
echo.

echo [1/6] Running migration...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migration failed!
    pause
    exit /b 1
)
echo Migration completed successfully!
echo.

echo [2/6] Clearing configuration cache...
php artisan config:clear
echo.

echo [3/6] Clearing application cache...
php artisan cache:clear
echo.

echo [4/6] Clearing route cache...
php artisan route:clear
echo.

echo [5/6] Clearing view cache...
php artisan view:clear
echo.

echo [6/6] Caching configuration for production...
php artisan config:cache
echo.

echo ========================================
echo  DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Restart your web server (Apache/Nginx)
echo 2. Clear browser cache (Ctrl+Shift+Delete)
echo 3. Test the application
echo.
echo Session driver is now: DATABASE
echo Session lifetime: 1440 minutes (24 hours)
echo.
pause
