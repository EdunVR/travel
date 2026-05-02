<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Keberangkatan;
use App\Models\JamaahBooking;

echo "=== DEBUG MANIFEST DATA ===\n\n";

// Cari keberangkatan BATCH 1
$keberangkatan = Keberangkatan::where('keberangkatan_name', 'LIKE', '%BATCH 1%')
    ->orWhere('keberangkatan_code', 'LIKE', '%BATCH%')
    ->first();

if (!$keberangkatan) {
    echo "Keberangkatan BATCH 1 tidak ditemukan!\n";
    echo "Mencari semua keberangkatan...\n\n";
    
    $allKeberangkatan = Keberangkatan::all();
    foreach ($allKeberangkatan as $k) {
        echo "ID: {$k->id}, Code: {$k->keberangkatan_code}, Name: {$k->keberangkatan_name}\n";
    }
    exit;
}

echo "Keberangkatan ditemukan:\n";
echo "ID: {$keberangkatan->id}\n";
echo "Code: {$keberangkatan->keberangkatan_code}\n";
echo "Name: {$keberangkatan->keberangkatan_name}\n";
echo "Status: {$keberangkatan->status}\n\n";

// Cek jamaah bookings tanpa filter
echo "=== SEMUA JAMAAH BOOKINGS (tanpa filter) ===\n";
$allBookings = JamaahBooking::where('id_keberangkatan', $keberangkatan->id)->get();
echo "Total bookings: " . $allBookings->count() . "\n\n";

foreach ($allBookings as $booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Booking Code: {$booking->booking_code}\n";
    echo "Status: {$booking->status}\n";
    echo "Payment Status: {$booking->payment_status}\n";
    echo "ID Member: {$booking->id_member}\n";
    
    if ($booking->jamaah) {
        echo "Jamaah Nama: {$booking->jamaah->nama}\n";
        echo "Jamaah KTP NIK: {$booking->jamaah->ktp_nik}\n";
    } else {
        echo "Jamaah: TIDAK DITEMUKAN!\n";
    }
    echo "---\n";
}

// Cek dengan filter status
echo "\n=== JAMAAH BOOKINGS DENGAN FILTER STATUS ===\n";
$filteredBookings = JamaahBooking::where('id_keberangkatan', $keberangkatan->id)
    ->whereIn('status', ['confirmed', 'paid', 'departed', 'completed'])
    ->get();
echo "Total bookings (filtered): " . $filteredBookings->count() . "\n\n";

foreach ($filteredBookings as $booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Booking Code: {$booking->booking_code}\n";
    echo "Status: {$booking->status}\n";
    if ($booking->jamaah) {
        echo "Jamaah Nama: {$booking->jamaah->nama}\n";
    }
    echo "---\n";
}

// Test query yang digunakan di controller
echo "\n=== TEST QUERY CONTROLLER (UPDATED) ===\n";
$keberangkatanTest = Keberangkatan::with(['travelPackage', 'outlet'])->find($keberangkatan->id);

// Get ALL bookings from the same package
$jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatanTest->id_travel_package)
    ->with(['jamaah', 'documents'])
    ->whereNotIn('status', ['cancelled'])
    ->get();

echo "ID Travel Package: {$keberangkatanTest->id_travel_package}\n";
echo "Total jamaahBookings dari package: " . $jamaahBookings->count() . "\n\n";

foreach ($jamaahBookings as $booking) {
    echo "Booking: {$booking->booking_code}, Status: {$booking->status}\n";
    echo "  ID Keberangkatan: " . ($booking->id_keberangkatan ?? 'NULL') . "\n";
    if ($booking->jamaah) {
        echo "  Jamaah: {$booking->jamaah->nama}\n";
        echo "  KTP NIK: {$booking->jamaah->ktp_nik}\n";
    }
    echo "---\n";
}

echo "\n=== SELESAI ===\n";
