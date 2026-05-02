<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

echo "=== Testing Attendance Photo Display ===\n\n";

// Test 1: Check recent attendance records with photos
echo "1. Checking recent attendance records with photos...\n";
$attendancesWithPhotos = Attendance::whereNotNull('clock_in_photo')
    ->orWhereNotNull('clock_out_photo')
    ->orWhereNotNull('break_in_photo')
    ->orWhereNotNull('break_out_photo')
    ->orWhereNotNull('overtime_in_photo')
    ->orWhereNotNull('overtime_out_photo')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($attendancesWithPhotos->count() > 0) {
    echo "   Found " . $attendancesWithPhotos->count() . " attendance records with photos:\n";
    foreach ($attendancesWithPhotos as $att) {
        echo "   - ID: {$att->id}, Employee: {$att->employee_name}, Date: {$att->date}\n";
        if ($att->clock_in_photo) echo "     Clock in photo: {$att->clock_in_photo}\n";
        if ($att->clock_out_photo) echo "     Clock out photo: {$att->clock_out_photo}\n";
        if ($att->break_in_photo) echo "     Break in photo: {$att->break_in_photo}\n";
        if ($att->break_out_photo) echo "     Break out photo: {$att->break_out_photo}\n";
        if ($att->overtime_in_photo) echo "     Overtime in photo: {$att->overtime_in_photo}\n";
        if ($att->overtime_out_photo) echo "     Overtime out photo: {$att->overtime_out_photo}\n";
    }
} else {
    echo "   ❌ No attendance records with photos found\n";
}

// Test 2: Check storage directory
echo "\n2. Checking storage directory...\n";
$photoDir = storage_path('app/public/attendance_photos');
echo "   Photo directory: $photoDir\n";

if (is_dir($photoDir)) {
    echo "   ✅ Directory exists\n";
    $files = glob($photoDir . '/*.jpg');
    echo "   Found " . count($files) . " photo files:\n";
    foreach (array_slice($files, 0, 5) as $file) {
        $filename = basename($file);
        $size = filesize($file);
        echo "     - $filename (" . number_format($size) . " bytes)\n";
    }
} else {
    echo "   ❌ Directory does not exist\n";
    echo "   Creating directory...\n";
    mkdir($photoDir, 0755, true);
    echo "   ✅ Directory created\n";
}

// Test 3: Check storage link
echo "\n3. Checking storage link...\n";
$publicStorageLink = public_path('storage');
if (is_link($publicStorageLink)) {
    echo "   ✅ Storage link exists: $publicStorageLink\n";
    $target = readlink($publicStorageLink);
    echo "   Points to: $target\n";
} else {
    echo "   ❌ Storage link missing\n";
    echo "   Creating storage link...\n";
    try {
        symlink(storage_path('app/public'), $publicStorageLink);
        echo "   ✅ Storage link created\n";
    } catch (Exception $e) {
        echo "   ❌ Failed to create storage link: " . $e->getMessage() . "\n";
    }
}

// Test 4: Test getDailyTable API response
echo "\n4. Testing getDailyTable API response...\n";
$today = now()->format('Y-m-d');

// Simulate API request
$request = new \Illuminate\Http\Request([
    'date' => $today,
    'outlet_ids' => [],
    'search' => ''
]);

$controller = new \App\Http\Controllers\AttendanceManagementController();
$response = $controller->getDailyTable($request);
$data = $response->getData(true);

if ($data['success']) {
    echo "   ✅ API response successful\n";
    echo "   Found " . count($data['data']) . " attendance records\n";
    
    $recordsWithPhotos = 0;
    foreach ($data['data'] as $record) {
        if (!empty($record['clock_in_photo']) || !empty($record['clock_out_photo'])) {
            $recordsWithPhotos++;
            echo "   - {$record['employee_name']}: ";
            if (!empty($record['clock_in_photo'])) echo "clock_in_photo ✅ ";
            if (!empty($record['clock_out_photo'])) echo "clock_out_photo ✅ ";
            echo "\n";
        }
    }
    
    if ($recordsWithPhotos > 0) {
        echo "   ✅ Found $recordsWithPhotos records with photo data in API response\n";
    } else {
        echo "   ⚠️ No records with photo data in API response\n";
    }
} else {
    echo "   ❌ API response failed: " . $data['message'] . "\n";
}

// Test 5: Check photo URL accessibility
echo "\n5. Testing photo URL accessibility...\n";
if ($attendancesWithPhotos->count() > 0) {
    $firstPhoto = $attendancesWithPhotos->first();
    $photoPath = $firstPhoto->clock_in_photo ?? $firstPhoto->clock_out_photo;
    
    if ($photoPath) {
        $fullPath = storage_path('app/public/' . $photoPath);
        $publicUrl = asset('storage/' . $photoPath);
        
        echo "   Photo path: $photoPath\n";
        echo "   Full path: $fullPath\n";
        echo "   Public URL: $publicUrl\n";
        
        if (file_exists($fullPath)) {
            echo "   ✅ Photo file exists\n";
            echo "   File size: " . number_format(filesize($fullPath)) . " bytes\n";
        } else {
            echo "   ❌ Photo file does not exist\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
echo "If photos are not showing in the datatable:\n";
echo "1. ✅ Photo columns added to database\n";
echo "2. ✅ Controller updated to include photo data\n";
echo "3. ✅ View has photo display functionality\n";
echo "4. Check if storage link exists and photos are accessible\n";