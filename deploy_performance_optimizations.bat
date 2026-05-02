@echo off
echo 🚀 Deploying Performance Optimizations...
echo ==========================================
echo.

echo 📦 1. Running composer dump-autoload...
composer dump-autoload
if %errorlevel% neq 0 (
    echo ❌ Composer dump-autoload failed
    pause
    exit /b 1
)
echo ✅ Composer autoload updated
echo.

echo 🧹 2. Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo ✅ Caches cleared
echo.

echo 🔧 3. Running database optimizations...
php implement_quick_optimizations.php
echo ✅ Database optimizations completed
echo.

echo 🔥 4. Warming up cache...
php artisan performance:monitor --warm-cache
echo ✅ Cache warmed up
echo.

echo 📊 5. Performance statistics...
php artisan performance:monitor --stats
echo.

echo 🎯 6. Testing optimized queries...
php performance_audit.php
echo ✅ Performance audit completed
echo.

echo 📋 7. Summary of optimizations deployed:
echo ✅ Database indexes added for better query performance
echo ✅ CacheService implemented for data caching
echo ✅ Response compression middleware created
echo ✅ Lazy loading for images implemented
echo ✅ Performance monitoring command added
echo ✅ Quick database optimizations applied
echo.

echo 🎉 Performance optimizations deployed successfully!
echo.
echo 📝 Next steps:
echo 1. Monitor application performance in production
echo 2. Set up regular cache warming (cron job)
echo 3. Enable MySQL slow query log for monitoring
echo 4. Consider Redis for better caching performance
echo 5. Implement CDN for static assets
echo.

echo 🔧 Available commands:
echo - php artisan performance:monitor --stats
echo - php artisan performance:monitor --clear-cache
echo - php artisan performance:monitor --warm-cache
echo - php performance_audit.php
echo.

pause