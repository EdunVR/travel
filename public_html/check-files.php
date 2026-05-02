<?php
// Check Files in public_html
// Akses: https://hmtourtravel.com/check-files.php

echo "<h1>📁 Check Files in public_html</h1>";

$publicHtmlPath = __DIR__;

echo "<h2>Current Directory:</h2>";
echo "<code>$publicHtmlPath</code><br><br>";

echo "<h2>Files in public_html:</h2>";

$files = scandir($publicHtmlPath);

echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
echo "<tr><th>Filename</th><th>Type</th><th>Size</th><th>Modified</th><th>First 200 chars</th></tr>";

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $filePath = $publicHtmlPath . '/' . $file;
    $isDir = is_dir($filePath);
    $size = $isDir ? '-' : number_format(filesize($filePath)) . ' bytes';
    $modified = date('Y-m-d H:i:s', filemtime($filePath));
    
    $preview = '';
    if (!$isDir && filesize($filePath) < 100000) {
        $content = file_get_contents($filePath);
        $preview = htmlspecialchars(substr($content, 0, 200));
        if (strlen($content) > 200) $preview .= '...';
    }
    
    $type = $isDir ? '📁 Directory' : '📄 File';
    
    // Highlight important files
    $style = '';
    if ($file === 'index.php' || $file === 'index.html') {
        $style = 'background-color: yellow; font-weight: bold;';
    }
    
    echo "<tr style='$style'>";
    echo "<td>$file</td>";
    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modified</td>";
    echo "<td style='font-size:11px; font-family:monospace;'>$preview</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br><h2>🔍 Check index.* files:</h2>";

$indexFiles = ['index.php', 'index.html', 'index.htm'];

foreach ($indexFiles as $indexFile) {
    $indexPath = $publicHtmlPath . '/' . $indexFile;
    
    if (file_exists($indexPath)) {
        echo "<h3 style='color:red;'>⚠️ Found: $indexFile</h3>";
        echo "Size: " . filesize($indexPath) . " bytes<br>";
        echo "Modified: " . date('Y-m-d H:i:s', filemtime($indexPath)) . "<br>";
        
        $content = file_get_contents($indexPath);
        
        // Check if it's Laravel index.php
        if ($indexFile === 'index.php') {
            if (strpos($content, 'laravel_app') !== false) {
                echo "<p style='color:green;'><strong>✓ This is Laravel index.php</strong></p>";
            } else {
                echo "<p style='color:red;'><strong>✗ This is NOT Laravel index.php!</strong></p>";
                echo "<p>It contains:</p>";
                echo "<pre style='background:#f5f5f5; padding:10px; max-height:300px; overflow:auto;'>";
                echo htmlspecialchars(substr($content, 0, 1000));
                if (strlen($content) > 1000) echo "\n\n... (truncated)";
                echo "</pre>";
            }
        } else {
            echo "<p style='color:orange;'><strong>⚠️ index.html exists! This might override index.php</strong></p>";
            echo "<p>Content preview:</p>";
            echo "<pre style='background:#f5f5f5; padding:10px; max-height:300px; overflow:auto;'>";
            echo htmlspecialchars(substr($content, 0, 1000));
            if (strlen($content) > 1000) echo "\n\n... (truncated)";
            echo "</pre>";
            
            echo "<p><strong>ACTION: DELETE this file!</strong></p>";
        }
    } else {
        echo "<p>✓ $indexFile not found (good if not needed)</p>";
    }
}

echo "<br><h2>📋 Summary:</h2>";

$indexPhpPath = $publicHtmlPath . '/index.php';
$indexHtmlPath = $publicHtmlPath . '/index.html';

if (file_exists($indexHtmlPath)) {
    echo "<p style='color:red; font-size:18px;'><strong>❌ PROBLEM FOUND!</strong></p>";
    echo "<p>File <code>index.html</code> exists and is being served instead of <code>index.php</code></p>";
    echo "<p><strong>SOLUTION:</strong></p>";
    echo "<ol>";
    echo "<li>Delete <code>index.html</code> via File Manager</li>";
    echo "<li>Or rename it to <code>index.html.backup</code></li>";
    echo "<li>Then test <code>https://hmtourtravel.com</code> again</li>";
    echo "</ol>";
} elseif (!file_exists($indexPhpPath)) {
    echo "<p style='color:red; font-size:18px;'><strong>❌ PROBLEM FOUND!</strong></p>";
    echo "<p>File <code>index.php</code> does NOT exist!</p>";
    echo "<p><strong>SOLUTION:</strong> Upload index.php to public_html</p>";
} else {
    $content = file_get_contents($indexPhpPath);
    if (strpos($content, 'laravel_app') === false) {
        echo "<p style='color:red; font-size:18px;'><strong>❌ PROBLEM FOUND!</strong></p>";
        echo "<p>File <code>index.php</code> exists but it's NOT the Laravel version!</p>";
        echo "<p><strong>SOLUTION:</strong> Replace index.php with the correct Laravel version</p>";
    } else {
        echo "<p style='color:green; font-size:18px;'><strong>✓ index.php looks correct!</strong></p>";
        echo "<p>If website still shows wrong page, try:</p>";
        echo "<ol>";
        echo "<li>Clear browser cache (Ctrl+Shift+R)</li>";
        echo "<li>Open in Incognito mode</li>";
        echo "<li>Check if there's a subdomain or folder redirect</li>";
        echo "</ol>";
    }
}

echo "<br><hr>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH DEBUG!</strong></p>";
?>
