<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Current workflow tasks:\n";
echo "======================\n\n";

$tasks = DB::table('workflow_tasks')
    ->select('task_name', 'task_description')
    ->distinct()
    ->get();

foreach ($tasks as $task) {
    echo "Task: {$task->task_name}\n";
    echo "Desc: {$task->task_description}\n";
    echo "---\n";
}
