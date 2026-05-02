<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TourPlan;
use App\Models\TravelPackage;

echo "=== DEBUG TOUR PLANS ===\n\n";

// Get a package with tour plans
$package = TravelPackage::whereHas('tourPlans')->first();

if (!$package) {
    echo "❌ No package with tour plans found\n";
    echo "\nLet's check all tour plans:\n";
    $allPlans = TourPlan::with('activities')->get();
    
    if ($allPlans->isEmpty()) {
        echo "❌ No tour plans found in database\n";
    } else {
        echo "✅ Found {$allPlans->count()} tour plans\n\n";
        foreach ($allPlans as $plan) {
            echo "Tour Plan ID: {$plan->id}\n";
            echo "  Package ID: {$plan->travel_package_id}\n";
            echo "  Day Number: {$plan->day_number}\n";
            echo "  Day Title: {$plan->day_title}\n";
            echo "  Day Date: " . ($plan->day_date ?? 'NULL') . "\n";
            echo "  Description: " . ($plan->description ?? '-') . "\n";
            echo "  Activities: {$plan->activities->count()}\n";
            echo "\n";
        }
    }
    exit;
}

echo "✅ Found package: {$package->package_name} (ID: {$package->id})\n\n";

$tourPlans = $package->tourPlans()->with('activities')->get();

echo "Tour Plans Count: {$tourPlans->count()}\n\n";

foreach ($tourPlans as $plan) {
    echo "=== Day {$plan->day_number}: {$plan->day_title} ===\n";
    echo "  ID: {$plan->id}\n";
    echo "  Day Date: " . ($plan->day_date ?? 'NULL') . "\n";
    echo "  Day Date (raw): " . ($plan->getRawOriginal('day_date') ?? 'NULL') . "\n";
    echo "  Description: " . ($plan->description ?? '-') . "\n";
    echo "  Order: {$plan->order}\n";
    echo "  Activities: {$plan->activities->count()}\n";
    
    if ($plan->activities->count() > 0) {
        foreach ($plan->activities as $activity) {
            echo "    - {$activity->activity_time} | {$activity->activity_title}\n";
            if ($activity->activity_description) {
                echo "      Desc: {$activity->activity_description}\n";
            }
        }
    }
    echo "\n";
}

echo "\n=== JSON OUTPUT (as API would return) ===\n";
echo json_encode($tourPlans->toArray(), JSON_PRETTY_PRINT);

echo "\n\n=== DONE ===\n";
