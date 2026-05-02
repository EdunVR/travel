<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Recruitment;

echo "=== Checking Recruitment Data ===\n\n";

$total = Recruitment::count();
echo "Total Recruitment: $total\n";

$active = Recruitment::where('status', 'active')->count();
echo "Active Recruitment: $active\n\n";

if ($active > 0) {
    echo "Sample Active Recruitments:\n";
    $samples = Recruitment::where('status', 'active')->take(5)->get(['id', 'nama', 'status', 'outlet_id']);
    foreach ($samples as $r) {
        echo "  ID: {$r->id} - {$r->nama} - Status: {$r->status} - Outlet: {$r->outlet_id}\n";
    }
} else {
    echo "No active recruitments found!\n";
    echo "\nAll statuses:\n";
    $statuses = Recruitment::select('status')->distinct()->get();
    foreach ($statuses as $s) {
        $count = Recruitment::where('status', $s->status)->count();
        echo "  Status '{$s->status}': $count records\n";
    }
}
