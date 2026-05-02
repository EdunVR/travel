<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== EXISTING BAHAN PERMISSIONS ===\n";
$permissions = DB::table('permissions')->where('name', 'like', '%bahan%')->get();
foreach($permissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== CHECKING EDIT PERMISSIONS ===\n";
$editPerms = DB::table('permissions')->where('name', 'like', '%edit%')->where('name', 'like', '%bahan%')->get();
foreach($editPerms as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== CHECKING INVENTARIS PERMISSIONS ===\n";
$inventarisPerms = DB::table('permissions')->where('name', 'like', '%inventaris%')->where('name', 'like', '%bahan%')->get();
foreach($inventarisPerms as $perm) {
    echo "- {$perm->name}\n";
}
?>