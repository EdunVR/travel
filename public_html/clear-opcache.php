<?php
/**
 * Clear PHP OPcache agar kode terbaru digunakan
 * Akses: https://hmtourtravel.com/clear-opcache.php
 * HAPUS SETELAH DIGUNAKAN
 * 
 * sadskdsakdksa
 */
echo "<style>body{font-family:Arial;padding:20px}.ok{color:green;font-weight:bold}.err{color:red;font-weight:bold}</style>";
echo "<h2>🔄 Clear OPcache & Laravel Cache</h2>";

// 1. OPcache
if (function_exists('opcache_reset')) {
    $result = opcache_reset();
    echo "<p class='" . ($result ? 'ok' : 'err') . "'>" . ($result ? '✅' : '❌') . " opcache_reset()</p>";
} else {
    echo "<p>⚠️ OPcache tidak aktif atau tidak tersedia.</p>";
}

// 2. Artisan cache clear
define('LARAVEL_START', microtime(true));
$paths = [__DIR__.'/vendor/autoload.php', __DIR__.'/../vendor/autoload.php'];
foreach ($paths as $p) { if (file_exists($p)) { require_once $p; break; } }
$bPaths = [__DIR__.'/bootstrap/app.php', __DIR__.'/../bootstrap/app.php'];
$app = null;
foreach ($bPaths as $b) { if (file_exists($b)) { $app = require_once $b; break; } }

if ($app) {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    foreach (['view:clear', 'config:clear', 'route:clear', 'cache:clear'] as $cmd) {
        try {
            $kernel->call($cmd);
            echo "<p class='ok'>✅ $cmd</p>";
        } catch (\Exception $e) {
            echo "<p class='err'>❌ $cmd: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // 3. Verifikasi kode controller sudah yang baru
    $controllerFile = dirname(__DIR__) . '/app/Http/Controllers/Api/MobileAttendanceController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        $hasSelfie = str_contains($content, 'selfie_in') && str_contains($content, 'saveSelfie');
        echo "<p class='" . ($hasSelfie ? 'ok' : 'err') . "'>";
        echo $hasSelfie
            ? '✅ MobileAttendanceController sudah versi baru (ada selfie support)'
            : '❌ MobileAttendanceController BELUM diupdate — kode lama masih digunakan!';
        echo "</p>";

        if (!$hasSelfie) {
            echo "<p>File di server belum ter-update. Cek Hostinger Git integration apakah pull sudah berjalan.</p>";
            echo "<p>Modified: " . date('d/m/Y H:i:s', filemtime($controllerFile)) . "</p>";
        } else {
            echo "<p>Modified: " . date('d/m/Y H:i:s', filemtime($controllerFile)) . "</p>";
        }
    }
} else {
    echo "<p class='err'>❌ Bootstrap Laravel gagal.</p>";
}

echo "<hr><p style='color:red;font-weight:bold'>🔒 HAPUS file ini!</p>";
?>
