@echo off
echo ===================================
echo   DEPLOY SERVICE INVOICE PRINT FIX
echo ===================================
echo.

echo 1. Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Running tests...
php test_service_invoice_print_final.php

echo.
echo 3. Checking service invoice routes...
php artisan route:list --name=service.invoice

echo.
echo 4. Testing PDF generation capability...
echo Checking if DomPDF is working...
php -r "
try {
    require 'vendor/autoload.php';
    \$pdf = new Barryvdh\DomPDF\PDF();
    echo 'PDF library: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'PDF library error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo 5. Verifying company settings...
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$count = DB::table('company_settings')->count();
echo 'Company settings records: ' . \$count . PHP_EOL;

if (\$count > 0) {
    \$setting = DB::table('company_settings')->first();
    echo 'Sample company: ' . \$setting->company_name . PHP_EOL;
}
"

echo.
echo ===================================
echo   DEPLOYMENT COMPLETED
echo ===================================
echo.
echo Manual Testing:
echo 1. Go to: http://localhost/tofu/admin/service/invoice
echo 2. Click print button (green printer icon)
echo 3. Verify PDF generates with company info
echo.
echo Print URL example:
echo http://localhost/tofu/admin/service/invoice/1/print
echo.
echo If issues persist:
echo 1. Check storage/logs/laravel.log
echo 2. Verify company_settings table has data
echo 3. Check outlet relationships
echo.
pause