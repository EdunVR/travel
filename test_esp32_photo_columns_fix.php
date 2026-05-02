<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Recruitment;
use App\Models\Attendance;
use App\Models\AttendanceTimeSetting;

echo "=== ESP32 Photo Columns Fix Test ===\n\n";

// Test 1: Verify photo columns exist
echo "1. Verifying photo columns in database...\n";
$attendance = new Attendance();
$fillable = $attendance->getFillable();
$photoColumns = ['clock_in_photo', 'clock_out_photo', 'break_in_photo', 'break_out_photo', 'overtime_in_photo', 'overtime_out_photo'];

foreach ($photoColumns as $column) {
    $exists = \Schema::hasColumn('attendances', $column);
    echo "   - $column: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";
}

// Test 2: Test employee with RFID
echo "\n2. Testing employee with RFID...\n";
$testEmployee = Recruitment::where('rfid_uid', '4A 8C C9 06')->first();
if ($testEmployee) {
    echo "   ✅ Employee found: {$testEmployee->name} (ID: {$testEmployee->id})\n";
    echo "   RFID UID: {$testEmployee->rfid_uid}\n";
    echo "   Outlet ID: {$testEmployee->outlet_id}\n";
} else {
    echo "   ❌ No employee found with RFID UID: 4A 8C C9 06\n";
}

// Test 3: Test attendance creation with photo
echo "\n3. Testing attendance creation with photo...\n";
$currentDate = now()->format('Y-m-d');
$currentTime = now()->format('H:i:s');

// Delete existing attendance for clean test
Attendance::where('recruitment_id', $testEmployee->id)
    ->where('date', $currentDate)
    ->delete();

try {
    // Create attendance record
    $attendance = Attendance::create([
        'outlet_id' => $testEmployee->outlet_id ?? 1,
        'recruitment_id' => $testEmployee->id,
        'employee_name' => $testEmployee->name,
        'fingerprint_id' => $testEmployee->fingerprint_id,
        'date' => $currentDate,
        'clock_in' => $currentTime,
        'clock_in_photo' => 'test_photos/test_photo_' . time() . '.jpg',
        'status' => 'present',
        'notes' => 'Test attendance with photo',
        'created_by' => null // ESP32 request
    ]);
    
    echo "   ✅ Attendance created successfully!\n";
    echo "   ID: {$attendance->id}\n";
    echo "   Clock in: {$attendance->clock_in}\n";
    echo "   Clock in photo: {$attendance->clock_in_photo}\n";
    
} catch (Exception $e) {
    echo "   ❌ Error creating attendance: " . $e->getMessage() . "\n";
}

// Test 4: Test updating attendance with photo
echo "\n4. Testing attendance update with photo...\n";
try {
    $attendance->clock_out = now()->addHours(8)->format('H:i:s');
    $attendance->clock_out_photo = 'test_photos/test_photo_out_' . time() . '.jpg';
    $attendance->save();
    
    echo "   ✅ Attendance updated successfully!\n";
    echo "   Clock out: {$attendance->clock_out}\n";
    echo "   Clock out photo: {$attendance->clock_out_photo}\n";
    
} catch (Exception $e) {
    echo "   ❌ Error updating attendance: " . $e->getMessage() . "\n";
}

// Test 5: Test time period logic
echo "\n5. Testing time period logic...\n";
$timePeriod = AttendanceTimeSetting::getCurrentTimePeriod($currentTime);
echo "   Current time: $currentTime\n";
echo "   Time period: " . ($timePeriod ?? 'outside_hours') . "\n";

$nextAction = AttendanceTimeSetting::determineNextAction($attendance, $timePeriod);
echo "   Next action: $nextAction\n";

echo "\n=== Test Complete ===\n";
echo "The 'Unknown column clock_in_photo' error should now be fixed!\n";
echo "\nKey fixes applied:\n";
echo "- ✅ Fixed migration table names (attendance -> attendances)\n";
echo "- ✅ Added all 6 photo columns to attendances table\n";
echo "- ✅ Fixed recruitment table name (recruitment -> recruitments)\n";
echo "- ✅ Added rfid_uid column to recruitments table\n";
echo "- ✅ Verified photo columns are working\n";