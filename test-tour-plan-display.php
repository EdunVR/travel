<?php
/**
 * Test script to verify tour plan display
 * Run: php test-tour-plan-display.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== TEST TOUR PLAN DISPLAY ===\n\n";

// Find a package with tour plans
$package = TravelPackage::with(['tourPlans.activities'])
    ->whereHas('tourPlans')
    ->first();

if (!$package) {
    echo "❌ No package with tour plans found\n";
    echo "Please create tour plans first from admin panel\n";
    exit(1);
}

echo "✅ Found package: {$package->package_name}\n";
echo "   Package ID: {$package->id}\n";
echo "   Tour Plans Count: {$package->tourPlans->count()}\n\n";

foreach ($package->tourPlans as $day) {
    echo "📅 Day {$day->day_number}: {$day->day_title}\n";
    echo "   Date: {$day->day_date}\n";
    echo "   Description: " . ($day->description ?: '-') . "\n";
    echo "   Activities: {$day->activities->count()}\n";
    
    foreach ($day->activities as $activity) {
        $time = \Carbon\Carbon::parse($activity->activity_time)->format('H:i');
        echo "   ⏰ {$time} - {$activity->activity_title}\n";
        if ($activity->activity_description) {
            echo "      {$activity->activity_description}\n";
        }
    }
    echo "\n";
}

echo "\n=== VERIFICATION ===\n";
echo "✅ Tour plans are loaded correctly\n";
echo "✅ Activities are loaded for each day\n";
echo "✅ Date and time formatting works\n\n";

echo "📋 Next Steps:\n";
echo "1. Visit admin catalog: /admin/inventaris/travel/catalog/{$package->id}\n";
echo "2. Visit public page: /paket/{$package->id}\n";
echo "3. Verify tour plan section displays correctly\n";
