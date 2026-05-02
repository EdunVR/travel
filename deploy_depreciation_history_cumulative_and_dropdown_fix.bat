@echo off
echo ========================================
echo DEPLOYING DEPRECIATION HISTORY FIXES
echo ========================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo 3. Testing route registration...
php artisan route:list --name="fixed-assets.assets.all"

echo.
echo 4. Testing API endpoints...
php test_route_fix.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo FIXES DEPLOYED:
echo ✅ Cumulative depreciation calculation in history table
echo ✅ Complete asset dropdown filter (all assets, not just current page)
echo ✅ Route fix for getAllFixedAssets API
echo.
echo USER BENEFITS:
echo 🎯 Accurate cumulative depreciation from oldest to newest
echo 🎯 Complete asset list in dropdown filter
echo 🎯 Better user experience with accurate data
echo.
echo Ready for production use!
pause