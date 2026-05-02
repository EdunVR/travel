@echo off
echo ========================================
echo DEPLOYING INTER OUTLET TRANSACTION NUMBER FIX
echo ========================================
echo.

echo Step 1: Backing up current files...
if not exist "backup_inter_outlet_fix_%date:~-4,4%%date:~-10,2%%date:~-7,2%" mkdir "backup_inter_outlet_fix_%date:~-4,4%%date:~-10,2%%date:~-7,2%"
copy "app\Models\InterOutletSale.php" "backup_inter_outlet_fix_%date:~-4,4%%date:~-10,2%%date:~-7,2%\InterOutletSale.php.bak"
copy "app\Http\Controllers\InterOutletSaleController.php" "backup_inter_outlet_fix_%date:~-4,4%%date:~-10,2%%date:~-7,2%\InterOutletSaleController.php.bak"
echo ✅ Backup completed

echo.
echo Step 2: Testing current transaction number generation...
php test_inter_outlet_transaction_number_fix.php
echo.

echo Step 3: Fixing existing duplicate transaction numbers...
php fix_existing_duplicate_inter_outlet_transactions.php
echo.

echo Step 4: Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✅ Cache cleared

echo.
echo Step 5: Running final verification...
php test_inter_outlet_transaction_number_fix.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo 📋 Changes Applied:
echo ✅ Updated InterOutletSale::generateTransactionNumber() method
echo ✅ Added database locking to prevent race conditions  
echo ✅ Fixed date-specific sequence numbering
echo ✅ Added retry logic in controller for duplicate handling
echo ✅ Fixed existing duplicate transaction numbers
echo.
echo 🔄 Next Steps:
echo 1. Test inter outlet sale creation in browser
echo 2. Monitor logs for any remaining duplicate issues
echo 3. Test with multiple concurrent users if possible
echo.
echo Press any key to continue...
pause >nul