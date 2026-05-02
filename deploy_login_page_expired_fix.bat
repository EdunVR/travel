@echo off
echo ===================================
echo   DEPLOY LOGIN PAGE EXPIRED FIX
echo ===================================
echo.

echo 1. Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Checking session table (if using database sessions)...
php artisan session:table --force 2>nul
php artisan migrate --force

echo.
echo 3. Testing login page access...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost/tofu/login

echo.
echo 4. Checking file permissions...
if exist "storage\framework\sessions" (
    echo Session directory exists: storage\framework\sessions
) else (
    echo Creating session directory...
    mkdir storage\framework\sessions
)

echo.
echo 5. Verifying configuration files...
if exist "app\Http\Middleware\VerifyCsrfToken.php" (
    echo ✅ Custom CSRF middleware created
) else (
    echo ❌ CSRF middleware missing
)

if exist "resources\views\auth\login.blade.php" (
    echo ✅ Enhanced login view created
) else (
    echo ❌ Login view missing
)

echo.
echo 6. Setting proper permissions...
icacls storage /grant Everyone:F /T >nul 2>&1
icacls bootstrap\cache /grant Everyone:F /T >nul 2>&1

echo.
echo ===================================
echo   DEPLOYMENT COMPLETED
echo ===================================
echo.
echo Next steps:
echo 1. Test login at: http://localhost/tofu/login
echo 2. Clear browser cache completely
echo 3. Try login with fresh session
echo 4. Monitor logs: storage\logs\laravel.log
echo.
echo If still getting 419 errors:
echo 1. Check .env SESSION_* settings
echo 2. Verify APP_KEY is set
echo 3. Check web server configuration
echo.
pause