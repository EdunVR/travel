@echo off
echo ========================================
echo DEPLOYING SDM MODAL PERMISSION FIX
echo ========================================
echo.

echo Step 1: Checking permissions in database...
php check_sdm_permissions_database.php
echo.

echo Step 2: Testing modal logic...
php test_sdm_modal_permissions.php
echo.

echo Step 3: Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Open browser and go to User Management ^> Roles
echo 2. Click Edit on any role
echo 3. Scroll to SDM section
echo 4. Verify all permissions appear:
echo    - Kepegawaian ^& Rekrutmen (6 permissions)
echo    - Manajemen Absensi (7 permissions)
echo    - Payroll (6 permissions)
echo    - Kinerja (4 permissions)
echo    - Kontrak (4 permissions)
echo.
pause
