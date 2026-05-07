<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Keberangkatan;

echo "=== CHECKING KEBERANGKATAN DATES MISMATCH ===\n\n";

$packages = TravelPackage::with('keberangkatan')->get();

$mismatches = [];

foreach ($packages as $package) {
    // Get all keberangkatan for this package
    $keberangkatanList = Keberangkatan::where('id_travel_package', $package->id)->get();
    
    if ($keberangkatanList->count() > 0) {
        foreach ($keberangkatanList as $keberangkatan) {
            $packageDate = $package->departure_date->format('Y-m-d');
            $keberangkatanDate = $keberangkatan->departure_date->format('Y-m-d');
            
            if ($packageDate !== $keberangkatanDate) {
                $mismatches[] = [
                    'package_id' => $package->id,
                    'package_name' => $package->package_name,
                    'package_date' => $packageDate,
                    'keberangkatan_id' => $keberangkatan->id,
                    'keberangkatan_date' => $keberangkatanDate,
                    'difference_days' => $package->departure_date->diffInDays($keberangkatan->departure_date, false)
                ];
            }
        }
    }
}

if (empty($mismatches)) {
    echo "✅ No date mismatches found!\n";
} else {
    echo "❌ Found " . count($mismatches) . " date mismatches:\n\n";
    
    foreach ($mismatches as $mismatch) {
        echo "Package ID: {$mismatch['package_id']}\n";
        echo "Package Name: {$mismatch['package_name']}\n";
        echo "Package Date: {$mismatch['package_date']}\n";
        echo "Keberangkatan Date: {$mismatch['keberangkatan_date']}\n";
        echo "Difference: {$mismatch['difference_days']} days\n";
        echo "---\n\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total Packages: " . $packages->count() . "\n";
$totalKeberangkatan = Keberangkatan::count();
echo "Total Keberangkatan: " . $totalKeberangkatan . "\n";
echo "Date Mismatches: " . count($mismatches) . "\n";
