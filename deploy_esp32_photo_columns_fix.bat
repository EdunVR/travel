@echo off
echo ========================================
echo ESP32 Photo Columns Database Fix
echo ========================================
echo.

echo Problem: Unknown column 'clock_in_photo' in 'field list'
echo Cause: Database migrations had wrong table names (singular vs plural)
echo Solution: Fixed migration table names and ran migrations
echo.

echo 1. Testing photo columns fix...
php test_esp32_photo_columns_fix.php

echo.
echo 2. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo.
echo 3. Optimizing Laravel...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo ESP32 Photo Columns Fix Deployed!
echo ========================================
echo.
echo Database changes applied:
echo - Fixed attendances table migration (attendance -> attendances)
echo - Added 6 photo columns: clock_in_photo, clock_out_photo, etc.
echo - Fixed recruitments table migration (recruitment -> recruitments)  
echo - Added rfid_uid column to recruitments table
echo.
echo Photo columns now available:
echo - clock_in_photo (for check-in photos)
echo - clock_out_photo (for check-out photos)
echo - break_in_photo (for break start photos)
echo - break_out_photo (for break end photos)
echo - overtime_in_photo (for overtime start photos)
echo - overtime_out_photo (for overtime end photos)
echo.
echo The ESP32-CAM can now save photos with attendance records!
echo.
pause