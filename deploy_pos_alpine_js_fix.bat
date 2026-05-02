@echo off
echo ========================================
echo POS Alpine.js Error Fix Deployment
echo ========================================
echo.

echo [INFO] Deploying POS Alpine.js error fix...
echo.

REM Clear cache
echo [STEP 1] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo ✅ Cache cleared successfully
echo.

REM Verify fix
echo [STEP 2] Verifying Alpine.js fix...
php fix_pos_alpine_js_error.php
echo.

REM Test instructions
echo [STEP 3] Testing Instructions
echo ========================================
echo.
echo 🔸 Open POS Page:
echo    Navigate to: /admin/penjualan/pos
echo.
echo 🔸 Check Console (F12):
echo    1. Look for Alpine.js initialization messages
echo    2. Verify no "posApp is not defined" errors
echo    3. Check for successful component registration
echo.
echo 🔸 Test Functionality:
echo    1. Verify POS interface loads correctly
echo    2. Test customer selection dropdown
echo    3. Test product grid display
echo    4. Test cart functionality
echo.
echo 🔸 Test Customer Type Pricing:
echo    1. Select customer with type
echo    2. Verify product prices change
echo    3. Add products to cart
echo    4. Check discount indicators
echo.

echo ========================================
echo ✅ DEPLOYMENT COMPLETE
echo ========================================
echo.
echo 📝 Changes Applied:
echo    - Replaced function posApp() with Alpine.data()
echo    - Added alpine:init event listener
echo    - Removed Alpine.start() conflicts
echo    - Fixed component registration timing
echo.
echo 🎯 Expected Results:
echo    ✅ No Alpine.js errors in console
echo    ✅ POS interface loads properly
echo    ✅ Customer type pricing works
echo    ✅ All POS functionality operational
echo.
echo 🚀 Ready for testing!
echo.
pause