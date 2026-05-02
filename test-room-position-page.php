<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ROOM POSITION PAGE ===\n\n";

// Test 1: Check if route exists
echo "1. Checking route...\n";
try {
    $url = route('admin.inventaris.document.manage-room-position', ['keberangkatan' => 1]);
    echo "   ✓ Route exists: {$url}\n";
} catch (\Exception $e) {
    echo "   ✗ Route error: " . $e->getMessage() . "\n";
}

// Test 2: Check if keberangkatan exists
echo "\n2. Checking keberangkatan data...\n";
$keberangkatan = \App\Models\Keberangkatan::first();
if ($keberangkatan) {
    echo "   ✓ Keberangkatan found: ID {$keberangkatan->id}, Code: {$keberangkatan->keberangkatan_code}\n";
    
    // Test 3: Check hotel bookings
    echo "\n3. Checking hotel bookings...\n";
    $hotelBookings = $keberangkatan->hotelBookings;
    echo "   Total hotel bookings: " . $hotelBookings->count() . "\n";
    
    foreach ($hotelBookings as $booking) {
        echo "   - Hotel: " . ($booking->hotel->hotel_name ?? 'N/A') . "\n";
        echo "     Room assignments: " . $booking->roomAssignments->count() . "\n";
    }
    
    // Test 4: Test API endpoint
    echo "\n4. Testing API endpoint...\n";
    try {
        $controller = new \App\Http\Controllers\DocumentController(
            app(\App\Services\NotificationService::class),
            app(\App\Services\AuditService::class)
        );
        
        $response = $controller->getRoomPositions($keberangkatan->id);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ✓ API works!\n";
            echo "   Hotels returned: " . count($data['hotels']) . "\n";
        } else {
            echo "   ✗ API returned error\n";
        }
    } catch (\Exception $e) {
        echo "   ✗ API error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ No keberangkatan found in database\n";
}

echo "\n=== TEST COMPLETE ===\n";
