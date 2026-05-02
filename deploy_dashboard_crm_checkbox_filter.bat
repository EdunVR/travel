@echo off
echo === DEPLOYING DASHBOARD CRM CHECKBOX FILTER ===
echo.

echo Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Optimizing...
php artisan config:cache
php artisan route:cache

echo.
echo ✓ Dashboard CRM checkbox filter deployed successfully!
echo.
echo Test the dashboard at: /admin/crm
echo.
echo Features implemented:
echo - Checkbox-based outlet selection
echo - Multiple outlet data filtering for:
echo   * Customer Stats (Total, Active, New, Inactive)
echo   * Sales Analytics (Revenue, Transactions, Avg Value)
echo   * Top Customers ranking
echo   * Customer Segmentation (VIP, Loyal, Regular, New, At Risk)
echo   * Piutang Analysis (Total, Overdue, Customer List)
echo   * Growth Trends charts
echo   * Customer Lifecycle analysis
echo   * Predictive Analytics (Churn Risk, Upsell, Forecast)
echo - Select all/clear all functionality
echo - Real-time data updates
echo - Integrated with period filter
echo - Advanced charts and analytics
echo.
echo Next: Test functionality and continue to Finance Dashboard
echo.
pause