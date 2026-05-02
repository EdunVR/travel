@echo off
echo ========================================
echo Tour Plan Migration Script
echo ========================================
echo.

echo Running migration for tour_plans tables...
php artisan migrate --path=database/migrations/2026_04_12_000001_create_tour_plans_table.php --force
php artisan migrate --path=database/migrations/2026_04_12_100001_add_day_date_to_tour_plans.php --force
php artisan migrate --path=database/migrations/2026_04_12_100002_make_day_date_not_nullable.php --force

echo.
echo ========================================
echo Clearing cache...
echo ========================================
php artisan route:clear
php artisan view:clear
php artisan config:clear

echo.
echo ========================================
echo Migration Complete!
echo ========================================
echo.
echo Tab "Tour Plan" sudah tersedia di halaman detail paket.
echo Silakan refresh browser untuk melihat perubahan.
echo.
pause
