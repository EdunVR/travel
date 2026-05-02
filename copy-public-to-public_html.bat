@echo off
echo ========================================
echo Copy Public Files to public_html
echo ========================================
echo.

REM Create public_html folder if not exists
if not exist "public_html" mkdir "public_html"

echo Copying all files from public folder to public_html...
echo.

REM Copy all files and folders from public to public_html
xcopy "public\*" "public_html\" /E /I /Y /EXCLUDE:exclude-files.txt

echo.
echo ========================================
echo Copy Complete!
echo ========================================
echo.
echo Files copied from public to public_html
echo.
echo NEXT STEPS:
echo 1. Upload folder public_html to server
echo 2. Make sure index.php points to ../laravel_app
echo.
pause
