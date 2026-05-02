<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AttendanceTimeSetting;
use App\Models\Attendance;
use App\Models\Recruitment;

echo "=== Checking RFID Time Settings ===\n\n";

// Test 1: Check current time settings
echo "1. Current RFID time settings:\n";
$settings = AttendanceTimeSetting::where('is_active', true)->orderBy('start_time')->get();
foreach ($settings as $setting) {
    echo "   - {$setting->name}: {$setting->start_time} - {$setting->end_time}\n";
    echo "     Description: {$setting->description}\n";
}

// Test 2: Test time period logic for different times
echo "\n2. Testing time period logic:\n";
$testTimes = [
    '08:00:00' => 'Morning (should be check_in)',
    '12:30:00' => 'Lunch time (should be break)',
    '16:30:00' => 'Afternoon (should be check_out)',
    '19:00:00' => 'Evening (should be overtime)',
    '10:30:00' => 'Mid morning (outside periods)'
];

foreach ($testTimes as $time => $description) {
    $period = AttendanceTimeSetting::getCurrentTimePeriod($time);
    echo "   $time ($description) -> " . ($period ?? 'outside_hours') . "\n";
}

// Test 3: Test current attendance logic
echo "\n3. Testing current attendance logic:\n";
$testEmployee = Recruitment::where('rfid_uid', '4A 8C C9 06')->first();
if ($testEmployee) {
    echo "   Employee: {$testEmployee->name}\n";
    
    // Get today's attendance
    $today = now()->format('Y-m-d');
    $attendance = Attendance::where('recruitment_id', $testEmployee->id)
        ->where('date', $today)
        ->first();
    
    if ($attendance) {
        echo "   Current attendance status:\n";
        echo "   - Clock in: " . ($attendance->clock_in ?? 'Not set') . "\n";
        echo "   - Break in: " . ($attendance->break_in ?? 'Not set') . "\n";
        echo "   - Break out: " . ($attendance->break_out ?? 'Not set') . "\n";
        echo "   - Clock out: " . ($attendance->clock_out ?? 'Not set') . "\n";
        echo "   - Overtime in: " . ($attendance->overtime_in ?? 'Not set') . "\n";
        echo "   - Overtime out: " . ($attendance->overtime_out ?? 'Not set') . "\n";
        
        // Test what should happen next for different time periods
        echo "\n   What should happen next for different times:\n";
        foreach (['check_in', 'break', 'check_out', 'overtime'] as $period) {
            $nextAction = AttendanceTimeSetting::determineNextAction($attendance, $period);
            echo "   - During $period period: $nextAction\n";
        }
    } else {
        echo "   No attendance record for today\n";
    }
}

echo "\n=== Analysis Complete ===\n";
echo "Issues identified:\n";
echo "1. Need to fix time period determination logic\n";
echo "2. Need to implement proper sequential attendance flow\n";
echo "3. Need to respect time period rules\n";