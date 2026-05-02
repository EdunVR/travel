<?php
/**
 * Test script to verify all required AttendanceManagementController methods exist
 */

echo "=== TESTING ATTENDANCE CONTROLLER METHODS ===\n\n";

// Check if controller file exists
$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (!file_exists($controllerFile)) {
    echo "❌ ERROR: Controller file not found: $controllerFile\n";
    exit(1);
}

echo "✅ Controller file exists: $controllerFile\n";

// Read controller content
$content = file_get_contents($controllerFile);

// List of required methods based on the routes
$requiredMethods = [
    'index' => 'Main index page',
    'getData' => 'Get attendance data (legacy)',
    'getDailyTable' => 'Get daily attendance table data',
    'getMonthlyTable' => 'Get monthly attendance table data',
    'getStatistics' => 'Get attendance statistics',
    'getEmployees' => 'Get employees list',
    'setWorkHours' => 'Set work hours for employees',
    'getTimeSettings' => 'Get RFID time settings',
    'updateTimeSettings' => 'Update RFID time settings',
    'testTimePeriod' => 'Test time period for RFID',
    'store' => 'Create new attendance record',
    'show' => 'Show specific attendance record',
    'update' => 'Update attendance record',
    'destroy' => 'Delete attendance record',
    'exportDailyPdf' => 'Export daily attendance to PDF',
    'exportMonthlyPdf' => 'Export monthly attendance to PDF',
    'exportExcel' => 'Export attendance to Excel'
];

echo "\n📋 Checking required methods:\n";

$missingMethods = [];
$foundMethods = [];

foreach ($requiredMethods as $method => $description) {
    if (preg_match('/public\s+function\s+' . preg_quote($method) . '\s*\(/', $content)) {
        echo "✅ $method - $description\n";
        $foundMethods[] = $method;
    } else {
        echo "❌ $method - $description (MISSING)\n";
        $missingMethods[] = $method;
    }
}

echo "\n📊 Summary:\n";
echo "- Found methods: " . count($foundMethods) . "\n";
echo "- Missing methods: " . count($missingMethods) . "\n";
echo "- Total required: " . count($requiredMethods) . "\n";

if (empty($missingMethods)) {
    echo "\n🎉 SUCCESS: All required methods are present!\n";
} else {
    echo "\n⚠️ WARNING: Missing methods found:\n";
    foreach ($missingMethods as $method) {
        echo "  - $method\n";
    }
}

// Check for syntax errors
echo "\n🔍 Checking PHP syntax...\n";
$syntaxCheck = shell_exec("php -l $controllerFile 2>&1");
if (strpos($syntaxCheck, 'No syntax errors detected') !== false) {
    echo "✅ PHP syntax is valid\n";
} else {
    echo "❌ PHP syntax errors found:\n";
    echo $syntaxCheck . "\n";
}

// Check for required imports
echo "\n📦 Checking required imports...\n";
$requiredImports = [
    'use App\Models\Attendance;',
    'use App\Models\Recruitment;',
    'use App\Models\WorkSchedule;',
    'use App\Models\AttendanceTimeSetting;',
    'use Illuminate\Http\Request;',
    'use Illuminate\Support\Facades\DB;',
    'use Carbon\Carbon;'
];

foreach ($requiredImports as $import) {
    if (strpos($content, $import) !== false) {
        echo "✅ " . trim($import) . "\n";
    } else {
        echo "⚠️ " . trim($import) . " (may be missing)\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";

// Test a simple route to see if it works
echo "\n🌐 Testing a simple route...\n";
try {
    // This would normally require Laravel to be running, so we'll just check if the method exists
    if (in_array('getDailyTable', $foundMethods)) {
        echo "✅ getDailyTable method is available for testing\n";
        echo "📝 You can now test the attendance page in your browser\n";
    }
} catch (Exception $e) {
    echo "⚠️ Route testing requires Laravel application to be running\n";
}

echo "\n📋 Next steps:\n";
echo "1. Clear application cache: php artisan cache:clear\n";
echo "2. Clear route cache: php artisan route:clear\n";
echo "3. Test the attendance page in browser\n";
echo "4. Check browser console for any remaining errors\n";
?>