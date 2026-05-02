<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Keberangkatan;
use App\Models\JamaahBooking;

echo "=== TEST SISKOPATUH DATA ===\n\n";

$keberangkatanId = 2; // BATCH 1

$keberangkatan = Keberangkatan::with(['travelPackage', 'outlet'])->findOrFail($keberangkatanId);

// Get ALL bookings from the same package
$jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
    ->with(['jamaah', 'documents'])
    ->whereNotIn('status', ['cancelled'])
    ->get();

echo "Total Bookings: {$jamaahBookings->count()}\n\n";

foreach ($jamaahBookings as $index => $booking) {
    $jamaah = $booking->jamaah;
    
    echo "=== Jamaah " . ($index + 1) . " ===\n";
    echo "Nama: " . ($jamaah->nama ?? $jamaah->ktp_nama ?? $jamaah->passport_nama ?? '-') . "\n";
    echo "KTP NIK: " . ($jamaah->ktp_nik ?? '-') . "\n";
    echo "Tempat Lahir (KTP): " . ($jamaah->ktp_tempat_lahir ?? '-') . "\n";
    echo "Tanggal Lahir (KTP): " . ($jamaah->ktp_tanggal_lahir ? \Carbon\Carbon::parse($jamaah->ktp_tanggal_lahir)->format('d/m/Y') : '-') . "\n";
    echo "Tanggal Lahir (Passport): " . ($jamaah->passport_tanggal_lahir ? \Carbon\Carbon::parse($jamaah->passport_tanggal_lahir)->format('d/m/Y') : '-') . "\n";
    echo "Gender: " . ($jamaah->gender ?? '-') . "\n";
    echo "Passport No: " . ($jamaah->passport_nomor ?? '-') . "\n";
    echo "Passport Expiry: " . ($jamaah->passport_tanggal_kadaluarsa ? \Carbon\Carbon::parse($jamaah->passport_tanggal_kadaluarsa)->format('d/m/Y') : '-') . "\n";
    
    // Check documents
    $passportDoc = $booking->documents->where('document_type', 'passport')->where('status', 'approved')->first();
    $visaDoc = $booking->documents->where('document_type', 'visa')->where('status', 'approved')->first();
    
    echo "\nDokumen Passport: " . ($passportDoc ? "Ada (Approved)" : "Tidak ada") . "\n";
    if ($passportDoc) {
        echo "  - Nomor: " . $passportDoc->document_number . "\n";
        echo "  - Issue Date: " . ($passportDoc->issue_date ? $passportDoc->issue_date->format('d/m/Y') : '-') . "\n";
        echo "  - Expiry Date: " . ($passportDoc->expiry_date ? $passportDoc->expiry_date->format('d/m/Y') : '-') . "\n";
    }
    
    echo "Dokumen Visa: " . ($visaDoc ? "Ada (Approved)" : "Tidak ada") . "\n";
    if ($visaDoc) {
        echo "  - Nomor: " . $visaDoc->document_number . "\n";
    }
    
    echo "\n";
}

echo "=== SELESAI ===\n";
