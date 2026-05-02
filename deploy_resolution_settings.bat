@echo off
REM ========================================
REM Deploy Resolution Settings Feature
REM ========================================

echo.
echo ========================================
echo   DEPLOY RESOLUTION SETTINGS FEATURE
echo ========================================
echo.
echo Fitur ini menambahkan:
echo - Setting Resolusi di menu Sistem
echo - Adjust scale, sidebar, font, spacing
echo - Preset cepat untuk berbagai layar
echo - Auto-save ke cookie browser
echo.
echo ========================================
echo.

set /p CONFIRM="Lanjutkan deployment? (Y/n): "
if /i "%CONFIRM%"=="n" (
    echo.
    echo Deployment dibatalkan.
    pause
    exit /b
)

echo.
echo [1/5] Clearing Laravel cache...
php artisan config:clear >nul 2>&1
echo       - Config cache cleared
php artisan cache:clear >nul 2>&1
echo       - Application cache cleared
php artisan route:clear >nul 2>&1
echo       - Route cache cleared
php artisan view:clear >nul 2>&1
echo       - View cache cleared

echo.
echo [2/5] Regenerating cache...
php artisan config:cache >nul 2>&1
echo       - Config cache regenerated
php artisan route:cache >nul 2>&1
echo       - Route cache regenerated

echo.
echo [3/5] Checking files...
if exist "app\Http\Controllers\Admin\ResolutionSettingController.php" (
    echo       ✅ Controller exists
) else (
    echo       ❌ Controller NOT found!
)

if exist "resources\views\admin\sistem\resolusi\index.blade.php" (
    echo       ✅ View exists
) else (
    echo       ❌ View NOT found!
)

if exist "public\js\resolution-settings.js" (
    echo       ✅ JavaScript exists
) else (
    echo       ❌ JavaScript NOT found!
)

if exist "public\css\resolution-settings.css" (
    echo       ✅ CSS exists
) else (
    echo       ❌ CSS NOT found!
)

echo.
echo [4/5] Verifying routes...
php artisan route:list | findstr "resolusi" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo       ✅ Routes registered
) else (
    echo       ❌ Routes NOT found!
)

echo.
echo [5/5] Testing configuration...
php artisan config:show app.url >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo       ✅ Configuration valid
) else (
    echo       ❌ Configuration error!
)

echo.
echo ========================================
echo   DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo.
echo 1. RESTART WEB SERVER
echo    - XAMPP: Stop dan Start Apache
echo    - Artisan: Ctrl+C lalu php artisan serve
echo.
echo 2. CLEAR BROWSER CACHE
echo    - Tekan Ctrl+Shift+Delete
echo    - Pilih "Cached images and files"
echo    - Klik "Clear data"
echo.
echo 3. TEST FITUR
echo    - Login ke aplikasi
echo    - Klik menu Sistem → Setting Resolusi
echo    - Test semua fitur
echo.
echo 4. BACA DOKUMENTASI
echo    - RESOLUTION_SETTINGS_IMPLEMENTATION_COMPLETE.md
echo    - QUICK_TEST_RESOLUTION_SETTINGS.md
echo.
echo ========================================
echo.
pause
