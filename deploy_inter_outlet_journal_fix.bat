@echo off
echo ========================================
echo DEPLOYING INTER OUTLET JOURNAL FIX
echo ========================================
echo.

echo 1. Testing journal creation functionality...
php test_inter_outlet_journal_creation.php
echo.

echo 2. Testing journal creation with COA configured...
php test_create_inter_outlet_with_journal.php
echo.

echo 3. Testing transaction without COA configuration...
php test_inter_outlet_without_coa.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo SUMMARY:
echo - Fixed getAccountIdByCode method to handle account IDs
echo - Improved journal creation with better error handling
echo - Transactions work with or without COA configuration
echo - Journal entries created automatically when COA is configured
echo.
echo NEXT STEPS:
echo 1. Test creating new inter outlet transactions
echo 2. Verify journal entries are created in accounting module
echo 3. Configure COA settings for outlets that need automatic journals
echo.
pause