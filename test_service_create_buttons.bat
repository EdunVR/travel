@echo off
echo === TESTING SERVICE CREATE BUTTONS ===
echo.

echo 1. Clearing Laravel caches...
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo.
echo 2. Testing @hasPermission directive...
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use Illuminate\Support\Facades\Blade;

echo 'Checking if @hasPermission directive is registered...' . PHP_EOL;
try {
    \$directives = Blade::getCustomDirectives();
    if (isset(\$directives['hasPermission'])) {
        echo '✓ @hasPermission directive is registered!' . PHP_EOL;
    } else {
        echo '❌ @hasPermission directive not found!' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo 3. Checking AppServiceProvider...
findstr /C:"hasPermission" app\Providers\AppServiceProvider.php >nul
if %errorlevel%==0 (
    echo ✓ hasPermission found in AppServiceProvider
) else (
    echo ❌ hasPermission NOT found in AppServiceProvider
)

echo.
echo === TESTING COMPLETE ===
echo.
echo Now test the service pages:
echo - /admin/service/mesin
echo - /admin/service/ongkir  
echo - /admin/service/history
echo.
echo The "Tambah" buttons should now appear for superadmin!

pause