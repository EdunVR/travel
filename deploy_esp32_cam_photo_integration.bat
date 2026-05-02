@echo off
echo ========================================
echo ESP32 CAM Photo Integration Deployment
echo ========================================

echo.
echo 1. Running database migrations...
php artisan migrate --force

echo.
echo 2. Creating storage link for photos...
php artisan storage:link

echo.
echo 3. Creating attendance photos directory...
if not exist "storage\app\public\attendance_photos" mkdir "storage\app\public\attendance_photos"

echo.
echo 4. Setting permissions for photo directory...
icacls "storage\app\public\attendance_photos" /grant Everyone:F /T

echo.
echo 5. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo ========================================
echo ESP32 CAM Photo Integration Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Upload the updated ESP32_CAM_RFID_Laravel.ino to your ESP32 CAM
echo 2. Make sure to install the base64 library in Arduino IDE
echo 3. Test RFID card detection with photo capture
echo 4. Check the attendance photos in storage/app/public/attendance_photos
echo.
echo RFID API Endpoints:
echo - GET  /api/morra/api/rfid/mode
echo - POST /api/morra/api/rfid/mode
echo - POST /api/morra/api/rfid/card-detected
echo - POST /api/morra/api/rfid/register
echo.
pause