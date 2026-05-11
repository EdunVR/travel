<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$member = \App\Models\Member::find(18);

if (!$member) {
    echo "Member 18 not found\n";
    exit;
}

echo "Member 18:\n";
echo "full_name: '{$member->full_name}'\n";
echo "phone_number: '{$member->phone_number}'\n";

echo "\nAll attributes:\n";
$attrs = $member->getAttributes();
foreach ($attrs as $key => $value) {
    if (str_contains($key, 'name') || str_contains($key, 'phone') || str_contains($key, 'nama')) {
        echo "- $key: '$value'\n";
    }
}
