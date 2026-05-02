<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;

echo "=== TEST API RESPONSE FORMAT ===\n\n";

// Test Flight Data
echo "1. FLIGHT DATA API:\n";
echo str_repeat("-", 80) . "\n";

$flightController = new FlightController();
$request = new Request();
$response = $flightController->getData($request);
$content = $response->getContent();
$data = json_decode($content, true);

echo "Response structure:\n";
echo "  - Has 'data' key: " . (isset($data['data']) ? 'YES' : 'NO') . "\n";

if (isset($data['data'])) {
    echo "  - data is array: " . (is_array($data['data']) ? 'YES' : 'NO') . "\n";
    echo "  - data count: " . count($data['data']) . "\n";
    
    if (!empty($data['data'])) {
        echo "\nFirst flight structure:\n";
        $firstFlight = $data['data'][0];
        echo "  Keys: " . implode(', ', array_keys($firstFlight)) . "\n";
        echo "\nFirst flight data:\n";
        echo "  ID: " . ($firstFlight['id'] ?? 'MISSING') . "\n";
        echo "  airline_name: " . ($firstFlight['airline_name'] ?? ($firstFlight['airline'] ?? 'MISSING')) . "\n";
        echo "  flight_number: " . ($firstFlight['flight_number'] ?? 'MISSING') . "\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Test Hotel Data
echo "2. HOTEL DATA API:\n";
echo str_repeat("-", 80) . "\n";

$hotelController = new HotelController();
$request = new Request();
$response = $hotelController->getData($request);
$content = $response->getContent();
$data = json_decode($content, true);

echo "Response structure:\n";
echo "  - Has 'data' key: " . (isset($data['data']) ? 'YES' : 'NO') . "\n";

if (isset($data['data'])) {
    echo "  - data is array: " . (is_array($data['data']) ? 'YES' : 'NO') . "\n";
    echo "  - data count: " . count($data['data']) . "\n";
    
    if (!empty($data['data'])) {
        echo "\nFirst hotel structure:\n";
        $firstHotel = $data['data'][0];
        echo "  Keys: " . implode(', ', array_keys($firstHotel)) . "\n";
        echo "\nFirst hotel data:\n";
        echo "  ID: " . ($firstHotel['id'] ?? 'MISSING') . "\n";
        echo "  hotel_name: " . ($firstHotel['hotel_name'] ?? 'MISSING') . "\n";
        echo "  location: " . ($firstHotel['location'] ?? 'MISSING') . "\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Test Room Types
echo "3. HOTEL ROOM TYPES API:\n";
echo str_repeat("-", 80) . "\n";

$hotelId = 1;
try {
    $request = new Request();
    $response = $hotelController->getRoomTypes($hotelId);
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "Response structure:\n";
    echo "  - Has 'data' key: " . (isset($data['data']) ? 'YES' : 'NO') . "\n";
    
    if (isset($data['data'])) {
        echo "  - data is array: " . (is_array($data['data']) ? 'YES' : 'NO') . "\n";
        echo "  - data count: " . count($data['data']) . "\n";
        
        if (!empty($data['data'])) {
            echo "\nFirst room type structure:\n";
            $firstRoom = $data['data'][0];
            echo "  Keys: " . implode(', ', array_keys($firstRoom)) . "\n";
            echo "\nFirst room type data:\n";
            echo "  ID: " . ($firstRoom['id'] ?? 'MISSING') . "\n";
            echo "  room_type_name: " . ($firstRoom['room_type_name'] ?? 'MISSING') . "\n";
            echo "  price_per_night: " . ($firstRoom['price_per_night'] ?? 'MISSING') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

echo "CONCLUSION:\n";
echo str_repeat("-", 80) . "\n";
echo "If all APIs return data.data structure with correct fields,\n";
echo "then the edit form should work correctly.\n\n";
echo "If fields are missing or have different names,\n";
echo "we need to fix the API response format.\n";
