<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \Illuminate\Support\Facades\DB::table('users')->select('id', 'name')->get();

echo "Available Users:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}\n";
}