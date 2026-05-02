<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Keberangkatan;
use App\Models\JamaahBooking;

echo "=== TEST MANIFEST GENERATION ===\n\n";

$keberangkatanId = 2; // BATCH 1

$keberangkatan = Keberangkatan::with(['travelPackage', 'outlet'])->findOrFail($keberangkatanId);

echo "Keberangkatan: {$keberangkatan->keberangkatan_name}\n";
echo "Package ID: {$keberangkatan->id_travel_package}\n\n";

// Get ALL bookings from the same package
$jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
    ->with(['jamaah', 'documents'])
    ->whereNotIn('status', ['cancelled'])
    ->get();

echo "Total Bookings: {$jamaahBookings->count()}\n\n";

foreach ($jamaahBookings as $index => $booking) {
    $jamaah = $booking->jamaah;
    $passportDoc = $booking->documents->where('document_type', 'passport')->first();
    
    echo "=== Jamaah " . ($index + 1) . " ===\n";
    echo "Booking Code: {$booking->booking_code}\n";
    echo "Status: {$booking->status}\n";
    echo "Nama: " . ($jamaah->nama ?? $jamaah->ktp_nama ?? '-') . "\n";
    echo "KTP NIK: " . ($jamaah->ktp_nik ?? '-') . "\n";
    echo "Passport No: " . ($passportDoc ? $passportDoc->document_number : ($jamaah->passport_nomor ?? '-')) . "\n";
    echo "Passport Expiry: ";
    if ($passportDoc && $passportDoc->expiry_date) {
        echo $passportDoc->expiry_date->format('d M Y');
    } elseif ($jamaah->passport_tanggal_kadaluarsa) {
        echo \Carbon\Carbon::parse($jamaah->passport_tanggal_kadaluarsa)->format('d M Y');
    } else {
        echo '-';
    }
    echo "\n\n";
}

echo "Data siap untuk di-export ke PDF!\n";
