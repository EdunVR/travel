@echo off
echo ========================================
echo DEPLOYING RFID INTEGRATION
echo ========================================

echo.
echo [1/4] Running migrations...
php artisan migrate --path=database/migrations/2026_01_13_092708_add_rfid_uid_to_recruitments_table.php --force
php artisan migrate --path=database/migrations/2026_01_13_092805_create_system_settings_table.php --force
php artisan migrate --path=database/migrations/2026_01_13_092842_add_rfid_uid_to_attendances_table.php --force

echo.
echo [2/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [3/4] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [4/4] Setting initial RFID mode...
php artisan tinker --execute="DB::table('system_settings')->updateOrInsert(['key' => 'esp32_rfid_mode'], ['value' => 'attendance', 'description' => 'ESP32 CAM RFID Mode', 'updated_at' => now()]);"

echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Upload ESP32_CAM_RFID_Laravel.ino to your ESP32 CAM
echo 2. Update serverURL in ESP32 code with your Laravel server IP
echo 3. Test RFID detection in recruitment modal
echo.
echo API ENDPOINTS AVAILABLE:
echo - GET  /api/morra/api/rfid/mode
echo - POST /api/morra/api/rfid/mode
echo - POST /api/morra/api/rfid/card-detected
echo - GET  /api/detected-rfid-uid
echo.
pause