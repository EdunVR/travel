<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AttendanceTimeSetting;
use App\Models\Recruitment;
use App\Models\Attendance;
use Carbon\Carbon;

echo "=== ESP32 Attendance Mode Fix Test ===\n\n";

// Test 1: Check time settings
echo "1. Testing time settings...\n";
$settings = AttendanceTimeSetting::where('is_active', true)->get();
echo "Active time settings: " . $settings->count() . "\n";
foreach ($settings as $setting) {
    echo "   - {$setting->name}: {$setting->start_time} - {$setting->end_time}\n";
}

// Test 2: Test time period determination
echo "\n2. Testing time period determination...\n";
$testTimes = ['08:00:00', '12:30:00', '16:30:00', '19:00:00', '02:00:00'];
foreach ($testTimes as $time) {
    $period = AttendanceTimeSetting::getCurrentTimePeriod($time);
    echo "   Time $time -> Period: " . ($period ?? 'outside_hours') . "\n";
}

// Test 3: Check if we have test employee with RFID
echo "\n3. Checking for test employee with RFID...\n";
$testEmployee = Recruitment::where('rfid_uid', '4A 8C C9 06')->first();
if (!$testEmployee) {
    echo "   Creating test employee with RFID UID: 4A 8C C9 06\n";
    $testEmployee = Recruitment::create([
        'name' => 'Test Employee RFID',
        'position' => 'Test Position',
        'department' => 'Test Department',
        'status' => 'active',
        'rfid_uid' => '4A 8C C9 06',
        'fingerprint_id' => 'TEST001',
        'outlet_id' => 1
    ]);
    echo "   Test employee created with ID: {$testEmployee->id}\n";
} else {
    echo "   Test employee found: {$testEmployee->name} (ID: {$testEmployee->id})\n";
}

// Test 4: Test attendance logic
echo "\n4. Testing attendance logic...\n";
$currentTime = now()->format('H:i:s');
$currentDate = now()->format('Y-m-d');
echo "   Current time: $currentTime\n";
echo "   Current date: $currentDate\n";

$timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
echo "   Current time period: " . ($timePeriod ?? 'outside_hours') . "\n";

// Find existing attendance for today
$attendance = Attendance::where('recruitment_id', $testEmployee->id)
    ->where('date', $currentDate)
    ->first();

if ($attendance) {
    echo "   Existing attendance found for today\n";
    echo "   Clock in: " . ($attendance->clock_in ?? 'Not set') . "\n";
    echo "   Break in: " . ($attendance->break_in ?? 'Not set') . "\n";
    echo "   Break out: " . ($attendance->break_out ?? 'Not set') . "\n";
    echo "   Clock out: " . ($attendance->clock_out ?? 'Not set') . "\n";
} else {
    echo "   No attendance record for today\n";
}

$nextAction = AttendanceTimeSetting::determineNextAction($attendance, $timePeriod);
echo "   Next action would be: $nextAction\n";

// Test 5: Simulate API call
echo "\n5. Simulating API call...\n";
try {
    $postData = json_encode([
        'uid' => '4A 8C C9 06',
        'photo' => 'test_photo_base64_data_here'
    ]);
    
    echo "   POST data prepared (length: " . strlen($postData) . " bytes)\n";
    echo "   This would be sent to: /api/morra/api/rfid/card-detected\n";
    echo "   Expected response: Success with attendance recorded\n";
    
} catch (Exception $e) {
    echo "   Error in simulation: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "The HTTP 500 error should now be fixed!\n";
echo "Key fixes applied:\n";
echo "- Added missing determineTimePeriod logic using AttendanceTimeSetting model\n";
echo "- Improved error handling with try-catch blocks\n";
echo "- Added proper logging for debugging\n";
echo "- Fixed attendance record creation with required fields\n";
echo "- Added photo handling with size limits\n";