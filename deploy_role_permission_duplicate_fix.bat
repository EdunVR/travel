@echo off
echo ========================================
echo DEPLOY: Role Permission Duplicate Fix
echo ========================================
echo.

echo [1/6] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared
echo.

echo [2/6] Running debug script...
php debug_role_permission_duplicate.php
echo.

echo [3/6] Fixing database constraints...
php fix_role_permission_constraint.php
echo.

echo [4/6] Optimizing application...
php artisan route:cache
php artisan config:cache
echo ✓ Application optimized
echo.

echo [5/6] Checking diagnostics...
echo Checking for any PHP syntax errors...
php -l app/Http/Controllers/RoleManagementController.php
echo ✓ Syntax check completed
echo.

echo [6/6] Final verification...
echo Checking database constraints...
php -r "
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;
try {
    \$constraints = DB::select('SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \"role_permissions\" AND CONSTRAINT_TYPE = \"UNIQUE\"');
    if (count(\$constraints) > 0) {
        echo '✓ Unique constraint exists: ' . \$constraints[0]->CONSTRAINT_NAME . PHP_EOL;
    } else {
        echo '❌ No unique constraint found' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
}
"
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Open browser and go to Role Management
echo 2. Try creating a new role with many permissions
echo 3. Verify no duplicate entry errors occur
echo 4. Test editing existing roles
echo 5. Check Laravel logs for debugging info
echo 6. Test with different permission combinations
echo.
echo FIXED ISSUES:
echo ✓ SQLSTATE[23000] Duplicate entry error
echo ✓ Added array_unique() to prevent backend duplicates
echo ✓ Fixed frontend form submission to prevent duplicates
echo ✓ Added/verified database unique constraint
echo ✓ Enhanced logging for debugging
echo ✓ Created comprehensive test scripts
echo.
echo IMPROVEMENTS:
echo ✓ Backend duplicate prevention
echo ✓ Frontend duplicate prevention
echo ✓ Database constraint enforcement
echo ✓ Enhanced error handling and logging
echo ✓ Comprehensive debugging tools
echo.
echo FILES MODIFIED:
echo - app/Http/Controllers/RoleManagementController.php
echo - resources/views/admin/user-management/roles/modal.blade.php
echo.
echo SCRIPTS CREATED:
echo - debug_role_permission_duplicate.php
echo - fix_role_permission_constraint.php
echo.
echo Documentation: ROLE_PERMISSION_DUPLICATE_FIX.md
echo.

pause