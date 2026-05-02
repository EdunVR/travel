<?php

// Fix due date decimal issue in sales invoice print
$filePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
$content = file_get_contents($filePath);

// Replace all instances of diffInDays calculation to cast to int
$content = str_replace(
    '$diffInDays = $today->diffInDays($dueDate, false);',
    '$diffInDays = (int) $today->diffInDays($dueDate, false);',
    $content
);

file_put_contents($filePath, $content);

echo "Fixed due date decimal calculations in sales invoice print file.\n";