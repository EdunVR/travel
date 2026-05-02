@echo off
echo === DEPLOYING DASHBOARD INVENTARIS CHECKBOX FILTER ===
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
echo ✓ Dashboard Inventaris checkbox filter deployed successfully!
echo.
echo Test the dashboard at: /admin/inventaris
echo.
echo Features implemented:
echo - Checkbox-based outlet selection
echo - Multiple outlet data filtering for:
echo   * KPI Stats (SKU, Outlets, Stock, Low Stock)
echo   * Outlet Summary
echo   * Low Stock Items
echo   * Recent Activities
echo   * Search functionality
echo - Select all/clear all functionality
echo - Real-time data updates
echo - Integrated with search bar
echo.
echo Next: Test functionality and continue to CRM Dashboard
echo.
pause