@echo off
echo ========================================
echo REBUILDING TAILWIND CSS FOR DASHBOARD
echo ========================================

echo.
echo [1/4] Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [2/4] Rebuilding Tailwind CSS...
npm run build

echo.
echo [3/4] Clearing browser cache (optional)...
echo Please clear your browser cache (Ctrl+Shift+R or Ctrl+F5)

echo.
echo [4/4] Testing dashboard colors...
echo Please visit: %~dp0admin/dashboard

echo.
echo ========================================
echo REBUILD COMPLETE!
echo ========================================
echo.
echo If colors still don't show:
echo 1. Clear browser cache (Ctrl+Shift+R)
echo 2. Check browser developer tools for CSS errors
echo 3. Verify Tailwind build completed successfully
echo.
pause