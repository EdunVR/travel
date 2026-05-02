<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating ALL workflow tasks to Indonesian...\n\n";

$taskUpdates = [
    ['old' => 'Book flights', 'new' => 'Booking Penerbangan', 'desc' => 'Pesan kursi penerbangan untuk paket'],
    ['old' => 'Confirm bookings', 'new' => 'Konfirmasi Booking', 'desc' => 'Dapatkan kode konfirmasi untuk semua booking penerbangan'],
];

foreach ($taskUpdates as $task) {
    $updated = DB::table('workflow_tasks')
        ->where('task_name', $task['old'])
        ->update(['task_name' => $task['new'], 'task_description' => $task['desc']]);
    
    if ($updated > 0) {
        echo "Updated: {$task['old']} -> {$task['new']} ({$updated} rows)\n";
    }
}

echo "\nDone!\n";
