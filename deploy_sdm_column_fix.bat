@echo off
echo ===================================
echo   SDM Dashboard Column Fix Deploy
echo ===================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Checking KontrakKerja model structure...
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);

\$model = new App\Models\KontrakKerja();
\$fillable = \$model->getFillable();
echo 'KontrakKerja fillable fields: ' . implode(', ', \$fillable) . PHP_EOL;
echo 'outlet_id present: ' . (in_array('outlet_id', \$fillable) ? 'YES' : 'NO') . PHP_EOL;
"

echo.
echo 3. Testing SDM Dashboard access...
echo Please test the SDM Dashboard in your browser:
echo http://localhost/tofu/admin/sdm
echo.

echo 4. Deployment Summary:
echo ✅ Fixed KontrakKerja model column from 'id_outlet' to 'outlet_id'
echo ✅ Updated SdmDashboardController getRecentActivities method
echo ✅ Cleared all Laravel caches
echo.

echo ===================================
echo   SDM Dashboard Column Fix Complete
echo ===================================
pause