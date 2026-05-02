@echo off
echo ===== DEPLOYING INVESTOR MODULE FIXES =====

echo.
echo 1. Clearing route cache...
php artisan route:clear

echo.
echo 2. Clearing config cache...
php artisan config:clear

echo.
echo 3. Clearing view cache...
php artisan view:clear

echo.
echo 4. Testing route registration...
php artisan route:list --name=admin.investor

echo.
echo 5. Testing direct route access...
php test_investor_controllers.php

echo.
echo ===== DEPLOYMENT COMPLETE =====
echo.
echo If routes show as /admin/investor-admin/profil, the fix is working.
echo If routes show as /investor-admin/profil, there's still an issue.
echo.
pause