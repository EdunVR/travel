@echo off
echo ===================================
echo DEPLOYING INTER OUTLET AUTO APPROVED FINAL
echo ===================================
echo.

echo 1. Testing all fixes...
php test_inter_outlet_auto_approved_and_stock.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
echo.

echo 3. Clearing config cache...
php artisan config:clear
echo.

echo 4. Clearing route cache...
php artisan route:clear
echo.

echo 5. Clearing view cache...
php artisan view:clear
echo.

echo 6. Optimizing application...
php artisan optimize
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo MAJOR CHANGES IMPLEMENTED:
echo 1. Transactions now automatically approved on creation
echo 2. Stock automatically updated in both outlets
echo 3. Print invoice URL fixed
echo 4. Approve/Delete buttons removed from history
echo.
echo NEW WORKFLOW:
echo 1. Create transaction → Automatically approved
echo 2. Stock reduced from source outlet
echo 3. Stock added to destination outlet  
echo 4. Journal entry created
echo 5. Ready to print invoice
echo.
echo NO MORE MANUAL APPROVAL NEEDED!
echo.
pause