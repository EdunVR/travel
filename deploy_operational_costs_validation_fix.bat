@echo off
echo ===== DEPLOYING OPERATIONAL COSTS VALIDATION FIX =====
echo.

echo 1. Testing the validation fix...
php test_operational_costs_validation_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 3. Fix deployment completed!
echo.
echo CRITICAL FIX APPLIED:
echo - Validation now accepts both 'cost_type' (manual) and 'description' (auto-generated)
echo - Custom validation ensures at least one identifier exists
echo - Both store and update methods fixed
echo.
echo NEXT STEPS:
echo 1. Try to update production ID 38 that previously failed
echo 2. Use auto operational costs feature
echo 3. Verify validation now passes
echo 4. Check that operational costs are saved to database
echo.
echo The validation error should now be resolved!
echo.
pause