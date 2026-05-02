@echo off
echo 🚀 [DEPLOY] Deploying POS Alpine.js Error Fix...
echo.

echo 📋 [DEPLOY] Step 1: Running fix script...
php fix_pos_alpine_js_error.php
echo.

echo 🧹 [DEPLOY] Step 2: Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 🔄 [DEPLOY] Step 3: Optimizing for production...
php artisan config:cache
php artisan view:cache
echo.

echo ✅ [DEPLOY] POS Alpine.js Error Fix Deployed Successfully!
echo.
echo 📋 [NEXT STEPS]:
echo 1. Clear browser cache (Ctrl+F5)
echo 2. Test POS page: /admin/penjualan/pos
echo 3. Check browser console for any remaining errors
echo.
echo 🔍 [TROUBLESHOOTING]:
echo - If errors persist, check browser developer tools
echo - Verify pos.js file is loading correctly
echo - Ensure Alpine.js CDN is accessible
echo.
pause