@echo off
echo ========================================
echo DEPLOY INVENTORY & SPAREPART OUTLET FILTER
echo ========================================
echo.

echo Step 1: Backup Database
echo ------------------------
echo Please backup your database before proceeding!
echo Run: php artisan backup:run
echo Or: mysqldump -u username -p database_name ^> backup.sql
echo.
pause

echo.
echo Step 2: Run Migration
echo ---------------------
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migration failed!
    pause
    exit /b 1
)
echo Migration completed successfully!
echo.

echo Step 3: Verify Migration
echo ------------------------
php artisan tinker --execute="echo 'Checking spareparts table...'; echo Schema::hasColumn('spareparts', 'outlet_id') ? 'outlet_id column exists' : 'outlet_id column NOT found';"
echo.

echo Step 4: Clear Cache
echo -------------------
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared!
echo.

echo Step 5: Verify Data
echo -------------------
echo Please run this SQL to verify:
echo SELECT COUNT(*) as total, COUNT(outlet_id) as with_outlet FROM spareparts;
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next Steps:
echo 1. Test with superadmin account
echo 2. Test with limited user account
echo 3. Verify outlet filtering works
echo 4. Check for any errors in logs
echo.
echo See: INVENTORY_SPAREPART_OUTLET_FILTER_COMPLETE.md
echo.
pause
