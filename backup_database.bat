@echo off
REM Database Backup Script
REM Run this daily to backup your database

set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_DIR=database_backups
set BACKUP_FILE=%BACKUP_DIR%\demo_backup_%TIMESTAMP%.sql

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM Backup database
echo Creating backup: %BACKUP_FILE%
C:\xampp\mysql\bin\mysqldump.exe -u root demo > %BACKUP_FILE%

REM Keep only last 7 days of backups
forfiles /p "%BACKUP_DIR%" /m *.sql /d -7 /c "cmd /c del @path" 2>nul

echo Backup complete: %BACKUP_FILE%
echo.
pause
