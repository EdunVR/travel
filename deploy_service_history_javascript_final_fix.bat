@echo off
echo ========================================
echo SERVICE HISTORY JAVASCRIPT FINAL FIX
echo ========================================
echo.

echo [1/4] Running JavaScript syntax test...
php test_service_history_javascript_final_fix.php
if %errorlevel% neq 0 (
    echo ERROR: JavaScript test failed!
    pause
    exit /b 1
)

echo.
echo [2/4] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [3/4] Testing Service History route...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" "http://localhost/admin/service/history"

echo.
echo [4/4] Deployment completed successfully!
echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo 1. Open browser and navigate to Service History
echo 2. Press Ctrl+F5 to clear browser cache
echo 3. Check browser console for any errors
echo 4. Test all functionality:
echo    - Outlet checkbox filter
echo    - Status tabs
echo    - Date filters
echo    - Export functions
echo    - Modal operations
echo ========================================
echo.
pause