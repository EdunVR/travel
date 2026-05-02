@echo off
echo ===== DEPLOYING PRODUCTION REALIZATION AND EDIT FIXES =====
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Fixes applied:
echo    - Fixed ProductionRealization::create() to only use existing table columns
echo    - Removed material_cost, realization_details, created_by from create calls
echo    - Updated ProductionRealization model fillable array
echo    - Enhanced realization details storage in notes field as JSON
echo    - Improved edit method data loading and transformation

echo.
echo 3. Testing production realization functionality...
echo    You can now test:
echo    - Creating multi-product realizations
echo    - Editing production records
echo    - Viewing production data with all relationships

echo.
echo ===== DEPLOYMENT COMPLETED =====
echo.
echo Next steps:
echo 1. Test multi-product realization creation
echo 2. Test production edit functionality
echo 3. Verify all data loads correctly in edit mode
echo.
pause