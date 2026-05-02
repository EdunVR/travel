<?php

/**
 * Debug Affiliate Phone Number
 * 
 * Usage: php debug-affiliate-phone.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG AFFILIATE PHONE NUMBER ===\n\n";

// Get the latest affiliator
$affiliator = \App\Models\Affiliator::latest()->first();

if (!$affiliator) {
    echo "❌ No affiliator found in database\n";
    exit(1);
}

echo "Latest Affiliator:\n";
echo "ID: {$affiliator->id}\n";
echo "Name: {$affiliator->full_name}\n";
echo "Username: {$affiliator->username}\n";
echo "Phone (from DB): {$affiliator->phone_number}\n";
echo "Email: {$affiliator->email}\n";
echo "Status: {$affiliator->status}\n";
echo "Created: {$affiliator->created_at}\n\n";

// Check admin phone from env
$adminPhone = env('ADMIN_WHATSAPP', '628976688800');
echo "Admin Phone (from .env): {$adminPhone}\n\n";

// Compare
if ($affiliator->phone_number === $adminPhone) {
    echo "⚠️  WARNING: Affiliator phone is SAME as admin phone!\n";
    echo "This means the affiliator registered with admin's phone number.\n";
} else {
    echo "✅ Affiliator phone is DIFFERENT from admin phone (correct)\n";
}

echo "\n=== PHONE NUMBER FORMATTING TEST ===\n\n";

$testNumbers = [
    $affiliator->phone_number,
    '081325005006',
    '6281325005006',
    '08976688800',
    '628976688800',
];

foreach ($testNumbers as $phone) {
    echo "Input: $phone\n";
    
    // Format like in controller
    $formatted = preg_replace('/[^0-9]/', '', $phone);
    if (substr($formatted, 0, 1) === '0') {
        $formatted = '62' . substr($formatted, 1);
    } elseif (substr($formatted, 0, 2) !== '62') {
        $formatted = '62' . $formatted;
    }
    
    echo "Formatted: $formatted\n";
    echo "Is Admin? " . ($formatted === $adminPhone ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

echo "\n=== RECOMMENDATION ===\n";
echo "Check if the phone number entered during registration was correct.\n";
echo "The system will send WA to whatever phone number is in the database.\n";

