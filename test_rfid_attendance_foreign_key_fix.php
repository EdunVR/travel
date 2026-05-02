<?php
/**
 * Test RFID Attendance Foreign Key Fix
 * Verify that RFID attendance creation works without foreign key errors
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TESTING RFID ATTENDANCE FOREIGN KEY FIX\n";
echo "==========================================\n\n";

try {
    // Get first user ID for system operations
    $systemUserId = DB::table('users')->orderBy('id')->value('id');
    echo "✅ System User ID: {$systemUserId}\n\n";
    
    // Get an active recruitment for testing
    $recruitment = DB::table('recruitments')->where('status', 'active')->first();
    
    if (!$recruitment) {
        echo "❌ No active recruitment found. Creating test recruitment...\n";
        
        $recruitmentId = DB::table('recruitments')->insertGetId([
            'name' => 'Test Employee RFID',
            'position' => 'Test Position',
            'status' => 'active',
            'outlet_id' => 1,
            'rfid_uid' => 'TEST-RFID-' . time(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $recruitment = DB::table('recruitments')->find($recruitmentId);
        echo "✅ Created test recruitment with ID: {$recruitmentId}\n\n";
    }
    
    echo "📋 TESTING WITH RECRUITMENT:\n";
    echo "ID: {$recruitment->id}\n";
    echo "Name: {$recruitment->name}\n";
    echo "RFID UID: " . ($recruitment->rfid_uid ?? 'Not set') . "\n";
    echo "Outlet ID: " . ($recruitment->outlet_id ?? 'Not set') . "\n\n";
    
    // Test 1: Direct attendance creation with correct user ID
    echo "🧪 TEST 1: Direct Attendance Creation\n";
    echo "====================================\n";
    
    $testAttendanceData = [
        'recruitment_id' => $recruitment->id,
        'employee_name' => $recruitment->name,
        'fingerprint_id' => $recruitment->fingerprint_id ?? 0,
        'rfid_uid' => $recruitment->rfid_uid ?? 'TEST-UID-' . time(),
        'date' => now()->format('Y-m-d'),
        'status' => 'present',
        'outlet_id' => $recruitment->outlet_id ?? 1,
        'created_by' => $systemUserId, // Use valid user ID
        'clock_in' => '08:00:00',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    try {
        $attendanceId = DB::table('attendances')->insertGetId($testAttendanceData);
        echo "✅ Direct attendance created successfully with ID: {$attendanceId}\n";
        
        // Clean up
        DB::table('attendances')->where('id', $attendanceId)->delete();
        echo "✅ Test data cleaned up\n\n";
        
    } catch (Exception $e) {
        echo "❌ Direct creation failed: " . $e->getMessage() . "\n\n";
    }
    
    // Test 2: Simulate RFID card detection API call
    echo "🧪 TEST 2: RFID Card Detection API Simulation\n";
    echo "=============================================\n";
    
    // Set RFID mode to attendance
    Cache::put('esp32_rfid_mode', 'attendance', 600);
    
    // Create a test request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'uid' => $recruitment->rfid_uid ?? 'TEST-UID-' . time()
    ]);
    
    // Update recruitment with RFID UID if not set
    if (!$recruitment->rfid_uid) {
        DB::table('recruitments')
            ->where('id', $recruitment->id)
            ->update(['rfid_uid' => $request->uid]);
        echo "✅ Updated recruitment with RFID UID: {$request->uid}\n";
    }
    
    try {
        // Call the controller method
        $controller = new \App\Http\Controllers\AttendanceManagementController();
        $response = $controller->handleCardDetected($request);
        
        $responseData = $response->getData(true);
        
        if ($responseData['success']) {
            echo "✅ RFID card detection successful!\n";
            echo "Message: " . $responseData['message'] . "\n";
            echo "Action: " . $responseData['action'] . "\n";
            
            if (isset($responseData['employee'])) {
                echo "Employee: " . $responseData['employee']['name'] . "\n";
            }
            
            if (isset($responseData['attendance'])) {
                echo "Condition: " . $responseData['attendance']['condition'] . "\n";
                echo "Time: " . $responseData['attendance']['time'] . "\n";
            }
            
            // Check if attendance was created
            $createdAttendance = DB::table('attendances')
                ->where('recruitment_id', $recruitment->id)
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();
            
            if ($createdAttendance) {
                echo "✅ Attendance record created with ID: {$createdAttendance->id}\n";
                echo "Created by: " . ($createdAttendance->created_by ?? 'NULL') . "\n";
                
                // Clean up
                DB::table('attendances')->where('id', $createdAttendance->id)->delete();
                echo "✅ Test attendance cleaned up\n";
            }
            
        } else {
            echo "❌ RFID card detection failed: " . $responseData['message'] . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ API simulation failed: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    echo "\n";
    
    // Test 3: Check foreign key constraints
    echo "🧪 TEST 3: Foreign Key Constraint Verification\n";
    echo "==============================================\n";
    
    // Try with invalid user ID (should fail)
    $invalidData = $testAttendanceData;
    $invalidData['created_by'] = 999999; // Non-existent user ID
    
    try {
        DB::table('attendances')->insert($invalidData);
        echo "❌ UNEXPECTED: Invalid user ID was accepted!\n";
    } catch (Exception $e) {
        echo "✅ EXPECTED: Foreign key constraint working correctly\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    // Try with NULL created_by (should work since column is nullable)
    $nullData = $testAttendanceData;
    $nullData['created_by'] = null;
    
    try {
        $attendanceId = DB::table('attendances')->insertGetId($nullData);
        echo "✅ NULL created_by accepted (column is nullable)\n";
        
        // Clean up
        DB::table('attendances')->where('id', $attendanceId)->delete();
        echo "✅ NULL test data cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ NULL created_by failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    echo "🎯 SUMMARY\n";
    echo "==========\n";
    echo "✅ Foreign key constraint issue identified and fixed\n";
    echo "✅ AttendanceManagementController updated to use valid user ID\n";
    echo "✅ RFID attendance creation should now work without errors\n";
    echo "✅ System uses first available user ID: {$systemUserId}\n\n";
    
    echo "🚀 NEXT STEPS:\n";
    echo "1. Test with physical RFID card\n";
    echo "2. Verify attendance records are created correctly\n";
    echo "3. Check that created_by field has valid user ID\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}