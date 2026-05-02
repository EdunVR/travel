<?php
/**
 * Test script untuk API room assignments
 * Jalankan: php test-room-assignment-api.php [hotel_booking_id]
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;
use App\Http\Controllers\HotelBookingController;

echo "=== TEST ROOM ASSIGNMENT API ===\n\n";

$hotelBookingId = $argv[1] ?? null;

if (!$hotelBookingId) {
    $firstBooking = \App\Models\HotelBooking::first();
    if ($firstBooking) {
        $hotelBookingId = $firstBooking->id;
        echo "Using first hotel booking ID: {$hotelBookingId}\n\n";
    } else {
        echo "ERROR: No hotel bookings found!\n";
        exit(1);
    }
}

// Create controller instance
$controller = new HotelBookingController();

// Call the method
$response = $controller->getRoomAssignments($hotelBookingId);

// Get response content
$content = $response->getContent();
$data = json_decode($content, true);

echo "API Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

if ($data['success']) {
    echo "Success: TRUE\n";
    echo "Unassigned Jamaah Count: " . count($data['data']['unassigned_jamaah']) . "\n";
    echo "Current Assignments Count: " . count($data['data']['assignments']) . "\n\n";
    
    if (count($data['data']['unassigned_jamaah']) > 0) {
        echo "Unassigned Jamaah Details:\n";
        foreach ($data['data']['unassigned_jamaah'] as $jamaah) {
            echo "  - ID: {$jamaah['id']}\n";
            echo "    Name: {$jamaah['jamaah_name']}\n";
            echo "    Room Type: {$jamaah['room_type']}\n";
            echo "    Booking Code: {$jamaah['booking_code']}\n\n";
        }
    } else {
        echo "No unassigned jamaah found.\n";
    }
} else {
    echo "Success: FALSE\n";
    echo "Message: " . ($data['message'] ?? 'Unknown error') . "\n";
}

echo "\n=== END TEST ===\n";
