<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PIUTANG DATA (source_type='travel') ===\n";
$piutang = DB::table('piutang')
    ->where('source_type', 'travel')
    ->get();

echo "Total records: " . $piutang->count() . "\n\n";

foreach ($piutang as $p) {
    echo "ID: {$p->id_piutang}\n";
    echo "Outlet: {$p->id_outlet}\n";
    echo "Member: {$p->id_member}\n";
    echo "Jumlah: Rp " . number_format($p->jumlah_piutang, 0, ',', '.') . "\n";
    echo "Dibayar: Rp " . number_format($p->jumlah_dibayar, 0, ',', '.') . "\n";
    echo "Sisa: Rp " . number_format($p->sisa_piutang, 0, ',', '.') . "\n";
    echo "Status: {$p->status}\n";
    echo "Source: {$p->source_type}\n";
    echo "---\n";
}

echo "\n=== SUMMARY ===\n";
$summary = DB::table('piutang')
    ->where('source_type', 'travel')
    ->selectRaw('
        SUM(jumlah_piutang) as total_piutang,
        SUM(jumlah_dibayar) as total_dibayar,
        SUM(sisa_piutang) as total_sisa
    ')
    ->first();

echo "Total Piutang: Rp " . number_format($summary->total_piutang, 0, ',', '.') . "\n";
echo "Total Dibayar: Rp " . number_format($summary->total_dibayar, 0, ',', '.') . "\n";
echo "Total Sisa: Rp " . number_format($summary->total_sisa, 0, ',', '.') . "\n";

echo "\n=== OUTLETS ===\n";
$outlets = DB::table('outlets')->get();
foreach ($outlets as $outlet) {
    echo "ID: {$outlet->id_outlet} - {$outlet->nama_outlet}\n";
}
