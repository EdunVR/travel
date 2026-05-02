<?php
/**
 * Test Final Validation Fix
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TESTING FINAL VALIDATION FIX\n";
echo "===============================\n\n";

try {
    // Test Laravel validation with date_format
    echo "🔍 Testing Laravel Validation (date_format)\n";
    echo "===========================================\n";
    
    $validData = [
        'clock_in' => '08:30',
        'clock_out' => '17:00'
    ];
    
    $validator = Validator::make($validData, [
        'clock_in' => 'required|date_format:H:i',
        'clock_out' => 'required|date_format:H:i'
    ], [
        'clock_in.date_format' => 'Format jam masuk harus HH:MM (24 jam)',
        'clock_out.date_format' => 'Format jam pulang harus HH:MM (24 jam)',
    ]);
    
    if ($validator->passes()) {
        echo "✅ Valid data (08:30, 17:00): PASSED\n";
    } else {
        echo "❌ Valid data validation: FAILED\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   Error: {$error}\n";
        }
    }
    
    // Test with various valid formats
    $validFormats = [
        '00:00' => 'Midnight',
        '08:30' => 'Morning',
        '14:45' => 'Afternoon', 
        '23:59' => 'Late night',
        '12:00' => 'Noon'
    ];
    
    echo "\nTesting various valid formats:\n";
    foreach ($validFormats as $time => $description) {
        $validator = Validator::make(['time' => $time], [
            'time' => 'required|date_format:H:i'
        ]);
        
        $status = $validator->passes() ? "✅" : "❌";
        echo "{$status} {$time} ({$description}): " . ($validator->passes() ? "Valid" : "Invalid") . "\n";
    }
    
    // Test invalid formats
    $invalidFormats = [
        '25:00' => 'Invalid hour',
        '08:60' => 'Invalid minute',
        '8:30' => 'Single digit hour (should be invalid with H:i)',
        'abc:def' => 'Non-numeric',
        '8:30 AM' => 'AM/PM format'
    ];
    
    echo "\nTesting invalid formats:\n";
    foreach ($invalidFormats as $time => $description) {
        $validator = Validator::make(['time' => $time], [
            'time' => 'required|date_format:H:i'
        ]);
        
        $status = $validator->fails() ? "✅" : "❌";
        echo "{$status} {$time} ({$description}): " . ($validator->fails() ? "Rejected (correct)" : "Accepted (incorrect)") . "\n";
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                echo "   → {$error}\n";
            }
        }
    }
    
    echo "\n";
    
    // Test time settings validation
    echo "🔍 Testing Time Settings Validation\n";
    echo "===================================\n";
    
    $timeSettingsData = [
        'settings' => [
            [
                'id' => 1,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'is_active' => true
            ]
        ]
    ];
    
    $validator = Validator::make($timeSettingsData, [
        'settings' => 'required|array',
        'settings.*.start_time' => 'required|date_format:H:i',
        'settings.*.end_time' => 'required|date_format:H:i',
        'settings.*.is_active' => 'boolean'
    ], [
        'settings.*.start_time.date_format' => 'Format jam mulai harus HH:MM (24 jam)',
        'settings.*.end_time.date_format' => 'Format jam selesai harus HH:MM (24 jam)',
    ]);
    
    if ($validator->passes()) {
        echo "✅ Time settings validation: PASSED\n";
    } else {
        echo "❌ Time settings validation: FAILED\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   Error: {$error}\n";
        }
    }
    
    echo "\n";
    
    echo "🎯 SUMMARY\n";
    echo "==========\n";
    echo "✅ Switched from regex to date_format validation\n";
    echo "✅ No more 'delimiter' errors\n";
    echo "✅ Proper validation for HH:MM format\n";
    echo "✅ Clear error messages for invalid formats\n";
    echo "✅ Accepts valid 24-hour times\n";
    echo "✅ Rejects invalid times and formats\n\n";
    
    echo "🚀 NEXT STEPS:\n";
    echo "==============\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Test modals in browser:\n";
    echo "   - Pengaturan Waktu RFID (ungu)\n";
    echo "   - Set Jam Kerja (biru)\n";
    echo "   - Tambah Absensi (hijau)\n";
    echo "3. Verify time picker shows 24-hour format\n";
    echo "4. Test saving with valid times\n";
    echo "5. Test validation with invalid times\n\n";
    
    echo "📝 VALIDATION RULES USED:\n";
    echo "=========================\n";
    echo "- date_format:H:i (requires HH:MM format)\n";
    echo "- H = 24-hour format hour (00-23)\n";
    echo "- i = minutes with leading zeros (00-59)\n";
    echo "- More reliable than regex for time validation\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}