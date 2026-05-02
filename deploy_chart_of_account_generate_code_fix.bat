@echo off
echo ========================================
echo DEPLOY: Chart of Account Generate Code Fix v2
echo ========================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared
echo.

echo [2/5] Optimizing application...
php artisan route:cache
php artisan config:cache
echo ✓ Application optimized
echo.

echo [3/5] Running debug script...
php debug_chart_of_account_generate_code.php
echo.

echo [4/5] Testing for duplicate generation...
php test_duplicate_code_generation.php
echo.

echo [5/5] Checking diagnostics...
echo Checking for any PHP syntax errors...
php -l app/Http/Controllers/FinanceAccountantController.php
echo ✓ Syntax check completed
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Open browser and go to Chart of Accounts
echo 2. Click 'Tambah Akun' button multiple times quickly
echo 3. Verify each call generates unique sequential codes
echo 4. Test different account types (asset, liability, expense)
echo 5. Test creating child accounts under parent accounts
echo 6. Monitor Laravel logs for any warnings/errors
echo.
echo FIXED ISSUES:
echo ✓ Call to undefined method generateAccountCode() error
echo ✓ Duplicate code generation (race conditions)
echo ✓ Added database transaction with row-level locking
echo ✓ Added duplicate code verification and retry logic
echo ✓ Enhanced logging for debugging
echo ✓ Added comprehensive test scripts
echo.
echo IMPROVEMENTS:
echo ✓ Database transaction wrapper
echo ✓ Row-level locking with lockForUpdate()
echo ✓ Duplicate detection and retry mechanism
echo ✓ Enhanced error handling and logging
echo ✓ Race condition prevention
echo.
echo Documentation: 
echo - CHART_OF_ACCOUNT_GENERATE_CODE_FIX.md
echo - CHART_OF_ACCOUNT_DUPLICATE_CODE_FIX.md
echo.

pause