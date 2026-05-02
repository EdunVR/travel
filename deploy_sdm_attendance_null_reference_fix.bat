@echo off
echo ========================================
echo SDM ATTENDANCE NULL REFERENCE FIX
echo ========================================
echo.

echo [1/4] Running null reference fix test...
php test_sdm_attendance_null_reference_fix.php
if %errorlevel% neq 0 (
    echo ERROR: Null reference fix test failed!
    pause
    exit /b 1
)

echo.
echo [2/4] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [3/4] Testing SDM Attendance route...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" "http://localhost/admin/sdm/attendance"

echo.
echo [4/4] Deployment completed successfully!
echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo 1. Open browser and navigate to SDM Attendance
echo 2. Press Ctrl+F5 to clear browser cache
echo 3. Open Time Settings modal (purple button)
echo 4. Test the "Test Periode Waktu" feature
echo 5. Check browser console - should be no errors
echo 6. Verify testResult displays properly
echo ========================================
echo.
pause