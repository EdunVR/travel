@echo off
REM Script untuk mempersiapkan WordPress sebelum upload ke Hostinger (Windows)
REM Jalankan dari folder WEB_HMTour: prepare-wordpress-upload.bat

echo ==========================================
echo Persiapan Upload WordPress ke Hostinger
echo ==========================================
echo.

REM 1. Create production wp-config
echo 1. Creating production wp-config...
if not exist wp-config-production.php (
    copy wp-config.php wp-config-production.php
    echo [OK] wp-config-production.php created
    echo.
    echo [WARNING] IMPORTANT: Edit wp-config-production.php dengan credentials Hostinger:
    echo    - DB_NAME
    echo    - DB_USER
    echo    - DB_PASSWORD
    echo    - DB_HOST (change to 'localhost')
) else (
    echo [WARNING] wp-config-production.php already exists - skipping
)
echo.

REM 2. Clear cache and temporary files
echo 2. Cleaning cache and temporary files...
if exist wp-content\cache (
    del /q /s wp-content\cache\* 2>nul
)
if exist wp-content\uploads\cache (
    del /q /s wp-content\uploads\cache\* 2>nul
)
del /q /s *.log 2>nul
del /q /s .DS_Store 2>nul
echo [OK] Cache and temporary files cleaned
echo.

REM 3. Create SQL script for URL replacement
echo 3. Creating URL replacement SQL script...
(
echo -- Update WordPress URLs
echo -- IMPORTANT: Replace 'https://your-domain.com' with your actual domain
echo.
echo -- Update site URL
echo UPDATE wp_options 
echo SET option_value = 'https://your-domain.com' 
echo WHERE option_name = 'siteurl';
echo.
echo -- Update home URL
echo UPDATE wp_options 
echo SET option_value = 'https://your-domain.com' 
echo WHERE option_name = 'home';
echo.
echo -- Update post content URLs
echo UPDATE wp_posts 
echo SET post_content = REPLACE^(post_content, 'http://localhost/WEB_HMTour', 'https://your-domain.com'^);
echo.
echo -- Update post meta
echo UPDATE wp_postmeta 
echo SET meta_value = REPLACE^(meta_value, 'http://localhost/WEB_HMTour', 'https://your-domain.com'^);
echo.
echo -- Update post GUID ^(optional, but recommended^)
echo UPDATE wp_posts 
echo SET guid = REPLACE^(guid, 'http://localhost/WEB_HMTour', 'https://your-domain.com'^);
echo.
echo SELECT 'URLs updated successfully!' as Status;
) > update_urls.sql

echo [OK] SQL script created: update_urls.sql
echo.

echo ==========================================
echo Persiapan Selesai!
echo ==========================================
echo.
echo Files yang sudah dibuat:
echo 1. wp-config-production.php - Production config template
echo 2. update_urls.sql - SQL script untuk update URLs
echo.
echo Langkah selanjutnya:
echo 1. Backup database via phpMyAdmin (Export)
echo 2. Edit wp-config-production.php dengan credentials Hostinger
echo 3. Compress folder WEB_HMTour menjadi ZIP (exclude: cache, logs, .git)
echo 4. Upload ZIP ke Hostinger public_html
echo 5. Extract di server
echo 6. Rename wp-config-production.php ke wp-config.php
echo 7. Import database backup ke Hostinger
echo 8. Edit dan jalankan update_urls.sql di phpMyAdmin
echo 9. Baca PANDUAN_UPLOAD_WORDPRESS_HOSTINGER.md untuk detail lengkap
echo.
echo CATATAN: Untuk backup database, gunakan phpMyAdmin:
echo 1. Buka phpMyAdmin lokal
echo 2. Pilih database u127727849_hikamimandiri
echo 3. Tab Export ^> Quick ^> Go
echo 4. Save file SQL
echo.
pause
