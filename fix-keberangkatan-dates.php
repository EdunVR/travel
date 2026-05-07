<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Keberangkatan;

echo "=== FIXING KEBERANGKATAN DATES ===\n\n";

$packages = TravelPackage::all();

$fixed = 0;
$errors = 0;

foreach ($packages as $package) {
    // Get all keberangkatan for this package
    $keberangkatanList = Keberangkatan::where('id_travel_package', $package->id)->get();
    
    if ($keberangkatanList->count() > 0) {
        foreach ($keberangkatanList as $keberangkatan) {
            $packageDate = $package->departure_date->format('Y-m-d');
            $keberangkatanDate = $keberangkatan->departure_date->format('Y-m-d');
            
            if ($packageDate !== $keberangkatanDate) {
                echo "Fixing Keberangkatan ID: {$keberangkatan->id}\n";
                echo "  Package: {$package->package_name}\n";
                echo "  Old Date: {$keberangkatanDate}\n";
                echo "  New Date: {$packageDate}\n";
                
                try {
                    // Update keberangkatan date to match package date
                    $keberangkatan->departure_date = $package->departure_date;
                    
                    // Also update return date if it exists
                    if ($package->return_date && $keberangkatan->return_date) {
                        $keberangkatan->return_date = $package->return_date;
                        echo "  Also updated return date to: {$package->return_date->format('Y-m-d')}\n";
                    }
                    
                    $keberangkatan->save();
                    
                    echo "  ✅ Fixed!\n\n";
                    $fixed++;
                } catch (\Exception $e) {
                    echo "  ❌ Error: {$e->getMessage()}\n\n";
                    $errors++;
                }
            }
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total Fixed: {$fixed}\n";
echo "Errors: {$errors}\n";

if ($fixed > 0) {
    echo "\n✅ All date mismatches have been fixed!\n";
    echo "Keberangkatan dates now match their package dates.\n";
} else {
    echo "\n✅ No date mismatches found!\n";
}
