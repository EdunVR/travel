@echo off
echo ========================================
echo DEPLOYING INVESTOR DATATABLE FIXES
echo ========================================

echo.
echo [1/4] Clearing all caches...
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo.
echo [2/4] Caching routes...
php artisan route:cache

echo.
echo [3/4] Testing investor routes...
php artisan route:list --name=investor.profil.index
php artisan route:list --name=investor.bagi-hasil.index
php artisan route:list --name=investor.pencairan.index

echo.
echo [4/4] Deployment complete!
echo.
echo ========================================
echo INVESTOR DATATABLE FIXES DEPLOYED
echo ========================================
echo.
echo FIXES APPLIED:
echo 1. Fixed DataTables reinitialisation error in all investor views
echo 2. Added proper DataTable cleanup and destroy methods
echo 3. Enhanced DataTable styling with Tailwind CSS classes
echo 4. Added responsive design and better pagination
echo 5. Improved table structure rebuilding to prevent conflicts
echo.
echo The DataTables warning should now be resolved.
echo All investor pages should work without reinitialisation errors.
echo.

pause