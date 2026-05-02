@echo off
echo ========================================
echo SERVICE HISTORY ALPINE.JS FIX DEPLOYMENT
echo ========================================
echo.

echo [1/3] Clearing Laravel cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo ✅ Cache cleared
echo.

echo [2/3] Verifying file...
if exist "resources\views\admin\service\history\index.blade.php" (
    echo ✅ File exists: index.blade.php
) else (
    echo ❌ ERROR: File not found!
    pause
    exit /b 1
)
echo.

echo [3/3] Deployment complete!
echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo 1. Open browser and clear cache (Ctrl+Shift+Delete)
echo 2. Login to system
echo 3. Go to: Service ^> History Invoice
echo 4. Open Console (F12) and check for errors
echo 5. Test all features:
echo    - Tab status switching
echo    - Filter by outlet and date
echo    - View PDF
echo    - Update status
echo    - Delete invoice
echo    - Export PDF/Excel
echo.
echo ✅ If no Alpine.js errors in console = SUCCESS!
echo.
echo ========================================
echo TESTING GUIDE:
echo ========================================
echo See: TEST_SERVICE_HISTORY_NOW.md
echo.
pause
