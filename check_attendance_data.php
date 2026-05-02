<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;

echo "=== Checking Attendance Data for Malformed Time Fields ===\n\n";

$attendances = Attendance::all();
$malformedCount = 0;
$totalCount = $attendances->count();

echo "Total attendance records: {$totalCount}\n\n";

foreach ($attendances as $attendance) {
    $hasMalformed = false;
    $issues = [];
    
    // Check each time field for malformed data
    $timeFields = ['clock_in', 'clock_out', 'break_in', 'break_out', 'overtime_in', 'overtime_out'];
    
    foreach ($timeFields as $field) {
        $value = $attendance->$field;
        if (!empty($value)) {
            // Check for double time specification pattern
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\s+\d{2}:\d{2}:\d{2}$/', $value)) {
                $hasMalformed = true;
                $issues[] = "{$field}: {$value}";
            }
        }
    }
    
    if ($hasMalformed) {
        $malformedCount++;
        echo "Record ID {$attendance->id} (Date: {$attendance->date}):\n";
        foreach ($issues as $issue) {
            echo "  - {$issue}\n";
        }
        echo "\n";
    }
}

echo "Summary:\n";
echo "- Total records: {$totalCount}\n";
echo "- Records with malformed time data: {$malformedCount}\n";
echo "- Clean records: " . ($totalCount - $malformedCount) . "\n";

if ($malformedCount > 0) {
    echo "\nRecommendation: Run the cleanup script to fix malformed data.\n";
} else {
    echo "\nAll attendance records have clean time data.\n";
}

echo "\n=== Check Complete ===\n";