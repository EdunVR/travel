@echo off
echo ========================================
echo DEPLOYING AUTO-CALCULATE SALVAGE VALUE
echo ========================================

echo.
echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Testing auto-calculate functionality...
php test_fixed_asset_auto_calculate_salvage.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED!
echo ========================================
echo.
echo FEATURES ADDED:
echo - Auto-calculate salvage value (10%% of acquisition cost)
echo - Toggle button to enable/disable auto-calculation
echo - Manual input option for custom salvage values
echo - Backend validation and auto-calculation
echo - Real-time calculation on form input
echo.
echo NEXT STEPS:
echo 1. Test creating new fixed asset
echo 2. Verify auto-calculation works
echo 3. Test toggle functionality
echo 4. Test manual input mode
echo.
pause