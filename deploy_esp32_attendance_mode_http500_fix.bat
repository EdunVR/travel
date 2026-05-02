@echo off
echo ========================================
echo ESP32 Attendance Mode HTTP 500 Fix
echo ========================================
echo.

echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Testing attendance time settings...
php test_esp32_attendance_mode_fix.php

echo.
echo 3. Optimizing Laravel...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo ESP32 Attendance Mode Fix Deployed!
echo ========================================
echo.
echo Key fixes applied:
echo - Fixed missing determineTimePeriod method
echo - Added proper error handling in handleAttendanceMode
echo - Improved attendance record creation with required fields
echo - Added photo handling with size limits
echo - Enhanced logging for debugging
echo.
echo The ESP32-CAM should now work properly in attendance mode!
echo Test with your ESP32-CAM device.
echo.
pause