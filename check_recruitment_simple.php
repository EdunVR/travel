<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Recruitment;

echo "=== RECRUITMENT DATA CHECK ===\n\n";

// Total count
$total = Recruitment::count();
echo "Total Recruitments: $total\n\n";

// By status
echo "By Status:\n";
$statuses = Recruitment::select('status', \DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();
foreach ($statuses as $s) {
    echo "  - {$s->status}: {$s->count}\n";
}

// Active with outlet
echo "\nActive Recruitments:\n";
$active = Recruitment::where('status', 'active')->get(['id', 'name', 'position', 'outlet_id']);
echo "  Total: " . $active->count() . "\n";

if ($active->count() > 0) {
    echo "\n  Sample Data:\n";
    foreach ($active->take(5) as $r) {
        echo "    ID: {$r->id} | Name: {$r->name} | Position: {$r->position} | Outlet: " . ($r->outlet_id ?? 'NULL') . "\n";
    }
    
    // Check outlet distribution
    echo "\n  By Outlet:\n";
    $byOutlet = Recruitment::where('status', 'active')
        ->select('outlet_id', \DB::raw('count(*) as count'))
        ->groupBy('outlet_id')
        ->get();
    foreach ($byOutlet as $o) {
        echo "    Outlet " . ($o->outlet_id ?? 'NULL') . ": {$o->count} employees\n";
    }
}

echo "\n=== END ===\n";
