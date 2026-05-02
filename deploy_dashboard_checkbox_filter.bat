@echo off
echo === DEPLOYING DASHBOARD CHECKBOX FILTER ===
echo.

echo Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Optimizing...
php artisan config:cache
php artisan route:cache

echo.
echo ✓ Dashboard checkbox filter deployed successfully!
echo.
echo Test the dashboard at: /admin
echo.
echo Features:
echo - Checkbox-based outlet selection
echo - Multiple outlet data filtering  
echo - Select all/clear all functionality
echo - Real-time data updates
echo.
echo Next: Test functionality and apply to other dashboards
echo.
pause