@echo off
echo Deploying Pre Order Additional Costs Feature...

echo.
echo 1. Running migration for additional costs...
php artisan migrate --path=database/migrations/2024_12_18_000001_add_additional_costs_to_pre_order_items_table.php --force

echo.
echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo ✅ Pre Order Additional Costs Feature deployed successfully!
echo.
echo Features added:
echo - Material Instalasi (biaya, satuan, keterangan)
echo - Biaya Pemasangan dan Pelatihan (biaya, satuan, keterangan)  
echo - Ongkos Kirim (biaya, satuan, komponen seperti fuso/forklift)
echo.
echo The additional cost forms will appear after selecting a product in the pre order modal.
echo.
pause