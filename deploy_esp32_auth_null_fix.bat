@echo off
echo ========================================
echo ESP32 Authentication Null Error Fix
echo ========================================
echo.

echo Problem: Call to a member function outlets() on null
echo Cause: ESP32 API requests are unauthenticated, auth()->user() returns null
echo Solution: Check authentication before accessing user properties
echo.

echo 1. Testing the fix...
php test_esp32_unauthenticated_fix.php

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
echo ESP32 Authentication Fix Deployed!
echo ========================================
echo.
echo Key changes made:
echo - Added auth()->check() before accessing auth()->user()
echo - Use employee's outlet_id for unauthenticated ESP32 requests
echo - Set created_by to null for ESP32 requests
echo - Graceful handling of both web and API requests
echo.
echo The ESP32-CAM should now work without authentication errors!
echo.
pause