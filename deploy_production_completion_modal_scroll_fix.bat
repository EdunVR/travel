@echo off
echo ========================================
echo Production Completion Modal Scroll Fix
echo ========================================
echo.

echo [1/3] Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo [2/3] Rebuilding assets (if needed)...
if exist "node_modules" (
    echo Running npm build...
    call npm run build
) else (
    echo Skipping npm build - node_modules not found
)

echo.
echo [3/3] Optimizing application...
php artisan optimize

echo.
echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo File yang dimodifikasi:
echo - resources/views/admin/produksi/produksi/index.blade.php
echo.
echo Perubahan:
echo - Modal completion sekarang dapat di-scroll
echo - Header dan footer tetap fixed
echo - Responsive untuk semua device
echo.
echo Testing:
echo 1. Buka: http://your-domain/admin/produksi/produksi
echo 2. Klik tombol "Selesaikan Produksi"
echo 3. Verifikasi modal dapat di-scroll di mobile
echo.
echo Dokumentasi:
echo - PRODUCTION_COMPLETION_MODAL_SCROLL_FIX_COMPLETE.md
echo - QUICK_TEST_PRODUCTION_COMPLETION_MODAL.md
echo - test_production_completion_modal_scroll.html
echo.
pause
