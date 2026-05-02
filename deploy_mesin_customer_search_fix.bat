@echo off
echo 🚀 Deploying Mesin Customer Search and Outlet Filter Fixes
echo ============================================================

echo.
echo 📋 CHANGES BEING DEPLOYED:
echo - Fixed customer search API response format
echo - Removed "Outlet: Semua" option from filter
echo - Set default outlet to first available outlet
echo - Added debug logging for troubleshooting
echo - Enhanced outlet change to refresh all data
echo.

echo 🔄 Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 🧪 Running verification test...
php test_mesin_customer_search_simple.php

echo.
echo ✅ DEPLOYMENT COMPLETED!
echo.
echo 🎯 NEXT STEPS:
echo 1. Open browser and navigate to Mesin Customer page
echo 2. Clear browser cache (Ctrl+F5)
echo 3. Test customer search functionality
echo 4. Verify outlet filter shows only available outlets
echo 5. Check browser console for any errors
echo.
echo 📞 If issues persist, check:
echo - Browser console for JavaScript errors
echo - Network tab for API response format
echo - Laravel logs for backend errors
echo.
pause