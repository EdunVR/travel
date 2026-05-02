#!/bin/bash
# Deploy Customer Cache Fix

echo "Deploying customer cache fix..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Clear customer cache specifically
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
\App\Services\CacheService::clearCustomerCache();
echo 'Customer cache cleared\n';
"

echo "✅ Customer cache fix deployed successfully!"
echo "📋 Please refresh POS page (F5 or Ctrl+R)"
echo "📋 If issue persists, clear browser cache (Ctrl+Shift+R)"
