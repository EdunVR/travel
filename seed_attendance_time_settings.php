<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AttendanceTimeSetting;

echo "Checking attendance time settings...\n";

$count = AttendanceTimeSetting::count();
echo "Current time settings count: $count\n";

if ($count == 0) {
    echo "Creating default time settings...\n";
    
    AttendanceTimeSetting::create([
        'name' => 'Jam Masuk',
        'start_time' => '06:00',
        'end_time' => '09:00',
        'description' => 'Waktu untuk clock in',
        'is_active' => true
    ]);
    
    AttendanceTimeSetting::create([
        'name' => 'Jam Istirahat',
        'start_time' => '12:00',
        'end_time' => '13:00',
        'description' => 'Waktu istirahat',
        'is_active' => true
    ]);
    
    AttendanceTimeSetting::create([
        'name' => 'Jam Pulang',
        'start_time' => '16:00',
        'end_time' => '18:00',
        'description' => 'Waktu untuk clock out',
        'is_active' => true
    ]);
    
    AttendanceTimeSetting::create([
        'name' => 'Jam Lembur',
        'start_time' => '18:01',
        'end_time' => '22:00',
        'description' => 'Waktu lembur',
        'is_active' => true
    ]);
    
    echo "Default time settings created successfully!\n";
} else {
    echo "Time settings already exist.\n";
    
    // Show existing settings
    $settings = AttendanceTimeSetting::all();
    foreach ($settings as $setting) {
        echo "- {$setting->name}: {$setting->start_time} - {$setting->end_time} (Active: " . ($setting->is_active ? 'Yes' : 'No') . ")\n";
    }
}

echo "Done!\n";