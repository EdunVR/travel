<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking all workflow tasks...\n\n";

// Get all tasks
$tasks = DB::table('workflow_tasks')->get();

echo "Found " . count($tasks) . " tasks:\n\n";

foreach ($tasks as $task) {
    echo "ID: {$task->id}\n";
    echo "Name: {$task->task_name}\n";
    echo "Description: {$task->task_description}\n";
    echo "---\n";
}

// Comprehensive translation map
$translations = [
    // Flight Tickets stage
    'Book flights' => ['name' => 'Booking Penerbangan', 'desc' => 'Pesan kursi penerbangan untuk paket'],
    'Confirm bookings' => ['name' => 'Konfirmasi Booking', 'desc' => 'Dapatkan kode konfirmasi untuk semua booking penerbangan'],
    
    // Hotel Booking stage
    'Book hotels' => ['name' => 'Booking Hotel', 'desc' => 'Pesan kamar hotel untuk paket'],
    'Confirm hotel reservations' => ['name' => 'Konfirmasi Reservasi Hotel', 'desc' => 'Dapatkan konfirmasi untuk semua reservasi hotel'],
    
    // Design Materials stage
    'Create flyer' => ['name' => 'Buat Flyer', 'desc' => 'Desain dan buat flyer promosi paket'],
    'Create itinerary' => ['name' => 'Buat Itinerary', 'desc' => 'Buat jadwal perjalanan lengkap'],
    'Create promotional video' => ['name' => 'Buat Video Promosi', 'desc' => 'Buat video promosi untuk paket'],
    'Prepare package information' => ['name' => 'Siapkan Informasi Paket', 'desc' => 'Siapkan dokumen informasi lengkap paket'],
    
    // Marketing stage
    'Launch marketing campaign' => ['name' => 'Luncurkan Kampanye Marketing', 'desc' => 'Mulai kampanye marketing untuk paket'],
    'Monitor campaign performance' => ['name' => 'Monitor Performa Kampanye', 'desc' => 'Pantau dan analisis performa kampanye'],
    
    // Registration stage
    'Process registrations' => ['name' => 'Proses Pendaftaran', 'desc' => 'Proses pendaftaran jamaah'],
    'Verify documents' => ['name' => 'Verifikasi Dokumen', 'desc' => 'Verifikasi kelengkapan dokumen jamaah'],
    
    // Pre-departure stage
    'Conduct briefing' => ['name' => 'Lakukan Briefing', 'desc' => 'Lakukan briefing keberangkatan untuk jamaah'],
    'Distribute travel kits' => ['name' => 'Distribusi Travel Kit', 'desc' => 'Bagikan travel kit kepada jamaah'],
    
    // Departure stage
    'Coordinate departure' => ['name' => 'Koordinasi Keberangkatan', 'desc' => 'Koordinasi proses keberangkatan jamaah'],
    'Check-in assistance' => ['name' => 'Bantuan Check-in', 'desc' => 'Bantu jamaah dalam proses check-in'],
    
    // In Progress stage
    'Monitor trip progress' => ['name' => 'Monitor Progress Perjalanan', 'desc' => 'Pantau progress perjalanan jamaah'],
    'Handle issues' => ['name' => 'Tangani Masalah', 'desc' => 'Tangani masalah yang muncul selama perjalanan'],
    
    // Completed stage
    'Collect feedback' => ['name' => 'Kumpulkan Feedback', 'desc' => 'Kumpulkan feedback dari jamaah'],
    'Generate report' => ['name' => 'Buat Laporan', 'desc' => 'Buat laporan lengkap perjalanan'],
];

echo "\nUpdating tasks to Indonesian...\n\n";

foreach ($translations as $oldName => $translation) {
    $updated = DB::table('workflow_tasks')
        ->where('task_name', $oldName)
        ->update([
            'task_name' => $translation['name'],
            'task_description' => $translation['desc']
        ]);
    
    if ($updated > 0) {
        echo "✓ Updated: {$oldName} -> {$translation['name']} ({$updated} rows)\n";
    }
}

echo "\nDone!\n";
