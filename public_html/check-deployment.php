<?php
/**
 * Diagnosa: cek apakah file-file terbaru sudah ada di server
 * Akses: https://hmtourtravel.com/check-deployment.php
 * HAPUS SETELAH DIGUNAKAN
 */
$basePath = dirname(__DIR__); // satu level atas public_html

$filesToCheck = [
    // File baru yang harus ada
    'resources/views/admin/travel/catalog/analytics.blade.php' => 'Analytics view (baru)',
    'app/Models/PackageViewLog.php'                             => 'PackageViewLog model (baru)',
    'app/Models/TransferRequestItem.php'                        => 'TransferRequestItem model (baru)',
    // File yang diupdate
    'app/Http/Controllers/PackageCatalogController.php'         => 'PackageCatalogController (diupdate)',
    'resources/views/components/package-card.blade.php'         => 'Package card component (diupdate)',
    'resources/views/admin/travel/catalog/index.blade.php'      => 'Catalog index (diupdate)',
];

// Cek konten spesifik untuk memastikan versi baru
$contentChecks = [
    'app/Http/Controllers/PackageCatalogController.php' => [
        'text'    => 'public function analytics',
        'label'   => 'Method analytics() ada',
    ],
    'resources/views/components/package-card.blade.php' => [
        'text'    => "\$href",
        'label'   => 'Prop $href ada',
    ],
    'resources/views/admin/travel/catalog/index.blade.php' => [
        'text'    => 'travel.catalog.analytics',
        'label'   => 'Link ke analytics ada',
    ],
];

echo "<style>
body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto}
.ok{color:#16a34a;font-weight:bold} .err{color:#dc2626;font-weight:bold}
.warn{color:#d97706} pre{background:#f4f4f4;padding:8px;border-radius:4px;font-size:12px}
h3{margin-top:20px}
</style>";

echo "<h2>🔍 Deployment Check</h2>";
echo "<p>Base path: <code>" . htmlspecialchars($basePath) . "</code></p>";

echo "<h3>1. Keberadaan File</h3>";
$allOk = true;
foreach ($filesToCheck as $file => $label) {
    $fullPath = $basePath . '/' . $file;
    $exists   = file_exists($fullPath);
    if (!$exists) $allOk = false;
    echo "<p class='" . ($exists ? 'ok' : 'err') . "'>";
    echo $exists ? '✅' : '❌';
    echo " <strong>{$label}</strong><br>";
    echo "<small><code>" . htmlspecialchars($file) . "</code></small>";
    if ($exists) {
        echo " — " . round(filesize($fullPath) / 1024, 1) . " KB, modified: " . date('d/m/Y H:i', filemtime($fullPath));
    }
    echo "</p>";
}

echo "<h3>2. Konten File</h3>";
foreach ($contentChecks as $file => $check) {
    $fullPath = $basePath . '/' . $file;
    if (!file_exists($fullPath)) {
        echo "<p class='err'>❌ File tidak ada: {$file}</p>";
        continue;
    }
    $content = file_get_contents($fullPath);
    $found   = str_contains($content, $check['text']);
    echo "<p class='" . ($found ? 'ok' : 'err') . "'>";
    echo $found ? '✅' : '❌';
    echo " {$check['label']} di <code>" . htmlspecialchars($file) . "</code></p>";
}

echo "<h3>3. Rekomendasi</h3>";
if (!$allOk) {
    echo "<div style='background:#fef2f2;border:1px solid #fca5a5;padding:12px;border-radius:8px'>";
    echo "<p class='err'>❌ Beberapa file belum ada di server. Server perlu di-update dari GitHub.</p>";
    echo "<p>Cara update di Hostinger hPanel:</p>";
    echo "<ol>
        <li>Masuk ke <strong>hPanel → Websites → Git</strong></li>
        <li>Klik <strong>Pull</strong> atau <strong>Deploy</strong></li>
        <li>Atau upload file yang kurang secara manual via File Manager</li>
    </ol>";
    echo "</div>";
} else {
    echo "<p class='ok'>✅ Semua file sudah ada. Coba clear cache Laravel:</p>";
    echo "<pre>php artisan view:clear
php artisan config:clear
php artisan route:clear</pre>";
}

echo "<p style='color:#dc2626;font-weight:bold;margin-top:20px'>🔒 HAPUS file ini setelah selesai!</p>";
?>
