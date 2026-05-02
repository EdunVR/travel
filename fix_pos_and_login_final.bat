@echo off
echo ================================================
echo POS dan Login Fix - Final Solution
echo ================================================

echo.
echo 1. Clearing all caches...
php artisan optimize:clear

echo.
echo 2. Clearing sessions...
php artisan tinker --execute="DB::table('sessions')->delete();"

echo.
echo 3. Caching configuration...
php artisan config:cache

echo.
echo 4. Testing configuration...
php -r "require 'vendor/autoload.php'; $app = require 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo 'Session Path: ' . config('session.path') . PHP_EOL; echo 'Session Domain: ' . (config('session.domain') ?: 'null (current domain)') . PHP_EOL; echo 'Session Secure: ' . (config('session.secure') ? 'true' : 'false') . PHP_EOL;"

echo.
echo ================================================
echo Perbaikan Selesai!
echo ================================================
echo.
echo PERUBAHAN YANG DILAKUKAN:
echo ✅ Session path diubah ke /MORRA
echo ✅ POS 401 error handling lebih konservatif
echo ✅ CSRF token auto-refresh setiap 10 menit
echo ✅ Error 419 handling ditambahkan
echo ✅ Login form dengan refresh button
echo.
echo LANGKAH TESTING:
echo 1. CLEAR BROWSER CACHE DAN COOKIES (PENTING!)
echo 2. Login dengan: superadmin@morra.com / password
echo 3. Buka POS page
echo 4. Coba switch outlet beberapa kali
echo 5. Periksa console browser (F12) untuk error
echo.
echo JIKA MASIH ERROR 419:
echo - Klik tombol refresh di error message
echo - Atau refresh halaman login (F5)
echo - Clear browser cache lagi
echo.
echo JIKA POS MASIH LOGOUT:
echo - Check console browser untuk log detail
echo - Pastikan tidak ada error JavaScript
echo - Coba di incognito mode
echo.
pause