@echo off
echo ========================================
echo DEPLOYING SUPPLY CHAIN & SDM PERMISSIONS
echo ========================================
echo.

echo Step 1: Creating permissions...
php create_supply_chain_sdm_permissions.php
echo.

echo Step 2: Verifying permissions...
php check_supply_chain_sdm_permissions.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Clear cache: php artisan cache:clear
echo 2. Test permissions in Role Management modal
echo 3. Test button visibility in each module
echo.
pause
