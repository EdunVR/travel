<?php

/**
 * Seed Default Time Settings
 * 
 * This script seeds the default time settings for RFID attendance system
 */

require 'vendor/autoload.php';

try {
    $app = require 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "🌱 Seeding Default Time Settings\n";
    echo "================================\n\n";
    
    $count = \App\Models\AttendanceTimeSetting::count();
    
    if ($count == 0) {
        echo "📝 Creating default time settings...\n";
        
        $settings = [
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
        
        foreach ($settings as $setting) {
            \App\Models\AttendanceTimeSetting::create($setting);
            echo "✅ Created: {$setting['name']} ({$setting['start_time']} - {$setting['end_time']})\n";
        }
        
        echo "\n✅ Default time settings seeded successfully!\n";
        echo "📊 Total records created: " . count($settings) . "\n";
        
    } else {
        echo "ℹ️  Time settings already exist ({$count} records)\n";
        echo "📋 Existing settings:\n";
        
        $existing = \App\Models\AttendanceTimeSetting::all();
        foreach ($existing as $setting) {
            $status = $setting->is_active ? '🟢 Active' : '🔴 Inactive';
            echo "   - {$setting->name}: {$setting->start_time} - {$setting->end_time} {$status}\n";
        }
    }
    
    echo "\n🎯 Time Settings Configuration:\n";
    echo "==============================\n";
    echo "1. Go to Admin > SDM > Absensi\n";
    echo "2. Click 'Pengaturan Waktu' button (purple button)\n";
    echo "3. Configure time ranges as needed\n";
    echo "4. Test with 'Test Periode Waktu' feature\n";
    echo "5. Save settings and test RFID functionality\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>