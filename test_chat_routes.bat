@echo off
echo ========================================
echo Testing Chat Routes
echo ========================================
echo.

echo Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo ========================================
echo Checking Chat Routes
echo ========================================
php artisan route:list --name=admin.chat
echo.

echo ========================================
echo Expected Routes:
echo ========================================
echo ✓ admin.chat.users
echo ✓ admin.chat.messages
echo ✓ admin.chat.send
echo ✓ admin.chat.mark-read
echo ✓ admin.chat.unread-count
echo.

echo ========================================
echo Next Steps:
echo ========================================
echo 1. Hard refresh browser (Ctrl+Shift+R)
echo 2. Open Developer Console (F12)
echo 3. Check Network tab
echo 4. Look for chat requests
echo 5. Verify all return 200 OK
echo.

pause
