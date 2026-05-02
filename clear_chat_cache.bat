@echo off
echo ========================================
echo  Clear Chat System Cache
echo ========================================
echo.

echo [1/4] Clearing Laravel cache...
php artisan cache:clear

echo.
echo [2/4] Clearing view cache...
php artisan view:clear

echo.
echo [3/4] Clearing config cache...
php artisan config:clear

echo.
echo [4/4] Clearing route cache...
php artisan route:clear

echo.
echo ========================================
echo  Cache cleared successfully!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Clear browser cache (Ctrl + Shift + Delete)
echo 2. Hard refresh page (Ctrl + F5)
echo 3. Test chat system
echo.
pause
