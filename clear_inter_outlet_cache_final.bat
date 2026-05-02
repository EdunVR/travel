@echo off
echo 🧹 CLEARING INTER OUTLET CACHE - FINAL FIX
echo ==========================================

echo.
echo 📋 Step 1: Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 📋 Step 2: Clear compiled views
if exist "storage/framework/views/*.php" (
    del /q "storage\framework\views\*.php"
    echo ✅ Compiled views cleared
) else (
    echo ℹ️  No compiled views to clear
)

echo.
echo 📋 Step 3: Clear session files
if exist "storage/framework/sessions/*" (
    del /q "storage\framework\sessions\*"
    echo ✅ Session files cleared
) else (
    echo ℹ️  No session files to clear
)

echo.
echo 📋 Step 4: Verify JavaScript file
if exist "public/js/inter-outlet.js" (
    echo ✅ JavaScript file exists
    findstr /C:"window.routes?.interOutletPrint" "public/js/inter-outlet.js" >nul
    if %errorlevel%==0 (
        echo ✅ JavaScript uses route helper
    ) else (
        echo ❌ JavaScript still uses hardcoded URL
    )
) else (
    echo ❌ JavaScript file missing
)

echo.
echo 📋 Step 5: Test route generation
php test_route_generation.php

echo.
echo 🎯 NEXT STEPS:
echo 1. Hard refresh browser (Ctrl+Shift+R or Ctrl+F5)
echo 2. Clear browser cache completely
echo 3. Test print functionality
echo 4. Check browser console for correct URL

echo.
echo ✅ Cache clearing complete!
pause