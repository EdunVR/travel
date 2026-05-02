@echo off
echo ========================================
echo Deploy Customer Template Export
echo ========================================
echo.

echo Checking files...
if exist "app\Exports\CustomerTemplateExport.php" (
    echo [OK] CustomerTemplateExport.php found
) else (
    echo [ERROR] CustomerTemplateExport.php NOT found!
    pause
    exit /b 1
)

if exist "app\Http\Controllers\CustomerManagementController.php" (
    echo [OK] CustomerManagementController.php found
) else (
    echo [ERROR] CustomerManagementController.php NOT found!
    pause
    exit /b 1
)

echo.
echo Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Dumping autoload...
composer dump-autoload

echo.
echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Upload app/Exports/CustomerTemplateExport.php to production
echo 2. Upload app/Http/Controllers/CustomerManagementController.php to production
echo 3. Run cache clear on production server
echo 4. Test: https://group.dahana-boiler.com/MORRA/admin/crm/pelanggan/download-template
echo.
pause
