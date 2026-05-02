<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = DB::table('users')->where('email', 'superadmin@morra.com')->first();
echo "User data:\n";
echo "- ID: " . $user->id . "\n";
echo "- Name: " . $user->name . "\n";
echo "- Email: " . $user->email . "\n";
echo "- Is Active: " . (isset($user->is_active) ? ($user->is_active ? 'Yes' : 'No') : 'Column not exists') . "\n";
echo "- Created: " . $user->created_at . "\n";

// Check table structure
$columns = DB::getSchemaBuilder()->getColumnListing('users');
echo "\nUsers table columns: " . implode(', ', $columns) . "\n";