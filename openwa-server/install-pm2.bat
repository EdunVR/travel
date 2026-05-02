@echo off
echo ========================================
echo Install PM2 untuk Production
echo ========================================
echo.

echo Installing PM2 globally...
call npm install -g pm2
echo.

echo Starting OpenWA server with PM2...
call pm2 start server.js --name "openwa-hm-tour"
echo.

echo Saving PM2 configuration...
call pm2 save
echo.

echo Setting up PM2 startup...
call pm2 startup
echo.

echo ========================================
echo PM2 Installation Complete!
echo ========================================
echo.
echo Useful PM2 commands:
echo   pm2 status                  - Check server status
echo   pm2 logs openwa-hm-tour     - View logs
echo   pm2 restart openwa-hm-tour  - Restart server
echo   pm2 stop openwa-hm-tour     - Stop server
echo   pm2 monit                   - Real-time monitoring
echo.

pause
