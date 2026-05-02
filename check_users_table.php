<?php
/**
 * Check users table and fix RFID attendance foreign key issue
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 CHECKING USERS TABLE FOR RFID ATTENDANCE FIX\n";
echo "===============================================\n\n";

try {
    // Check if users table exists and has data
    $users = DB::table('users')->get();
    
    echo "📊 USERS TABLE STATUS:\n";
    echo "Total users: " . $users->count() . "\n\n";
    
    if ($users->count() > 0) {
        echo "👥 EXISTING USERS:\n";
        foreach ($users as $user) {
            echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
        }
        echo "\n";
        
        // Get the first user ID for system operations
        $systemUserId = $users->first()->id;
        echo "✅ System User ID for RFID: {$systemUserId}\n\n";
        
    } else {
        echo "❌ NO USERS FOUND!\n";
        echo "Creating default system user for RFID operations...\n\n";
        
        // Create system user
        $systemUserId = DB::table('users')->insertGetId([
            'name' => 'System RFID',
            'email' => 'system@rfid.local',
            'password' => bcrypt('system123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Created system user with ID: {$systemUserId}\n\n";
    }
    
    // Check attendances table structure
    echo "🔍 CHECKING ATTENDANCES TABLE:\n";
    $attendanceColumns = DB::select("DESCRIBE attendances");
    
    $hasCreatedBy = false;
    foreach ($attendanceColumns as $column) {
        if ($column->Field === 'created_by') {
            $hasCreatedBy = true;
            echo "✅ created_by column exists: {$column->Type} | Null: {$column->Null} | Key: {$column->Key}\n";
            break;
        }
    }
    
    if (!$hasCreatedBy) {
        echo "❌ created_by column not found!\n";
    }
    
    echo "\n";
    
    // Check foreign key constraints
    echo "🔍 CHECKING FOREIGN KEY CONSTRAINTS:\n";
    $constraints = DB::select("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'attendances' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    foreach ($constraints as $constraint) {
        echo "Constraint: {$constraint->CONSTRAINT_NAME}\n";
        echo "  Column: {$constraint->COLUMN_NAME} → {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}\n";
    }
    
    echo "\n";
    
    // Test attendance creation with correct user ID
    echo "🧪 TESTING ATTENDANCE CREATION:\n";
    
    // Get a recruitment record for testing
    $recruitment = DB::table('recruitments')->where('status', 'active')->first();
    
    if ($recruitment) {
        echo "Using recruitment ID: {$recruitment->id} ({$recruitment->name})\n";
        
        // Try to create test attendance
        $testData = [
            'recruitment_id' => $recruitment->id,
            'employee_name' => $recruitment->name,
            'fingerprint_id' => $recruitment->fingerprint_id ?? 0,
            'rfid_uid' => 'TEST-UID-123',
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'outlet_id' => $recruitment->outlet_id ?? 1,
            'created_by' => $systemUserId, // Use valid user ID
            'clock_in' => '08:00:00',
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        try {
            $attendanceId = DB::table('attendances')->insertGetId($testData);
            echo "✅ Test attendance created successfully with ID: {$attendanceId}\n";
            
            // Clean up test data
            DB::table('attendances')->where('id', $attendanceId)->delete();
            echo "✅ Test data cleaned up\n";
            
        } catch (Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ No active recruitment found for testing\n";
    }
    
    echo "\n";
    
    echo "🔧 RECOMMENDED FIX FOR RFID ATTENDANCE:\n";
    echo "=====================================\n";
    echo "1. Use existing user ID: {$systemUserId}\n";
    echo "2. Update AttendanceManagementController::handleCardDetected()\n";
    echo "3. Change 'created_by' => 1 to 'created_by' => {$systemUserId}\n";
    echo "4. Or make created_by nullable and set to null\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}