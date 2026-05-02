@echo off
echo ========================================
echo Testing OpenWA API
echo ========================================
echo.

set API_KEY=hm-tour-secret-key-2026
set BASE_URL=http://localhost:3000

echo 1. Testing Health Check...
curl -s %BASE_URL%/health
echo.
echo.

echo 2. Testing Status (requires API key)...
curl -s -H "X-API-Key: %API_KEY%" %BASE_URL%/status
echo.
echo.

echo 3. Testing Send Message...
set /p PHONE="Enter phone number (e.g., 08123456789): "
curl -s -X POST %BASE_URL%/send-message ^
  -H "X-API-Key: %API_KEY%" ^
  -H "Content-Type: application/json" ^
  -d "{\"phone\":\"%PHONE%\",\"message\":\"Test message from HM Tour OpenWA Server\"}"
echo.
echo.

echo ========================================
echo Test Complete!
echo ========================================
pause
