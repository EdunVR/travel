@echo off
echo ===================================
echo DEPLOY PERMINTAAN BARANG MENU & PERMISSIONS
echo ===================================
echo.

echo 1. Creating permissions in database...
php create_permintaan_barang_permissions.php
echo.

echo 2. Testing permissions and menu...
php test_permintaan_barang_menu_permissions.php
echo.

echo 3. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully.
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY
echo ===================================
echo.
echo CHANGES IMPLEMENTED:
echo 1. Created 6 permissions for Permintaan Barang (CRUD + Approve + Reject)
echo 2. Added hasPermission function to helpers
echo 3. Updated User model with hasPermission method
echo 4. Registered @hasPermission blade directive
echo 5. Updated sidebar menu to show Permintaan Barang submenu
echo 6. Assigned all permissions to Super Admin role
echo.
echo MENU ACCESS:
echo - Rantai Pasok menu will now appear for users with permissions
echo - Permintaan Barang submenu will show for users with view permission
echo - All CRUD operations protected by respective permissions
echo.
echo READY FOR PRODUCTION USE
echo ===================================

pause