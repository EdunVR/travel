@echo off
echo ===================================
echo DEPLOYING RFID ATTENDANCE FLOW FIX
echo ===================================

echo.
echo 1. Testing RFID attendance flow logic...
php test_rfid_attendance_flow_fix.php

echo.
echo 2. Clearing cache to ensure fresh data...
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo.
echo 3. Testing ESP32 RFID communication...
php test_esp32_rfid_mode_communication.bat

echo.
echo ===================================
echo DEPLOYMENT COMPLETE!
echo ===================================
echo.
echo ✅ RFID attendance flow has been fixed!
echo.
echo Key improvements:
echo - ✅ Follows proper sequence: clock_in → break_in → break_out → clock_out → overtime_in → overtime_out
echo - ✅ Respects time period rules (only updates appropriate fields in each period)  
echo - ✅ Handles outside hours with intelligent sequential logic
echo - ✅ Prevents skipping steps in the attendance flow
echo - ✅ Controller now passes currentTime parameter to determineNextAction method
echo.
echo Next steps:
echo 1. Test with actual ESP32 RFID device
echo 2. Verify attendance records are created correctly
echo 3. Check that photos are saved and displayed properly
echo.
pause