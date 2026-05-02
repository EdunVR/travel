<?php

$timestamp = time();
$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
$content = file_get_contents($viewFile);
$content = preg_replace('/inter-outlet\.js\?v=\d+/', 'inter-outlet.js?v=' . $timestamp, $content);
file_put_contents($viewFile, $content);
echo "Updated cache busting version: ?v={$timestamp}\n";