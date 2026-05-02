@echo off
echo.
echo ============================================
echo Fix Travel Menu - Complete Cache Clear
echo ============================================
echo.

echo Step 1: Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
echo [OK] All caches cleared!

echo.
echo Step 2: Rebuilding caches...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo [OK] Caches rebuilt!

echo.
echo Step 3: Verifying permissions...
php check-user-permissions.php

echo.
echo ============================================
echo Fix Complete!
echo ============================================
echo.
echo IMPORTANT: You MUST do these steps:
echo    1. Close ALL browser tabs of the application
echo    2. Clear browser cache (Ctrl+Shift+Delete)
echo    3. Open application in NEW browser tab
echo    4. Login again
echo    5. Check sidebar for 'Travel Management' menu
echo.
echo If still not showing:
echo    - Check browser console (F12) for JavaScript errors
echo    - Try different browser (Chrome, Firefox, Edge)
echo    - Check if you're logged in as correct user
echo.

pause
