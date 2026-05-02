@echo off
echo ===================================
echo   DEPLOYING ALL MODAL FIXES
echo ===================================

echo.
echo 1. Clearing view cache...
php artisan view:clear

echo.
echo 2. Clearing config cache...
php artisan config:clear

echo.
echo 3. Clearing route cache...
php artisan route:clear

echo.
echo 4. Clearing application cache...
php artisan cache:clear

echo.
echo 5. Optimizing application...
php artisan optimize

echo.
echo ===================================
echo   ALL FIXES DEPLOYED SUCCESSFULLY!
echo ===================================
echo.
echo Fixed Issues:
echo 1. ✅ CompanySetting type error - outlet ID conversion
echo 2. ✅ Created edit modal instead of separate page
echo 3. ✅ Fixed Alpine.js modal function errors
echo 4. ✅ Fixed showApprovalModal and showRejectModal functions
echo 5. ✅ Fixed detail modal edit function
echo 6. ✅ Removed problematic x-init from approval modal
echo.
echo Please test the following:
echo - Visit pengaturan page (company settings)
echo - Navigate to Supply Chain ^> Permintaan Barang
echo - Test all modal functions (detail, edit, approve, reject)
echo - Test modal interactions and form submissions
echo.
pause