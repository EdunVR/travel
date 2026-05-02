@echo off
echo ========================================
echo QUICK TEST: Chart of Account Generate Code Fix
echo ========================================
echo.

echo Testing the fixed generateAccountCode functionality...
echo.

echo [1/3] Testing API endpoints...
echo.

echo Testing generate code for asset account...
curl -X GET "http://localhost:8000/finance/chart-of-accounts/generate-code?outlet_id=1&type=asset" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo Testing generate code for liability account...
curl -X GET "http://localhost:8000/finance/chart-of-accounts/generate-code?outlet_id=1&type=liability" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo Testing generate code for expense account...
curl -X GET "http://localhost:8000/finance/chart-of-accounts/generate-code?outlet_id=1&type=expense" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo [2/3] Testing validation...
echo.

echo Testing without outlet_id (should return 422)...
curl -X GET "http://localhost:8000/finance/chart-of-accounts/generate-code?type=asset" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo [3/3] Running PHP test script...
php test_chart_of_account_generate_code.php
echo.

echo ========================================
echo MANUAL TESTING CHECKLIST:
echo ========================================
echo.
echo □ 1. Open browser: http://localhost:8000/finance/chart-of-accounts
echo □ 2. Click "Tambah Akun" button
echo □ 3. Select outlet from dropdown
echo □ 4. Select account type (Asset, Liability, Expense, etc.)
echo □ 5. Verify account code is generated automatically
echo □ 6. Try different account types and verify different prefixes
echo □ 7. Try creating child accounts under parent accounts
echo □ 8. Check browser console for any JavaScript errors
echo □ 9. Verify generated codes follow proper numbering sequence
echo □ 10. Test with different outlets
echo.

echo ========================================
echo EXPECTED BEHAVIOR:
echo ========================================
echo.
echo ✓ Asset accounts: 1001, 1002, 1003, etc.
echo ✓ Liability accounts: 2001, 2002, 2003, etc.
echo ✓ Expense accounts: 5001, 5002, 5003, etc.
echo ✓ Child accounts: {parent}.001, {parent}.002, etc.
echo ✓ No more "Call to undefined method" errors
echo ✓ Proper outlet access validation
echo ✓ Sequential numbering with padding
echo.

echo ========================================
echo FIXED ISSUES:
echo ========================================
echo.
echo ✓ Call to undefined method App\Models\ChartOfAccount::generateAccountCode()
echo ✓ Added ChartOfAccountService dependency injection
echo ✓ Implemented database-based code generation logic
echo ✓ Added outlet access validation
echo ✓ Added type-based prefixes (1=asset, 2=liability, etc.)
echo ✓ Improved error handling and logging
echo ✓ Added support for parent-child account hierarchy
echo.

pause