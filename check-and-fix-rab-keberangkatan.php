<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking RAB and Keberangkatan Data ===\n\n";

// 1. Check keberangkatan with id_rab that doesn't exist in rab_template
echo "1. Checking keberangkatan with invalid id_rab...\n";
$invalidKeberangkatan = DB::table('keberangkatan as k')
    ->leftJoin('rab_template as r', 'k.id_rab', '=', 'r.id_rab')
    ->whereNotNull('k.id_rab')
    ->whereNull('r.id_rab')
    ->select('k.id', 'k.keberangkatan_code', 'k.id_rab')
    ->get();

if ($invalidKeberangkatan->count() > 0) {
    echo "   Found " . $invalidKeberangkatan->count() . " keberangkatan with invalid id_rab:\n";
    foreach ($invalidKeberangkatan as $k) {
        echo "   - ID: {$k->id}, Code: {$k->keberangkatan_code}, id_rab: {$k->id_rab} (RAB not found)\n";
    }
    
    echo "\n   Fixing by setting id_rab to NULL...\n";
    $updated = DB::table('keberangkatan')
        ->whereIn('id', $invalidKeberangkatan->pluck('id'))
        ->update(['id_rab' => null]);
    
    echo "   ✓ Fixed {$updated} records\n";
} else {
    echo "   ✓ No invalid id_rab found\n";
}

echo "\n";

// 2. Check all keberangkatan
echo "2. Listing all keberangkatan:\n";
$allKeberangkatan = DB::table('keberangkatan as k')
    ->leftJoin('rab_template as r', 'k.id_rab', '=', 'r.id_rab')
    ->select('k.id', 'k.keberangkatan_code', 'k.id_rab', 'r.nama_rab')
    ->get();

foreach ($allKeberangkatan as $k) {
    $rabStatus = $k->id_rab ? "RAB: {$k->id_rab} ({$k->nama_rab})" : "No RAB";
    echo "   - ID: {$k->id}, Code: {$k->keberangkatan_code}, {$rabStatus}\n";
}

echo "\n";

// 3. Check RAB that are linked to keberangkatan
echo "3. Checking RAB linked to keberangkatan:\n";
$linkedRab = DB::table('rab_template as r')
    ->join('keberangkatan as k', 'r.id_rab', '=', 'k.id_rab')
    ->select('r.id_rab', 'r.nama_rab', 'k.id as keberangkatan_id', 'k.keberangkatan_code')
    ->get();

if ($linkedRab->count() > 0) {
    foreach ($linkedRab as $r) {
        echo "   - RAB: {$r->id_rab} ({$r->nama_rab}) → Keberangkatan: {$r->keberangkatan_id} ({$r->keberangkatan_code})\n";
    }
} else {
    echo "   No RAB linked to any keberangkatan\n";
}

echo "\n";

// 4. Summary
echo "=== Summary ===\n";
echo "Total keberangkatan: " . $allKeberangkatan->count() . "\n";
echo "Keberangkatan with RAB: " . $allKeberangkatan->where('id_rab', '!=', null)->count() . "\n";
echo "Keberangkatan without RAB: " . $allKeberangkatan->where('id_rab', '=', null)->count() . "\n";
echo "Total RAB: " . DB::table('rab_template')->count() . "\n";
echo "RAB linked to keberangkatan: " . $linkedRab->count() . "\n";

echo "\n✓ Check complete!\n";
