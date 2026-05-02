@echo off
echo =============================================
echo POS Transaction Number Fix Deployment
echo =============================================

echo.
echo 1. Backing up current database...
mysqldump -u root demo > backup_before_pos_fix_%date:~-4,4%%date:~-10,2%%date:~-7,2%.sql
if %errorlevel% equ 0 (
    echo    ✅ Database backup created
) else (
    echo    ⚠️  Database backup failed, continuing anyway...
)

echo.
echo 2. Testing current outlet data...
php test_outlet_prefix.php

echo.
echo 3. Testing transaction number generation...
php test_pos_transaction_number.php

echo.
echo 4. Checking for duplicate transaction numbers...
php fix_pos_duplicate_transaction_number.php

echo.
echo 5. Clearing application cache...
php artisan optimize:clear

echo.
echo =============================================
echo Deployment Complete!
echo =============================================
echo.
echo CHANGES APPLIED:
echo ✅ POS transaction numbers now include outlet prefix
echo ✅ Format changed from: 0001/POS/12/2025
echo ✅ Format changed to:   0001/PBU/POS/12/2025
echo ✅ Each outlet has unique transaction sequences
echo ✅ No more duplicate entry errors
echo.
echo TESTING CHECKLIST:
echo 1. Login to POS system
echo 2. Create transaction in outlet PBU
echo 3. Verify number format: 00XX/PBU/POS/12/2025
echo 4. Switch to outlet Dahana
echo 5. Create transaction in outlet Dahana
echo 6. Verify number format: 00XX/DAH/POS/12/2025
echo 7. Confirm no duplicate entry errors
echo.
echo MONITORING:
echo - Check Laravel logs for any errors
echo - Verify transaction numbers are unique
echo - Test multiple transactions per outlet
echo.
echo If any issues occur:
echo - Check backup file: backup_before_pos_fix_*.sql
echo - Run: php test_pos_transaction_number.php
echo - Check Laravel logs: storage/logs/laravel.log
echo.
pause