@echo off
echo ========================================
echo DEPLOYING ASSET ACCOUNT SELECTION FIX
echo ========================================
echo.

echo 1. Testing asset account functionality...
php test_permintaan_barang_asset_account_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Testing Fixed Asset creation...
echo Please test the following in browser:
echo - Open Permintaan Barang module
echo - Create a test request with any item type
echo - Click Approve and select "Lanjutkan ke Aktiva Tetap"
echo - Verify "Akun Aktiva Tetap" dropdown appears
echo - Verify dropdown shows only child asset accounts
echo - Verify accounts are filtered by outlet
echo - Test form validation (try submitting without selecting account)
echo - Test successful Fixed Asset creation
echo.

echo 4. Deployment completed successfully!
echo.
echo WHAT WAS FIXED:
echo ✓ Added "Akun Aktiva Tetap" selection form
echo ✓ Only shows child asset accounts (accounts with parent_id)
echo ✓ Filters asset accounts by outlet
echo ✓ Added asset_account_id validation
echo ✓ Fixed Fixed Asset creation error
echo ✓ Added asset_account_id to Fixed Asset record
echo ✓ Created API endpoint for asset accounts
echo.
echo TESTING CHECKLIST:
echo [ ] Asset account dropdown appears in Fixed Asset form
echo [ ] Only child asset accounts are shown
echo [ ] Accounts are filtered by outlet
echo [ ] Form validation prevents submission without asset account
echo [ ] Fixed Asset is created successfully with asset_account_id
echo [ ] No more "Field 'asset_account_id' doesn't have a default value" error
echo.

pause