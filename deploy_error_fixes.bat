@echo off
echo ===================================
echo   DEPLOYING ERROR FIXES
echo ===================================

echo.
echo 1. Clearing application cache...
php artisan cache:clear

echo.
echo 2. Clearing config cache...
php artisan config:clear

echo.
echo 3. Clearing view cache...
php artisan view:clear

echo.
echo 4. Clearing route cache...
php artisan route:clear

echo.
echo 5. Optimizing application...
php artisan optimize

echo.
echo ===================================
echo   FIXES DEPLOYED SUCCESSFULLY!
echo ===================================
echo.
echo Fixed Issues:
echo 1. CompanySetting type error - outlet ID conversion
echo 2. Missing edit view for permintaan barang
echo 3. Alpine.js modal function errors
echo 4. showApprovalModal and showRejectModal functions
echo.
echo Please test the following:
echo - Visit pengaturan page (company settings)
echo - Try editing a permintaan barang
echo - Test approval/reject modals
echo.
pause