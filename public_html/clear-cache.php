<?php
/**
 * Clear Laravel cache: views, config, routes
 * Akses: https://hmtourtravel.com/clear-cache.php
 * HAPUS SETELAH DIGUNAKAN
 */
define('LARAVEL_START', microtime(true));
$paths = [__DIR__.'/vendor/autoload.php', __DIR__.'/../vendor/autoload.php'];
foreach ($paths as $p) { if (file_exists($p)) { require_once $p; break; } }
$bPaths = [__DIR__.'/bootstrap/app.php', __DIR__.'/../bootstrap/app.php'];
$app = null;
foreach ($bPaths as $b) { if (file_exists($b)) { $app = require_once $b; break; } }
if (!$app) die('bootstrap/app.php not found');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<style>body{font-family:Arial;padding:20px}.ok{color:green}.err{color:red}</style>";
echo "<h2>🧹 Clear Cache</h2>";

$commands = [
    'view:clear'   => 'Compiled views cleared',
    'config:clear' => 'Configuration cache cleared',
    'route:clear'  => 'Route cache cleared',
    'cache:clear'  => 'Application cache cleared',
];

foreach ($commands as $cmd => $label) {
    try {
        $exitCode = $kernel->call($cmd);
        echo "<p class='" . ($exitCode === 0 ? 'ok' : 'err') . "'>✅ $label</p>";
    } catch (\Exception $e) {
        echo "<p class='err'>❌ $cmd: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Verifikasi: cek apakah analytics view ada
$analyticsView = dirname(__DIR__) . '/resources/views/admin/travel/catalog/analytics.blade.php';
echo "<p><strong>Analytics view exists:</strong> " . (file_exists($analyticsView) ? '<span class="ok">✅ YES</span>' : '<span class="err">❌ NO</span>') . "</p>";

// Cek package-card component punya prop $href
$cardView = dirname(__DIR__) . '/resources/views/components/package-card.blade.php';
if (file_exists($cardView)) {
    $content = file_get_contents($cardView);
    $hasHref = str_contains($content, '$href') || str_contains($content, 'href');
    echo "<p><strong>Package card has href prop:</strong> " . ($hasHref ? '<span class="ok">✅ YES</span>' : '<span class="err">❌ NO - prop belum ada</span>') . "</p>";
    echo "<p><strong>First line:</strong> <code>" . htmlspecialchars(substr($content, 0, 100)) . "</code></p>";
}

echo "<p style='color:red;font-weight:bold;margin-top:20px'>🔒 HAPUS file ini!</p>";
?>
