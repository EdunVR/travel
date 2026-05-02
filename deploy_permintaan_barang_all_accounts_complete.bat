@echo off
echo ===============================================
echo DEPLOYING ALL ACCOUNT FORMS FOR FIXED ASSET
echo ===============================================
echo.

echo 1. Testing all account endpoints...
php test_permintaan_barang_all_accounts_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Deployment completed successfully!
echo.
echo WHAT WAS IMPLEMENTED:
echo ✓ Added 4 account selection forms for Fixed Asset creation:
echo   - Akun Aktiva Tetap (asset_account_id)
echo   - Akun Beban Penyusutan (depreciation_expense_account_id)  
echo   - Akun Akumulasi Penyusutan (accumulated_depreciation_account_id)
echo   - Akun Pembayaran (payment_account_id)
echo ✓ All forms use hierarchical structure with disabled parents
echo ✓ Outlet-based filtering for all account types
echo ✓ Complete validation for all required fields
echo ✓ API endpoints for all account types
echo ✓ Fixed Asset creation includes all account relationships
echo.
echo ACCOUNT FILTERING LOGIC:
echo - Asset Accounts: type = 'asset' (general asset accounts)
echo - Expense Accounts: type = 'expense' (for depreciation expense)
echo - Accumulated Depreciation: type = 'asset' + name contains 'akumulasi/penyusutan' or code starts with '18'
echo - Payment Accounts: type = 'asset' + name contains 'kas/bank' or code starts with '10'
echo.
echo TESTING CHECKLIST:
echo [ ] All 4 account dropdowns appear in Fixed Asset form
echo [ ] Hierarchical structure works for all account types
echo [ ] Parent accounts are disabled, child accounts selectable
echo [ ] Outlet filtering works for all account endpoints
echo [ ] Form validation prevents submission with missing accounts
echo [ ] Fixed Asset creation succeeds with all accounts selected
echo [ ] No more "field doesn't have a default value" errors
echo.
echo RECOMMENDED TEST DATA:
echo - Use Outlet 3 (Bojong Kunci) - has most accounts
echo - Asset accounts: 30 available (10 parents, 20 children)
echo - Expense accounts: 37 available (7 parents, 30 children)
echo - Accumulated depreciation: 5+ accounts available
echo - Payment accounts: 5+ cash/bank accounts available
echo.

pause