<?php
// Fix Permissions - Create cache folders and set permissions
// Akses: https://hmtourtravel.com/fix-permissions.php

echo "<h1>🔧 Fix Permissions & Create Cache Folders</h1>";

$laravelPath = __DIR__ . '/../laravel_app';

echo "<h2>Step 1: Create Required Folders</h2>";

$folders = [
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($folders as $folder) {
    $fullPath = $laravelPath . '/' . $folder;
    
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0775, true)) {
            echo "✓ Created: <code>$folder</code><br>";
        } else {
            echo "✗ Failed to create: <code>$folder</code><br>";
        }
    } else {
        echo "✓ Already exists: <code>$folder</code><br>";
    }
}

echo "<br><h2>Step 2: Set Permissions</h2>";

$permissionFolders = [
    'storage',
    'bootstrap/cache'
];

foreach ($permissionFolders as $folder) {
    $fullPath = $laravelPath . '/' . $folder;
    
    if (is_dir($fullPath)) {
        // Try to set permission
        if (chmod($fullPath, 0775)) {
            echo "✓ Set permission 775 on: <code>$folder</code><br>";
        } else {
            echo "⚠️ Could not set permission on: <code>$folder</code> (might need SSH)<br>";
        }
        
        // Check if writable
        if (is_writable($fullPath)) {
            echo "✓ <code>$folder</code> is writable<br>";
        } else {
            echo "✗ <code>$folder</code> is NOT writable<br>";
        }
    }
}

echo "<br><h2>Step 3: Create .gitignore Files</h2>";

$gitignoreFolders = [
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs'
];

foreach ($gitignoreFolders as $folder) {
    $fullPath = $laravelPath . '/' . $folder;
    $gitignorePath = $fullPath . '/.gitignore';
    
    if (is_dir($fullPath) && !file_exists($gitignorePath)) {
        $content = "*\n!.gitignore\n";
        if (file_put_contents($gitignorePath, $content)) {
            echo "✓ Created .gitignore in: <code>$folder</code><br>";
        }
    }
}

echo "<br><h2>Step 4: Test Write Access</h2>";

$testFile = $laravelPath . '/storage/framework/views/test.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✓ Can write to storage/framework/views<br>";
    unlink($testFile);
} else {
    echo "✗ Cannot write to storage/framework/views<br>";
    echo "<p style='color:red;'><strong>You need to set permissions via SSH or File Manager!</strong></p>";
}

echo "<br><h2>📋 Summary</h2>";

$viewsPath = $laravelPath . '/storage/framework/views';
if (is_dir($viewsPath) && is_writable($viewsPath)) {
    echo "<p style='color:green; font-size:18px;'><strong>✓✓✓ All folders created and writable!</strong></p>";
    echo "<p>Now test your website: <a href='https://hmtourtravel.com'>https://hmtourtravel.com</a></p>";
    echo "<br><p><strong>Next steps via SSH:</strong></p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "ssh u127727849@hmtourtravel.com\n";
    echo "cd /home/u127727849/domains/hmtourtravel.com/laravel_app\n";
    echo "php artisan config:clear\n";
    echo "php artisan cache:clear\n";
    echo "php artisan migrate --force\n";
    echo "</pre>";
} else {
    echo "<p style='color:red; font-size:18px;'><strong>⚠️ Folders created but not writable!</strong></p>";
    echo "<p>You need to set permissions via SSH:</p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "ssh u127727849@hmtourtravel.com\n";
    echo "cd /home/u127727849/domains/hmtourtravel.com/laravel_app\n";
    echo "chmod -R 775 storage bootstrap/cache\n";
    echo "chown -R u127727849:u127727849 storage bootstrap/cache\n";
    echo "</pre>";
}

echo "<br><hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH SELESAI!</strong></p>";
?>
