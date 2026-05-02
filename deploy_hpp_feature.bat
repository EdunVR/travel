@echo off
echo ========================================
echo DEPLOYING HPP FEATURE FOR PRODUK
echo ========================================

echo.
echo 1. Creating HPP permission...
php create_hpp_permission.php

echo.
echo 2. Testing implementation...
php test_hpp_implementation.php

echo.
echo 3. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo WHAT'S NEW:
echo - Added HPP button to product cards (only visible to users with permission)
echo - Created HPP modal for managing stock and HPP history
echo - Added permission 'inventaris.produk.hpp' (assigned to Super Admin)
echo - Added controller methods for HPP management
echo - Added routes for HPP operations
echo.
echo NEXT STEPS:
echo 1. Login as Super Admin
echo 2. Go to Inventaris ^> Produk
echo 3. Click HPP button on any product card
echo 4. Test adding/viewing HPP data
echo.
echo To assign HPP permission to other roles:
echo 1. Go to User Management ^> Roles
echo 2. Edit the desired role
echo 3. Check "HPP" under Inventaris ^> Produk section
echo.
pause