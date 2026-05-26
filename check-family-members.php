<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;

echo "=== Checking Family Members Data ===\n\n";

// Check if column exists
try {
    $member = Member::whereNotNull('family_members')->first();
    
    if ($member) {
        echo "✅ Found member with family_members data:\n";
        echo "   ID: {$member->id_member}\n";
        echo "   Nama: {$member->nama}\n";
        echo "   Family Members: " . json_encode($member->family_members, JSON_PRETTY_PRINT) . "\n\n";
    } else {
        echo "⚠️  No members with family_members data found yet.\n";
        echo "   This is normal if no one has added family members.\n\n";
    }
    
    // Check total members
    $totalMembers = Member::count();
    $membersWithFamily = Member::whereNotNull('family_members')->count();
    
    echo "📊 Statistics:\n";
    echo "   Total Members: {$totalMembers}\n";
    echo "   Members with Family Data: {$membersWithFamily}\n\n";
    
    echo "✅ Column 'family_members' exists and is working!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   The 'family_members' column might not exist in the database.\n";
}
