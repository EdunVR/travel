@echo off
REM Script untuk deploy affiliate fee spesifik di production server
REM Jalankan di server: deploy-commands.bat

echo === DEPLOYMENT: Affiliate Fee Spesifik ===
echo.

REM 1. Check current directory
echo 1. Checking directory...
cd
echo.

REM 2. Pull latest changes (jika belum auto-deploy)
echo 2. Pulling latest changes...
git pull origin main
echo.

REM 3. Run migration
echo 3. Running migration...
php artisan migrate --force
echo.

REM 4. Clear all cache
echo 4. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo.

REM 5. Verify migration
echo 5. Verifying migration...
php artisan migrate:status | findstr "add_specific_affiliator_fee_settings"
echo.

echo === DEPLOYMENT COMPLETE ===
echo.
echo Next steps:
echo 1. Buka https://hmtourtravel.com/admin/inventaris/affiliate/hierarchy/tree
echo 2. Test fee setting untuk 2 downline dengan level sama
echo 3. Verifikasi fee tersimpan spesifik (tidak saling mempengaruhi)
echo.
pause
