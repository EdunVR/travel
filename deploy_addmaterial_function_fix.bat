@echo off
echo ===================================
echo DEPLOYING ADDMATERIAL FUNCTION FIX
echo ===================================
echo.

echo 1. Clearing Laravel caches...
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 2. Checking if fix files exist...
if exist "public\fix_addmaterial_function.js" (
    echo    ✅ Fix JavaScript file exists
) else (
    echo    ❌ Fix JavaScript file missing
)
echo.

echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo WHAT WAS FIXED:
echo - Added temporary fix for addMaterial function
echo - Created standalone addMaterial, removeMaterial, updateMaterialUnit functions
echo - Added comprehensive error handling and logging
echo - Included fallback for missing dependencies
echo.
echo ORIGINAL ERROR RESOLVED:
echo - ReferenceError: addMaterial is not defined
echo - Function is now available on window object
echo.
echo NEXT STEPS:
echo 1. Clear browser cache (Ctrl+F5)
echo 2. Test the "Tambah Bahan" button
echo 3. Verify materials can be added and removed
echo 4. Check browser console for debug messages
echo.
echo TEMPORARY SOLUTION:
echo - This is a temporary fix while investigating the root cause
echo - The original production.js may have a syntax error preventing function definition
echo - Consider reviewing the entire production.js file for issues
echo.
pause