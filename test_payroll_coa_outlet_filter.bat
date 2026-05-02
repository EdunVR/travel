@echo off
echo ========================================
echo QUICK TEST: Payroll COA Outlet Filter
echo ========================================
echo.

echo Testing the new Payroll COA Setting features:
echo 1. Outlet-specific account filtering
echo 2. Hide parent accounts (show only leaf accounts)
echo 3. Dynamic account loading
echo.

echo [1/3] Testing API endpoints...
echo.

echo Testing getAccounts endpoint...
curl -X GET "http://localhost:8000/sdm/payroll/coa-settings/accounts?outlet_id=1" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo Testing getSettings endpoint...
curl -X GET "http://localhost:8000/sdm/payroll/coa-settings/get?outlet_id=1" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo [2/3] Testing validation...
echo.

echo Testing without outlet_id (should return 422)...
curl -X GET "http://localhost:8000/sdm/payroll/coa-settings/accounts" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo Testing with invalid outlet_id (should return error)...
curl -X GET "http://localhost:8000/sdm/payroll/coa-settings/accounts?outlet_id=99999" ^
     -H "Accept: application/json" ^
     -H "X-Requested-With: XMLHttpRequest"
echo.
echo.

echo [3/3] Running PHP test script...
php test_payroll_coa_outlet_filter.php
echo.

echo ========================================
echo MANUAL TESTING CHECKLIST:
echo ========================================
echo.
echo □ 1. Open browser: http://localhost:8000/sdm/payroll/coa-settings
echo □ 2. Login with user that has multiple outlet access
echo □ 3. Select Outlet A - verify accounts load
echo □ 4. Select Outlet B - verify different accounts load
echo □ 5. Verify no parent accounts appear in dropdowns
echo □ 6. Fill form and save settings
echo □ 7. Refresh page and verify settings persist
echo □ 8. Check browser console for errors
echo □ 9. Test with user with limited outlet access
echo □ 10. Verify loading indicator appears when changing outlets
echo.

echo ========================================
echo FILES MODIFIED:
echo ========================================
echo - app/Http/Controllers/PayrollCoaSettingController.php
echo - routes/web.php
echo - resources/views/admin/sdm/payroll/coa-settings.blade.php
echo.

echo ========================================
echo FEATURES IMPLEMENTED:
echo ========================================
echo ✓ Outlet-specific account filtering
echo ✓ Hide parent accounts (show only leaf accounts)
echo ✓ Dynamic account loading via AJAX
echo ✓ Loading indicator
echo ✓ Outlet access validation
echo ✓ Improved user experience
echo.

pause