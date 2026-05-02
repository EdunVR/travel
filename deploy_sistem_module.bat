@echo off
echo ========================================
echo   DEPLOYING SISTEM MODULE
echo ========================================

echo.
echo [1/5] Running Sistem Permission Seeder...
php artisan db:seed --class=SistemPermissionSeeder

echo.
echo [2/5] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [3/5] Creating storage directories...
php artisan storage:link
if not exist "storage\app\backups" mkdir "storage\app\backups"

echo.
echo [4/5] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [5/5] Running final checks...
php artisan about

echo.
echo ========================================
echo   SISTEM MODULE DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo You can now access:
echo - Sistem Dashboard: /admin/sistem
echo - Company Settings: /admin/sistem/pengaturan
echo.
echo Features available:
echo - System Information
echo - Database Backup & Restore
echo - Cache Management
echo - Database Optimization
echo - Company Settings Management
echo.
pause