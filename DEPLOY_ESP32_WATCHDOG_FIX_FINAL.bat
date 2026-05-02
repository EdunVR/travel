@echo off
echo ========================================
echo ESP32-CAM Watchdog Reset - COMPLETE FIX
echo ========================================
echo.

echo PROBLEM:
echo - ESP32-CAM restarting with RTCWDT_RTC_RESET
echo - Occurs when tapping RFID card during registration
echo - Watchdog timer triggering due to long operations
echo.

echo ROOT CAUSES IDENTIFIED:
echo ========================================
echo 1. Photo capture and base64 encoding taking too long
echo 2. HTTP POST with large photo data causing timeout
echo 3. Laravel API processing time
echo 4. Memory allocation issues
echo 5. Network latency
echo.

echo COMPLETE SOLUTION:
echo ========================================
echo.
echo PART 1: ESP32-CAM FIRMWARE FIX
echo -------------------------------
echo File: ESP32_CAM_RFID_Laravel_Fixed.ino
echo.
echo Changes:
echo - Added watchdog timer management (30s timeout)
echo - Strategic watchdog resets at critical points
echo - Photo size limiting (30KB max)
echo - HTTP timeout configuration (15s)
echo - Conservative camera settings (VGA 640x480)
echo - Memory-aware configuration
echo - Better error handling
echo.
echo PART 2: LARAVEL API OPTIMIZATION
echo -------------------------------
echo File: app/Http/Controllers/AttendanceManagementController.php
echo.
echo Changes:
echo - Set maximum execution time (30s)
echo - Background photo processing for large files
echo - Quick validation checks
echo - File size limits (2MB max)
echo - Better error handling
echo - Faster response times
echo.

echo FILES TO DEPLOY:
echo ========================================
echo.
echo ESP32-CAM:
echo 1. ESP32_CAM_RFID_Laravel_Fixed.ino
echo    - Upload to ESP32-CAM using Arduino IDE
echo.
echo LARAVEL:
echo 2. app/Http/Controllers/AttendanceManagementController.php
echo    - Already updated with optimizations
echo.
echo DOCUMENTATION:
echo 3. ESP32_CAM_WATCHDOG_RESET_FIX.md
echo    - Detailed troubleshooting guide
echo.

echo DEPLOYMENT STEPS:
echo ========================================
echo.
echo STEP 1: UPLOAD ESP32-CAM FIRMWARE
echo ----------------------------------
echo 1. Open Arduino IDE
echo 2. Load ESP32_CAM_RFID_Laravel_Fixed.ino
echo 3. Select board: "AI Thinker ESP32-CAM"
echo 4. Select correct COM port
echo 5. Upload firmware
echo 6. Open Serial Monitor (115200 baud)
echo.
echo STEP 2: VERIFY ESP32-CAM
echo ----------------------------------
echo Watch for these messages:
echo   🔧 Configuring watchdog timer...
echo   ✅ Camera initialized successfully
echo   ✅ WiFi terhubung
echo   ✅ PN532 terdeteksi
echo   ✅ Sistem siap, scan kartu...
echo.
echo STEP 3: TEST REGISTRATION
echo ----------------------------------
echo 1. Open Laravel admin panel
echo 2. Go to SDM ^> Kepegawaian
echo 3. Click Edit on any employee
echo 4. Click "Mulai Deteksi UID Kartu"
echo 5. Tap RFID card on reader
echo 6. Monitor Serial output
echo.
echo STEP 4: VERIFY SUCCESS
echo ----------------------------------
echo ESP32 Serial should show:
echo   🔍 Kartu terdeteksi - UID: XX XX XX XX
echo   📸 Photo captured: XXXX bytes, 640x480
echo   ✅ Photo encoded to base64: XXXX characters
echo   📤 Sending with photo (XXXX chars)
echo   📥 Response: {"success":true,...}
echo   ✅ Kartu berhasil dideteksi untuk registrasi
echo.
echo Laravel should show:
echo   - UID detected in modal
echo   - Photo captured (if available)
echo   - No errors in console
echo.

echo EXPECTED BEHAVIOR:
echo ========================================
echo.
echo ✅ No more watchdog resets
echo ✅ Successful photo capture
echo ✅ Fast API response (^<5 seconds)
echo ✅ UID displayed in modal
echo ✅ Photo saved to storage
echo ✅ LED feedback patterns working
echo.

echo TROUBLESHOOTING:
echo ========================================
echo.
echo IF STILL GETTING RESETS:
echo ------------------------
echo 1. Check power supply (5V/2A+ required)
echo 2. Reduce photo quality in ESP32 code:
echo    config.frame_size = FRAMESIZE_QVGA;
echo    config.jpeg_quality = 25;
echo 3. Monitor memory usage in Serial
echo 4. Check WiFi signal strength
echo.
echo IF PHOTOS NOT SAVING:
echo ------------------------
echo 1. Check storage/app/public/attendance_photos exists
echo 2. Check file permissions (755)
echo 3. Monitor Laravel logs: storage/logs/laravel.log
echo 4. Verify base64 encoding is valid
echo.
echo IF API TIMEOUT:
echo ------------------------
echo 1. Check server response time
echo 2. Verify network connectivity
echo 3. Check Laravel queue workers running
echo 4. Monitor server resources
echo.
echo IF UID NOT DETECTED:
echo ------------------------
echo 1. Check PN532 connections (SDA=16, SCL=14)
echo 2. Verify RFID card is compatible (Mifare)
echo 3. Check Serial for PN532 initialization
echo 4. Test with different RFID cards
echo.

echo PERFORMANCE METRICS:
echo ========================================
echo.
echo ESP32-CAM:
echo - Camera init: ^<2 seconds
echo - Photo capture: ^<1 second
echo - Base64 encoding: ^<2 seconds
echo - HTTP POST: ^<5 seconds
echo - Total time: ^<10 seconds
echo.
echo Laravel API:
echo - Request validation: ^<100ms
echo - Database query: ^<200ms
echo - Photo save: ^<500ms
echo - Response: ^<1 second
echo.
echo Memory Usage:
echo - Free heap: ^>250KB
echo - Free PSRAM: ^>3MB (if available)
echo - Photo size: ^<30KB
echo.

echo TESTING CHECKLIST:
echo ========================================
echo.
echo [ ] ESP32-CAM firmware uploaded
echo [ ] Serial monitor shows successful init
echo [ ] WiFi connected
echo [ ] PN532 detected
echo [ ] Camera initialized
echo [ ] Watchdog configured
echo [ ] RFID card tap detected
echo [ ] Photo captured
echo [ ] Base64 encoded
echo [ ] HTTP POST successful
echo [ ] Laravel response received
echo [ ] UID displayed in modal
echo [ ] Photo saved to storage
echo [ ] No watchdog resets
echo [ ] LED feedback working
echo.

echo MONITORING COMMANDS:
echo ========================================
echo.
echo ESP32 Serial Monitor:
echo - Baud rate: 115200
echo - Monitor for errors and resets
echo.
echo Laravel Logs:
echo - tail -f storage/logs/laravel.log
echo - Watch for API errors
echo.
echo Network Monitor:
echo - Check HTTP response times
echo - Verify SSL certificate
echo.

echo SUCCESS INDICATORS:
echo ========================================
echo.
echo ✅ ESP32 runs without resets for ^>5 minutes
echo ✅ Multiple RFID taps work consecutively
echo ✅ Photos consistently saved
echo ✅ API responses under 5 seconds
echo ✅ Memory usage stable
echo ✅ No errors in logs
echo.

echo The complete fix addresses both ESP32-CAM firmware
echo and Laravel API to ensure stable, fast RFID registration
echo with photo capture functionality.
echo.
echo For detailed troubleshooting, see:
echo ESP32_CAM_WATCHDOG_RESET_FIX.md
echo.

pause