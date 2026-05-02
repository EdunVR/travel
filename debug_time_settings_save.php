<?php

/**
 * Debug Time Settings Save Issue
 * 
 * This script helps debug the 422 error when saving time settings
 */

echo "=== DEBUG TIME SETTINGS SAVE ISSUE ===\n\n";

// Test the validation logic
echo "1. TESTING VALIDATION LOGIC\n";
echo "============================\n";

// Sample data that might be sent from frontend
$testData = [
    'settings' => [
        [
            'id' => 1,
            'start_time' => '07:00',
            'end_time' => '09:00',
            'is_active' => true
        ],
        [
            'id' => 2,
            'start_time' => '11:01',
            'end_time' => '14:00',
            'is_active' => true
        ],
        [
            'id' => 3,
            'start_time' => '14:01',
            'end_time' => '18:00',
            'is_active' => true
        ],
        [
            'id' => 4,
            'start_time' => '18:01',
            'end_time' => '03:30',
            'is_active' => true
        ]
    ]
];

echo "Sample data structure:\n";
print_r($testData);

echo "\n2. TESTING INDIVIDUAL VALIDATIONS\n";
echo "==================================\n";

// Test each validation rule
$validationRules = [
    'settings' => 'required|array',
    'settings.*.id' => 'required|exists:attendance_time_settings,id',
    'settings.*.start_time' => 'required|date_format:H:i',
    'settings.*.end_time' => 'required|date_format:H:i',
    'settings.*.is_active' => 'boolean'
];

foreach ($testData['settings'] as $index => $setting) {
    echo "Testing setting {$index}:\n";
    echo "  ID: {$setting['id']}\n";
    echo "  Start Time: {$setting['start_time']}\n";
    echo "  End Time: {$setting['end_time']}\n";
    echo "  Is Active: " . ($setting['is_active'] ? 'true' : 'false') . "\n";
    
    // Test time format validation
    $startTimeValid = preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $setting['start_time']);
    $endTimeValid = preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $setting['end_time']);
    
    echo "  Start Time Valid: " . ($startTimeValid ? "✅ YES" : "❌ NO") . "\n";
    echo "  End Time Valid: " . ($endTimeValid ? "✅ YES" : "❌ NO") . "\n";
    
    // Test Laravel date_format validation
    try {
        $startDateTime = DateTime::createFromFormat('H:i', $setting['start_time']);
        $endDateTime = DateTime::createFromFormat('H:i', $setting['end_time']);
        
        $startLaravelValid = $startDateTime && $startDateTime->format('H:i') === $setting['start_time'];
        $endLaravelValid = $endDateTime && $endDateTime->format('H:i') === $setting['end_time'];
        
        echo "  Laravel Start Time Valid: " . ($startLaravelValid ? "✅ YES" : "❌ NO") . "\n";
        echo "  Laravel End Time Valid: " . ($endLaravelValid ? "✅ YES" : "❌ NO") . "\n";
    } catch (Exception $e) {
        echo "  Laravel Validation Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "3. CHECKING DATABASE TABLE\n";
echo "==========================\n";

// Check if attendance_time_settings table exists and has data
try {
    $pdo = new PDO("mysql:host=localhost;dbname=tofu", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'attendance_time_settings'");
    $tableExists = $stmt->rowCount() > 0;
    
    echo "Table 'attendance_time_settings' exists: " . ($tableExists ? "✅ YES" : "❌ NO") . "\n";
    
    if ($tableExists) {
        // Check table structure
        $stmt = $pdo->query("DESCRIBE attendance_time_settings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check existing data
        $stmt = $pdo->query("SELECT * FROM attendance_time_settings ORDER BY id");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nExisting data:\n";
        if (empty($settings)) {
            echo "  No data found\n";
        } else {
            foreach ($settings as $setting) {
                echo "  ID {$setting['id']}: {$setting['name']} ({$setting['start_time']} - {$setting['end_time']})\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}

echo "\n4. POTENTIAL ISSUES\n";
echo "===================\n";

$potentialIssues = [
    "Time format not HH:MM" => "Ensure all times are exactly 5 characters (HH:MM)",
    "Invalid time values" => "Check for times like 24:00, 25:00, or XX:60",
    "Missing database records" => "Ensure all IDs exist in attendance_time_settings table",
    "Boolean conversion" => "JavaScript true/false might not convert properly",
    "CSRF token" => "Ensure CSRF token is valid",
    "JSON encoding" => "Check if JSON is properly formatted"
];

foreach ($potentialIssues as $issue => $solution) {
    echo "❓ {$issue}:\n";
    echo "   Solution: {$solution}\n\n";
}

echo "5. DEBUGGING STEPS\n";
echo "==================\n";
echo "1. Check browser Network tab for the actual request data\n";
echo "2. Look at Laravel logs: storage/logs/laravel.log\n";
echo "3. Add console.log in JavaScript before sending request\n";
echo "4. Add Log::info in controller to see received data\n";
echo "5. Test with simple curl request\n";

echo "\n6. SAMPLE CURL TEST\n";
echo "===================\n";
echo "curl -X POST 'https://poshan.my.id/tofu/sdm/attendance/time-settings' \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -H 'X-CSRF-TOKEN: YOUR_TOKEN_HERE' \\\n";
echo "  -d '{\n";
echo "    \"settings\": [\n";
echo "      {\n";
echo "        \"id\": 1,\n";
echo "        \"start_time\": \"07:00\",\n";
echo "        \"end_time\": \"09:00\",\n";
echo "        \"is_active\": true\n";
echo "      }\n";
echo "    ]\n";
echo "  }'\n";

echo "\n=== DEBUG COMPLETE ===\n";

?>