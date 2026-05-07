<?php

/**
 * Script to check and diagnose departure date +1 day issue
 * 
 * This script will:
 * 1. Check all packages and keberangkatan for date discrepancies
 * 2. Show which dates are affected
 * 3. Provide fix recommendations
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Keberangkatan;
use Carbon\Carbon;

echo "=== CHECKING DEPARTURE DATE ISSUES ===\n\n";

// Set timezone
date_default_timezone_set('Asia/Jakarta');
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "Current Date: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

// Check Travel Packages
echo "--- TRAVEL PACKAGES ---\n";
$packages = TravelPackage::with('keberangkatan')->get();

$issuesFound = 0;

foreach ($packages as $package) {
    $packageDate = $package->departure_date;
    
    // Check if package has keberangkatan
    $keberangkatanList = Keberangkatan::where('id_travel_package', $package->id)->get();
    
    if ($keberangkatanList->count() > 0) {
        foreach ($keberangkatanList as $kb) {
            $kbDate = $kb->departure_date;
            
            // Compare dates
            $packageDateStr = $packageDate ? $packageDate->format('Y-m-d') : 'NULL';
            $kbDateStr = $kbDate ? $kbDate->format('Y-m-d') : 'NULL';
            
            if ($packageDateStr !== $kbDateStr) {
                $issuesFound++;
                echo "\n❌ MISMATCH FOUND:\n";
                echo "   Package ID: {$package->id}\n";
                echo "   Package Name: {$package->package_name}\n";
                echo "   Package Date: {$packageDateStr}\n";
                echo "   Keberangkatan ID: {$kb->id}\n";
                echo "   Keberangkatan Date: {$kbDateStr}\n";
                
                // Calculate difference
                if ($packageDate && $kbDate) {
                    $diff = $packageDate->diffInDays($kbDate, false);
                    echo "   Difference: {$diff} day(s)\n";
                }
            }
        }
    }
}

if ($issuesFound === 0) {
    echo "\n✅ No date mismatches found between packages and keberangkatan!\n";
} else {
    echo "\n\n⚠️  Total issues found: {$issuesFound}\n";
}

// Check for timezone issues in database
echo "\n\n--- DATABASE RAW DATA CHECK ---\n";
echo "Checking first 5 packages from database...\n\n";

$rawPackages = \DB::table('travel_packages')
    ->select('id', 'package_name', 'departure_date')
    ->limit(5)
    ->get();

foreach ($rawPackages as $raw) {
    echo "Package ID {$raw->id}: {$raw->package_name}\n";
    echo "  Raw DB value: {$raw->departure_date}\n";
    
    // Get via Eloquent
    $eloquent = TravelPackage::find($raw->id);
    if ($eloquent) {
        echo "  Eloquent value: " . $eloquent->departure_date->format('Y-m-d H:i:s') . "\n";
        echo "  Formatted: " . $eloquent->departure_date->format('d M Y') . "\n";
    }
    echo "\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n\n";

echo "RECOMMENDATIONS:\n";
echo "1. If dates in DB are stored with time (e.g., '2024-12-25 00:00:00'), they should be fine\n";
echo "2. If dates show +1 day, it's likely a timezone conversion issue\n";
echo "3. Solution: Use Carbon::parse()->startOfDay() when saving dates\n";
echo "4. Or change cast from 'date' to 'datetime' and always use startOfDay()\n\n";

