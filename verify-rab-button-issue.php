<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFIKASI MASALAH TOMBOL BUAT RAB ===\n\n";

// 1. Cek data keberangkatan yang id_rab nya NULL
echo "1. Keberangkatan dengan id_rab NULL (tombol SEHARUSNYA muncul):\n";
$keberangkatanNull = DB::table('keberangkatan')
    ->whereNull('id_rab')
    ->select('id', 'keberangkatan_code', 'keberangkatan_name', 'id_rab')
    ->get();

if ($keberangkatanNull->isEmpty()) {
    echo "   Tidak ada keberangkatan dengan id_rab NULL\n";
} else {
    foreach ($keberangkatanNull as $k) {
        echo "   - ID: {$k->id}, Code: {$k->keberangkatan_code}, Name: {$k->keberangkatan_name}, id_rab: NULL\n";
    }
}

echo "\n2. Keberangkatan dengan id_rab NOT NULL (tombol TIDAK muncul):\n";
$keberangkatanNotNull = DB::table('keberangkatan')
    ->whereNotNull('id_rab')
    ->select('id', 'keberangkatan_code', 'keberangkatan_name', 'id_rab')
    ->get();

if ($keberangkatanNotNull->isEmpty()) {
    echo "   Tidak ada keberangkatan dengan id_rab NOT NULL\n";
} else {
    foreach ($keberangkatanNotNull as $k) {
        echo "   - ID: {$k->id}, Code: {$k->keberangkatan_code}, Name: {$k->keberangkatan_name}, id_rab: {$k->id_rab}\n";
        
        // Cek apakah RAB nya masih ada
        $rabExists = DB::table('rab_template')->where('id_rab', $k->id_rab)->exists();
        if (!$rabExists) {
            echo "     ⚠️  WARNING: RAB dengan id_rab {$k->id_rab} TIDAK DITEMUKAN di tabel rab_template!\n";
            echo "     ⚠️  Keberangkatan ini perlu di-update id_rab nya menjadi NULL\n";
        }
    }
}

echo "\n3. Cek RAB yang ada di database:\n";
$rabs = DB::table('rab_template')
    ->select('id_rab', 'nama_template', 'book_id', 'total_biaya')
    ->get();

if ($rabs->isEmpty()) {
    echo "   Tidak ada RAB di database\n";
} else {
    foreach ($rabs as $rab) {
        echo "   - id_rab: {$rab->id_rab}, Nama: {$rab->nama_template}, book_id: {$rab->book_id}, Total: Rp " . number_format($rab->total_biaya, 0, ',', '.') . "\n";
    }
}

echo "\n4. Cek keberangkatan yang id_rab nya tidak valid (orphaned):\n";
$orphaned = DB::table('keberangkatan as k')
    ->leftJoin('rab_template as r', 'k.id_rab', '=', 'r.id_rab')
    ->whereNotNull('k.id_rab')
    ->whereNull('r.id_rab')
    ->select('k.id', 'k.keberangkatan_code', 'k.keberangkatan_name', 'k.id_rab')
    ->get();

if ($orphaned->isEmpty()) {
    echo "   ✓ Tidak ada keberangkatan dengan id_rab orphaned\n";
} else {
    echo "   ⚠️  DITEMUKAN keberangkatan dengan id_rab yang tidak valid:\n";
    foreach ($orphaned as $k) {
        echo "   - ID: {$k->id}, Code: {$k->keberangkatan_code}, id_rab: {$k->id_rab} (RAB tidak ada)\n";
    }
    
    echo "\n   Apakah Anda ingin memperbaiki data ini? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        foreach ($orphaned as $k) {
            DB::table('keberangkatan')
                ->where('id', $k->id)
                ->update(['id_rab' => null]);
            echo "   ✓ Fixed: Keberangkatan ID {$k->id} - id_rab set to NULL\n";
        }
        echo "\n   ✓ Semua data orphaned telah diperbaiki!\n";
    }
    fclose($handle);
}

echo "\n=== SELESAI ===\n";
