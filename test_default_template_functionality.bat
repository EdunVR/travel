@echo off
echo ========================================
echo  TEST: Default Template Functionality
echo ========================================
echo.
echo Testing the default template feature...
echo.

echo [1/4] Checking if onSetAsDefaultChange function exists...
findstr /C:"onSetAsDefaultChange" resources\views\admin\penjualan\invoice\index.blade.php >nul
if %errorlevel%==0 (
    echo ✓ onSetAsDefaultChange function found
) else (
    echo ✗ onSetAsDefaultChange function NOT found
)

echo.
echo [2/4] Checking if showNotification function exists...
findstr /C:"showNotification" resources\views\admin\penjualan\invoice\index.blade.php >nul
if %errorlevel%==0 (
    echo ✓ showNotification function found
) else (
    echo ✗ showNotification function NOT found
)

echo.
echo [3/4] Checking if checkbox has change event handler...
findstr /C:"@change=\"onSetAsDefaultChange()\"" resources\views\admin\penjualan\invoice\index.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Checkbox change event handler found
) else (
    echo ✗ Checkbox change event handler NOT found
)

echo.
echo [4/4] Checking if template watcher exists...
findstr /C:"$watch('selectedTemplate'" resources\views\admin\penjualan\invoice\index.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Template watcher found
) else (
    echo ✗ Template watcher NOT found
)

echo.
echo ========================================
echo  MANUAL TESTING STEPS
echo ========================================
echo.
echo 1. Login to admin panel
echo 2. Go to Penjualan ^> Invoice Penjualan
echo 3. Click Print button on any invoice
echo 4. Select "Ikuti POS" template
echo 5. Check the "Jadikan template ini sebagai default" checkbox
echo 6. Verify notification appears: "Template Ikuti POS berhasil disimpan sebagai default"
echo 7. Close the print modal
echo 8. Open print modal again on another invoice
echo 9. Verify "Ikuti POS" template is automatically selected
echo 10. Verify checkbox is checked (showing it's the default)
echo 11. Select "Standard" template
echo 12. Verify checkbox becomes unchecked
echo 13. Check the checkbox again to set "Standard" as default
echo 14. Verify notification appears and localStorage is updated
echo.
echo Expected behavior:
echo - Checkbox works when clicked
echo - Notification appears when template is saved as default
echo - Default template is remembered and auto-selected
echo - Checkbox shows correct state based on current vs default template
echo.
pause