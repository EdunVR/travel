@echo off
echo 🚀 Deploying timezone, date format, and piutang double fix...
echo.

echo 📦 1. Running composer dump-autoload...
composer dump-autoload
if %errorlevel% neq 0 (
    echo ❌ Composer dump-autoload failed
    pause
    exit /b 1
)
echo ✅ Composer autoload updated
echo.

echo 🗄️ 2. Running database migration...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ❌ Migration failed
    pause
    exit /b 1
)
echo ✅ Migration completed
echo.

echo 🧹 3. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo ✅ Cache cleared
echo.

echo 🔧 4. Running fix script...
php fix_timezone_date_piutang.php
echo ✅ Fix script completed
echo.

echo 🧪 5. Running test script...
php test_timezone_date_piutang_fix.php
echo ✅ Test script completed
echo.

echo 📋 6. Summary of changes:
echo ✅ Timezone set to Asia/Jakarta (WIB)
echo ✅ Date format standardized to DD/MM/YYYY
echo ✅ Piutang double prevention implemented
echo ✅ Unique constraint added to piutang table
echo ✅ POS controller updated with validation
echo ✅ DateHelper created for consistent formatting
echo ✅ JavaScript helper added for frontend
echo.

echo 🎉 Deployment completed successfully!
echo.
echo 📝 Next steps:
echo 1. Test POS transactions with BON to ensure no duplicates
echo 2. Check all ERP pages use DD/MM/YYYY format
echo 3. Monitor logs for timezone issues
echo 4. Update other views to use DateHelper
echo.

pause