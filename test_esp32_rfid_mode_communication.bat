@echo off
echo ========================================
echo ESP32-CAM RFID Mode Communication Test
echo ========================================
echo.

echo TESTING LARAVEL API ENDPOINTS...
echo.

echo 1. Running PHP debug script...
php debug_esp32_rfid_mode_communication.php
echo.

echo 2. MANUAL TESTING STEPS:
echo ========================================
echo.
echo STEP 1: Check ESP32-CAM Serial Monitor
echo ---------------------------------------
echo 1. Open Arduino IDE Serial Monitor (115200 baud)
echo 2. Reset ESP32-CAM
echo 3. Look for these messages:
echo    - ✅ WiFi terhubung
echo    - 🌐 Server URL: https://poshan.my.id/tofu
echo    - 🔗 API Endpoint: /api/morra/api/rfid
echo    - 📡 Mode Check URL: https://poshan.my.id/tofu/api/morra/api/rfid/mode
echo    - 📤 Card Detect URL: https://poshan.my.id/tofu/api/morra/api/rfid/card-detected
echo.

echo STEP 2: Monitor Mode Checking
echo ---------------------------------------
echo Every 2 seconds, ESP32 should show:
echo    🔍 Checking mode from server...
echo    📡 Mode URL: https://poshan.my.id/tofu/api/morra/api/rfid/mode
echo    📥 HTTP Response Code: 200
echo    📥 Response: {"success":true,"mode":"attendance"}
echo    📋 Current server mode: attendance
echo    📋 Current ESP32 mode: attendance
echo    ✅ Mode unchanged: attendance
echo.

echo STEP 3: Test Mode Change from Laravel
echo ---------------------------------------
echo 1. Open Laravel admin panel
echo 2. Go to SDM ^> Kepegawaian
echo 3. Click Edit on any employee
echo 4. Click "Mulai Deteksi UID Kartu"
echo 5. Watch ESP32 Serial Monitor for:
echo    🔍 Checking mode from server...
echo    📥 HTTP Response Code: 200
echo    📥 Response: {"success":true,"mode":"register"}
echo    📋 Current server mode: register
echo    📋 Current ESP32 mode: attendance
echo    🔄 Mode berubah ke: register
echo    [LED should flash 3 times]
echo.

echo STEP 4: Test RFID Card Detection
echo ---------------------------------------
echo 1. After mode changes to "register"
echo 2. Tap RFID card on reader
echo 3. ESP32 should show:
echo    🔍 Kartu terdeteksi - UID: XX XX XX XX
echo    📸 Capturing photo...
echo    📤 Sending with photo (XXXX chars)
echo    📥 Response: {"success":true,"action":"register",...}
echo.

echo TROUBLESHOOTING:
echo ========================================
echo.
echo IF NO MODE CHECKING MESSAGES:
echo - Check WiFi connection
echo - Verify server URL and API endpoint
echo - Check SSL certificate issues
echo.
echo IF HTTP ERROR CODES:
echo - 404: Wrong URL or route not found
echo - 500: Server error, check Laravel logs
echo - 0: Network connection failed
echo.
echo IF JSON PARSING ERRORS:
echo - Server returning non-JSON response
echo - Check Laravel API response format
echo.
echo IF MODE NOT CHANGING:
echo - Check Laravel cache system
echo - Verify POST request from frontend
echo - Check CSRF token issues
echo.

echo EXPECTED TIMELINE:
echo ========================================
echo.
echo 1. ESP32 boots and connects to WiFi (10s)
echo 2. Mode checking starts every 2 seconds
echo 3. User clicks "Mulai Deteksi" in Laravel
echo 4. Laravel sets mode to "register" in cache
echo 5. ESP32 detects mode change within 2 seconds
echo 6. LED flashes 3 times to indicate mode change
echo 7. User taps RFID card
echo 8. ESP32 processes card and sends to server
echo 9. Laravel responds with registration success
echo.

echo Run this test and monitor both ESP32 Serial Monitor
echo and Laravel logs to identify communication issues.
echo.

pause