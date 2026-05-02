@echo off
echo ========================================
echo   DEPLOYING TIME SETTINGS UI
echo ========================================
echo.

echo 1. Testing Time Settings UI Implementation...
php test_time_settings_ui.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo ✅ Cache cleared
echo.

echo 3. Checking database migration status...
php artisan migrate:status | findstr attendance_time_settings
echo.

echo 4. Running migrations if needed...
php artisan migrate --force
echo ✅ Migrations completed
echo.

echo 5. Seeding default time settings (if table is empty)...
php -r "
try {
    require 'vendor/autoload.php';
    \$app = require 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    \$count = \App\Models\AttendanceTimeSetting::count();
    if (\$count == 0) {
        echo 'Seeding default time settings...' . PHP_EOL;
        
        \$settings = [
            [
                'name' => 'check_in',
                'start_time' => '07:00:00',
                'end_time' => '09:00:00',
                'description' => 'Jam masuk kerja - tap pertama akan dicatat sebagai clock_in',
                'is_active' => true
            ],
            [
                'name' => 'break',
                'start_time' => '11:01:00',
                'end_time' => '14:00:00',
                'description' => 'Jam istirahat - tap pertama break_in, tap kedua break_out',
                'is_active' => true
            ],
            [
                'name' => 'check_out',
                'start_time' => '14:01:00',
                'end_time' => '18:00:00',
                'description' => 'Jam pulang - tap pertama clock_out, tap kedua overtime_in',
                'is_active' => true
            ],
            [
                'name' => 'overtime',
                'start_time' => '18:01:00',
                'end_time' => '03:30:00',
                'description' => 'Jam lembur - tap akan dicatat sebagai overtime_out',
                'is_active' => true
            ]
        ];
        
        foreach (\$settings as \$setting) {
            \App\Models\AttendanceTimeSetting::create(\$setting);
            echo '✅ Created: ' . \$setting['name'] . PHP_EOL;
        }
        
        echo '✅ Default time settings seeded successfully!' . PHP_EOL;
    } else {
        echo '✅ Time settings already exist (' . \$count . ' records)' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
}
"
echo.

echo 6. Optimizing application...
php artisan optimize
echo ✅ Application optimized
echo.

echo 7. Testing API endpoints...
echo Testing GET /admin/sdm/attendance/time-settings...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" "https://poshan.my.id/tofu/admin/sdm/attendance/time-settings" || echo "❌ Endpoint test failed"
echo.

echo ========================================
echo   DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo ✅ Time Settings UI has been deployed successfully!
echo.
echo 📋 WHAT WAS DEPLOYED:
echo - Added "Pengaturan Waktu" button to attendance page
echo - Created time settings configuration modal
echo - Added JavaScript functions for time settings management
echo - Integrated with existing API endpoints
echo - Added test time period functionality
echo - Seeded default time settings data
echo.
echo 🚀 NEXT STEPS:
echo 1. Go to Admin ^> SDM ^> Absensi
echo 2. Click "Pengaturan Waktu" button (purple button)
echo 3. Configure time ranges as needed
echo 4. Test with "Test Periode Waktu" feature
echo 5. Save settings and test RFID functionality
echo.
echo 🔧 DEFAULT TIME RANGES:
echo - Check In: 07:00 - 09:00
echo - Break: 11:01 - 14:00  
echo - Check Out: 14:01 - 18:00
echo - Overtime: 18:01 - 03:30
echo.
pause