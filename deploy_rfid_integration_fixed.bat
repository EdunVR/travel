@echo off
echo ========================================
echo DEPLOYING RFID INTEGRATION - FIXED
echo ========================================

echo.
echo [1/5] Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/5] Running migrations (if needed)...
php artisan migrate --path=database/migrations/2026_01_13_092708_add_rfid_uid_to_recruitments_table.php --force
php artisan migrate --path=database/migrations/2026_01_13_092805_create_system_settings_table.php --force
php artisan migrate --path=database/migrations/2026_01_13_092842_add_rfid_uid_to_attendances_table.php --force

echo.
echo [3/5] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [4/5] Setting initial RFID mode...
php artisan tinker --execute="DB::table('system_settings')->updateOrInsert(['key' => 'esp32_rfid_mode'], ['value' => 'attendance', 'description' => 'ESP32 CAM RFID Mode', 'updated_at' => now()]);"

echo.
echo [5/5] Testing API endpoints...
php test_rfid_api_manual.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETE - FIXED!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Upload ESP32_CAM_RFID_Laravel_Simple.ino to your ESP32 CAM
echo 2. Update WiFi credentials in ESP32 code:
echo    - const char* ssid = "YOUR_WIFI_SSID";
echo    - const char* password = "YOUR_WIFI_PASSWORD";
echo 3. Test RFID detection in recruitment modal
echo.
echo CORRECT API ENDPOINTS:
echo - GET  https://poshan.my.id/tofu/api/morra/api/rfid/mode
echo - POST https://poshan.my.id/tofu/api/morra/api/rfid/mode
echo - POST https://poshan.my.id/tofu/api/morra/api/rfid/card-detected
echo - GET  https://poshan.my.id/tofu/api/detected-rfid-uid
echo.
echo ESP32 SERVER URL: https://poshan.my.id/tofu
echo.
pause