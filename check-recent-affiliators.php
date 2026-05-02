<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RECENT AFFILIATORS ===\n\n";

$affiliators = \App\Models\Affiliator::orderBy('created_at', 'desc')->take(10)->get();

$adminPhone = env('ADMIN_WHATSAPP', '6281325005006');

foreach ($affiliators as $aff) {
    echo "ID: {$aff->id}\n";
    echo "Name: {$aff->full_name}\n";
    echo "Username: {$aff->username}\n";
    echo "Phone: {$aff->phone_number}\n";
    
    // Format phone
    $formatted = preg_replace('/[^0-9]/', '', $aff->phone_number);
    if (substr($formatted, 0, 1) === '0') {
        $formatted = '62' . substr($formatted, 1);
    } elseif (substr($formatted, 0, 2) !== '62') {
        $formatted = '62' . $formatted;
    }
    
    echo "Formatted: {$formatted}\n";
    echo "Is Admin Phone? " . ($formatted === $adminPhone ? 'YES ⚠️' : 'NO') . "\n";
    echo "Created: {$aff->created_at}\n";
    echo "---\n";
}

echo "\nAdmin Phone: {$adminPhone}\n";
