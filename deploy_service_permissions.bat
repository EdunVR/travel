@echo off
echo ========================================
echo DEPLOYING SERVICE MANAGEMENT PERMISSIONS
echo ========================================
echo.

echo Step 1: Creating permissions...
php create_service_permissions.php
echo.

echo Step 2: Verifying permissions...
php check_service_permissions.php
echo.

echo Step 3: Testing modal logic...
php test_service_modal_permissions.php
echo.

echo Step 4: Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Open browser: User Management ^> Roles
echo 2. Click Edit on any role
echo 3. Scroll to Service Management section
echo 4. Verify all 4 submenus with 5 permissions each
echo.
echo Total: 20 permissions created
echo - Invoice Service (5)
echo - History Service (5)
echo - Ongkir Service (5)
echo - Mesin Customer (5)
echo.
pause
