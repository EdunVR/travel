<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Permission;

echo "=== EXISTING BAHAN PERMISSIONS ===\n";
$permissions = Permission::where('name', 'like', '%bahan%')->get();
foreach($permissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== CHECKING EDIT PERMISSIONS ===\n";
$editPerms = Permission::where('name', 'like', '%edit%')->where('name', 'like', '%bahan%')->get();
foreach($editPerms as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== CHECKING INVENTARIS PERMISSIONS ===\n";
$inventarisPerms = Permission::where('name', 'like', '%inventaris%')->where('name', 'like', '%bahan%')->get();
foreach($inventarisPerms as $perm) {
    echo "- {$perm->name}\n";
}
?>