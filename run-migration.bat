@echo off
echo ========================================
echo Running Migration for DP and Commission
echo ========================================
echo.

echo Step 1: Check migration status...
php artisan migrate:status
echo.

echo Step 2: Running migration...
php artisan migrate --force
echo.

echo Step 3: Verify migration...
php artisan migrate:status
echo.

echo Step 4: Running test script...
php test-dp-and-commission.php
echo.

echo ========================================
echo Migration Complete!
echo ========================================
pause
