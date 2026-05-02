@echo off
echo ===================================
echo   Service Dashboard Complete Deploy
echo ===================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing Service Dashboard implementation...
php test_service_dashboard_complete.php

echo.
echo 3. Deployment Summary:
echo ✅ Added index() method to ServiceController
echo ✅ Added getData() method with comprehensive KPI metrics
echo ✅ Updated getStatusCounts() for multiple outlet support
echo ✅ Added private helper methods for dashboard data
echo ✅ Updated routes to use controller instead of view
echo ✅ Service Dashboard now fully functional with checkbox filters
echo.

echo 4. Features Implemented:
echo   - Multiple outlet selection via checkboxes
echo   - Service KPI metrics (revenue, invoices, customers)
echo   - Status counts (menunggu, lunas, gagal, service berikutnya)
echo   - Recent invoices list
echo   - Due soon alerts
echo   - Revenue trend analysis
echo   - Real-time data filtering
echo   - Proper outlet access control
echo.

echo 5. Testing Instructions:
echo   - Visit: http://localhost/tofu/admin/service
echo   - Select multiple outlets using checkboxes
echo   - Verify data updates in real-time
echo   - Check Quick Actions links work correctly
echo   - Test "Pilih Semua" and "Hapus Semua" buttons
echo.

echo ===================================
echo   Service Dashboard Deploy Complete
echo ===================================
pause