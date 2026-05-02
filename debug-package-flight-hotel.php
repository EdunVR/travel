<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\HppCalculation;
use App\Models\JamaahBooking;

echo "=== DEBUG PACKAGE FLIGHT & HOTEL DATA ===\n\n";

// Get a booking with invoice
$booking = JamaahBooking::whereNotNull('id_invoice')
    ->with(['travelPackage.hppCalculation', 'travelPackage.keberangkatan'])
    ->first();

if (!$booking) {
    echo "No booking with invoice found\n";
    exit;
}

echo "Booking ID: {$booking->id}\n";
echo "Booking Code: {$booking->booking_code}\n\n";

$package = $booking->travelPackage;
echo "=== TRAVEL PACKAGE ===\n";
echo "Package ID: {$package->id}\n";
echo "Package Name: {$package->package_name}\n";
echo "Package Type: {$package->package_type}\n";

// Check all fields in travel_packages table
$packageAttributes = $package->getAttributes();
echo "\n=== ALL PACKAGE FIELDS ===\n";
foreach ($packageAttributes as $key => $value) {
    if (str_contains(strtolower($key), 'flight') || 
        str_contains(strtolower($key), 'airline') || 
        str_contains(strtolower($key), 'hotel')) {
        echo "$key: " . ($value ?? 'NULL') . "\n";
    }
}

// Check HPP Calculation
if ($package->hppCalculation) {
    echo "\n=== HPP CALCULATION ===\n";
    $hppAttributes = $package->hppCalculation->getAttributes();
    foreach ($hppAttributes as $key => $value) {
        if (str_contains(strtolower($key), 'flight') || 
            str_contains(strtolower($key), 'airline') || 
            str_contains(strtolower($key), 'hotel')) {
            echo "$key: " . ($value ?? 'NULL') . "\n";
        }
    }
}

// Check Keberangkatan
if ($package->keberangkatan->count() > 0) {
    echo "\n=== KEBERANGKATAN (FIRST) ===\n";
    $keberangkatan = $package->keberangkatan->first();
    echo "Keberangkatan ID: {$keberangkatan->id}\n";
    echo "Keberangkatan Name: {$keberangkatan->keberangkatan_name}\n";
    
    // Check if keberangkatan has flight/hotel bookings
    if (method_exists($keberangkatan, 'flightBookings')) {
        $flightBookings = $keberangkatan->flightBookings;
        echo "\nFlight Bookings Count: " . $flightBookings->count() . "\n";
        if ($flightBookings->count() > 0) {
            $flightBooking = $flightBookings->first();
            if ($flightBooking->flight) {
                echo "Flight Airline: " . $flightBooking->flight->airline_name . "\n";
                echo "Flight Number: " . $flightBooking->flight->flight_number . "\n";
            }
        }
    }
    
    if (method_exists($keberangkatan, 'hotelBookings')) {
        $hotelBookings = $keberangkatan->hotelBookings;
        echo "\nHotel Bookings Count: " . $hotelBookings->count() . "\n";
        if ($hotelBookings->count() > 0) {
            $hotelBooking = $hotelBookings->first();
            if ($hotelBooking->hotel) {
                echo "Hotel Name: " . $hotelBooking->hotel->hotel_name . "\n";
                echo "Hotel Location: " . $hotelBooking->hotel->location . "\n";
            }
        }
    }
}

echo "\n=== SOLUTION ===\n";
echo "Data maskapai dan hotel ada di level KEBERANGKATAN, bukan di level PACKAGE.\n";
echo "Untuk menampilkan di invoice, perlu:\n";
echo "1. Ambil data dari booking->keberangkatan->flightBookings->flight\n";
echo "2. Ambil data dari booking->keberangkatan->hotelBookings->hotel\n";
echo "3. Atau tambahkan field airline dan hotel_name di travel_packages table\n";
