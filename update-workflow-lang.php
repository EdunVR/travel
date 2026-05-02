<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating workflow stages to Indonesian...\n";

// Update Workflow Stages
$stages = [
    ['code' => 'product_analysis', 'name' => 'Analisis Produk', 'desc' => 'Tahap awal untuk merancang dan menghitung biaya paket perjalanan. Hitung HPP dan tentukan kelayakan paket.'],
    ['code' => 'flight_tickets', 'name' => 'Tiket Pesawat', 'desc' => 'Booking dan konfirmasi tiket pesawat untuk paket perjalanan. Pastikan semua kursi sudah dipesan.'],
    ['code' => 'design_materials', 'name' => 'Materi Desain', 'desc' => 'Buat materi marketing termasuk flyer, itinerary, video promosi, dan informasi paket.'],
    ['code' => 'finance', 'name' => 'Keuangan', 'desc' => 'Buat invoice dan atur sistem pembayaran untuk pelanggan.'],
    ['code' => 'follow_up', 'name' => 'Follow-up', 'desc' => 'Hubungi calon pelanggan dan lacak upaya komunikasi serta respons.'],
    ['code' => 'closing', 'name' => 'Closing', 'desc' => 'Finalisasi kesepakatan dengan konfirmasi komitmen pelanggan.'],
    ['code' => 'cs_all_divisions', 'name' => 'CS/Semua Divisi', 'desc' => 'Koordinasi layanan pelanggan di semua divisi untuk dukungan komprehensif.'],
    ['code' => 'social_media', 'name' => 'Media Sosial', 'desc' => 'Kelola promosi media sosial dan pengumpulan testimoni.'],
    ['code' => 'administration', 'name' => 'Administrasi', 'desc' => 'Proses semua dokumen yang diperlukan termasuk paspor, visa, tiket, asuransi, dan sertifikat kesehatan.'],
    ['code' => 'logistics', 'name' => 'Logistik', 'desc' => 'Kelola persiapan peralatan dan pengiriman untuk grup keberangkatan.'],
    ['code' => 'save_jamaah_data', 'name' => 'Simpan Data Jamaah', 'desc' => 'Validasi dan simpan data lengkap jamaah sebelum keberangkatan.'],
];

foreach ($stages as $stage) {
    DB::table('workflow_stages')
        ->where('stage_code', $stage['code'])
        ->update([
            'stage_name' => $stage['name'],
            'description' => $stage['desc']
        ]);
    echo "Updated stage: {$stage['code']}\n";
}

echo "\nUpdating workflow tasks to Indonesian...\n";

// Update Workflow Tasks
$tasks = [
    ['old' => 'Calculate HPP', 'new' => 'Hitung HPP', 'desc' => 'Hitung Harga Pokok Penjualan untuk paket termasuk semua biaya (penerbangan, hotel, transportasi, makan, visa, guide, asuransi, overhead operasional, dan kontingensi)'],
    ['old' => 'Determine Selling Price', 'new' => 'Tentukan Harga Jual', 'desc' => 'Tetapkan harga jual berdasarkan HPP dan margin keuntungan yang diinginkan'],
    ['old' => 'Verify Package Viability', 'new' => 'Verifikasi Kelayakan Paket', 'desc' => 'Pastikan paket layak secara finansial dan kompetitif di pasar'],
    ['old' => 'Book Flight Tickets', 'new' => 'Booking Tiket Pesawat', 'desc' => 'Pesan tiket pesawat untuk semua jamaah sesuai kapasitas paket'],
    ['old' => 'Confirm Reservations', 'new' => 'Konfirmasi Reservasi', 'desc' => 'Dapatkan konfirmasi dari maskapai untuk semua booking'],
    ['old' => 'Update Flight Data', 'new' => 'Update Data Penerbangan', 'desc' => 'Perbarui sistem dengan detail penerbangan dan nomor booking'],
    ['old' => 'Design Flyer', 'new' => 'Desain Flyer', 'desc' => 'Buat flyer promosi yang menarik untuk paket'],
    ['old' => 'Create Itinerary', 'new' => 'Buat Itinerary', 'desc' => 'Siapkan itinerary perjalanan detail untuk jamaah'],
    ['old' => 'Produce Promotional Video', 'new' => 'Produksi Video Promosi', 'desc' => 'Buat video promosi untuk media sosial dan marketing'],
    ['old' => 'Compile Package Information', 'new' => 'Kompilasi Informasi Paket', 'desc' => 'Kumpulkan semua informasi paket dalam format yang mudah dibagikan'],
];

foreach ($tasks as $task) {
    $updated = DB::table('workflow_tasks')
        ->where('task_name', $task['old'])
        ->update([
            'task_name' => $task['new'],
            'task_description' => $task['desc']
        ]);
    if ($updated > 0) {
        echo "Updated task: {$task['old']} -> {$task['new']}\n";
    }
}

echo "\nDone! All workflow data updated to Indonesian.\n";
