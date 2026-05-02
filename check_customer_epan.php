<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking customer Epan(Bogor)...\n";

// Cari customer dengan nama mengandung 'epan' atau 'bogor'
$customers = DB::table('pelanggan as p')
    ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
    ->where('p.nama_pelanggan', 'like', '%epan%')
    ->orWhere('p.nama_pelanggan', 'like', '%bogor%')
    ->select('p.*', 't.nama_tipe')
    ->get();

if ($customers->isEmpty()) {
    echo "Customer not found. Showing recent customers:\n";
    $recent = DB::table('pelanggan as p')
        ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
        ->select('p.id_pelanggan', 'p.nama_pelanggan', 'p.id_tipe', 't.nama_tipe', 'p.updated_at')
        ->orderBy('p.updated_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recent as $customer) {
        echo "ID: {$customer->id_pelanggan}, Name: {$customer->nama_pelanggan}, Type: {$customer->nama_tipe}, Updated: {$customer->updated_at}\n";
    }
} else {
    echo "Found customers:\n";
    foreach ($customers as $customer) {
        echo "ID: {$customer->id_pelanggan}\n";
        echo "Name: {$customer->nama_pelanggan}\n";
        echo "Type ID: {$customer->id_tipe}\n";
        echo "Type Name: {$customer->nama_tipe}\n";
        echo "Updated: {$customer->updated_at}\n";
        echo "---\n";
    }
}

// Test API endpoint
echo "\nTesting API endpoint...\n";
$apiCustomers = DB::table('pelanggan as p')
    ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
    ->select(
        'p.id_pelanggan as id',
        'p.nama_pelanggan as name',
        'p.telepon',
        'p.id_tipe',
        't.nama_tipe'
    )
    ->where('p.nama_pelanggan', 'like', '%epan%')
    ->orWhere('p.nama_pelanggan', 'like', '%bogor%')
    ->get();

echo "API result:\n";
foreach ($apiCustomers as $customer) {
    echo json_encode($customer) . "\n";
}
?>