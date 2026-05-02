@echo off
echo ========================================
echo VERIFY KONTRA BON DELETE FEATURE
echo ========================================
echo.

echo [1/5] Checking route...
php artisan route:list | findstr "admin.penjualan.kontrabon.destroy"
if %errorlevel% neq 0 (
    echo [X] Route NOT FOUND
    echo.
    echo Running fix...
    call fix_kontrabon_delete_route.bat
) else (
    echo [OK] Route exists
)
echo.

echo [2/5] Checking controller method...
php artisan tinker --execute="echo method_exists('App\Http\Controllers\Admin\KontraBonController', 'destroy') ? 'OK' : 'NOT FOUND';"
echo.

echo [3/5] Checking permission...
php artisan tinker --execute="$p = DB::table('permissions')->where('name', 'sales.kontrabon.delete')->first(); echo $p ? 'OK' : 'NOT FOUND';"
echo.

echo [4/5] Checking view file...
if exist "resources\views\admin\penjualan\kontrabon\index.blade.php" (
    echo [OK] View file exists
) else (
    echo [X] View file NOT FOUND
)
echo.

echo [5/5] Checking JavaScript function...
findstr /C:"deleteKontraBon" resources\views\admin\penjualan\kontrabon\index.blade.php >nul
if %errorlevel% equ 0 (
    echo [OK] JavaScript function exists
) else (
    echo [X] JavaScript function NOT FOUND
)
echo.

echo ========================================
echo VERIFICATION COMPLETE
echo ========================================
echo.
echo Next steps:
echo 1. Open browser and refresh (Ctrl+Shift+R)
echo 2. Go to: /admin/penjualan/kontrabon
echo 3. Click tab "List Kontra Bon"
echo 4. Check if delete button (trash icon) appears
echo 5. Try to delete a kontra bon
echo.
pause
