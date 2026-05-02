@echo off
echo ========================================
echo Testing Chat Input Field
echo ========================================
echo.

echo Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo ========================================
echo Checking chat-panel.js file
echo ========================================
if exist "public\js\chat-panel.js" (
    echo ✓ File exists: public\js\chat-panel.js
    dir "public\js\chat-panel.js"
) else (
    echo ✗ File NOT found: public\js\chat-panel.js
    echo ERROR: Please create the file first!
    pause
    exit /b 1
)
echo.

echo ========================================
echo Next Steps:
echo ========================================
echo 1. Hard refresh browser (Ctrl+Shift+R)
echo 2. Open Developer Console (F12)
echo 3. Look for these logs:
echo    ✓ Chat Panel Alpine component initializing...
echo    ✓ Chat Panel init() called
echo    ✓ Initial state: { connectionStatus: "online", ... }
echo.
echo 4. Click chat button
echo 5. Try typing in input field
echo 6. Input should NOT be disabled
echo.

echo ========================================
echo Troubleshooting:
echo ========================================
echo If input still disabled, check console for:
echo - connectionStatus value (should be "online")
echo - isSending value (should be false)
echo - Any JavaScript errors
echo.

pause
