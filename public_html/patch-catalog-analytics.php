<?php
/**
 * Patch: Buat tabel package_view_logs untuk analytics katalog paket
 * dan diagnosa konfigurasi GA4.
 *
 * Akses: https://hmtourtravel.com/patch-catalog-analytics.php
 * HAPUS FILE INI SETELAH DIJALANKAN!
 */
define('LARAVEL_START', microtime(true));

$paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../laravel_app/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];
foreach ($paths as $path) {
    if (file_exists($path)) { require_once $path; break; }
}

$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    __DIR__ . '/../laravel_app/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
];
$app = null;
foreach ($bootstrapPaths as $bPath) {
    if (file_exists($bPath)) { $app = require_once $bPath; break; }
}
if (!$app) die("<p style='color:red'>❌ bootstrap/app.php tidak ditemukan.</p>");
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<style>
body{font-family:Arial;padding:20px;max-width:900px;margin:0 auto;line-height:1.6}
.ok{color:#16a34a;font-weight:bold} .err{color:#dc2626;font-weight:bold}
.warn{color:#d97706;font-weight:bold} .info{color:#2563eb}
pre{background:#f4f4f4;padding:12px;border-radius:4px;font-size:12px;overflow-x:auto;word-break:break-all}
h2{border-bottom:2px solid #374151;padding-bottom:8px} h3{color:#374151;margin-top:20px}
</style>";

echo "<h2>🔧 Patch: Catalog Analytics Setup</h2>";
$errors = 0;

// ── 1. Tabel package_view_logs ─────────────────────────────────────────────
echo "<h3>1. Tabel <code>package_view_logs</code></h3>";
try {
    $exists = DB::select("SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='package_view_logs'");
    if (($exists[0]->cnt ?? 0) > 0) {
        echo "<p class='ok'>✅ Tabel sudah ada.</p>";
    } else {
        DB::statement("
            CREATE TABLE `package_view_logs` (
                `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `travel_package_id` BIGINT UNSIGNED NOT NULL,
                `viewed_date`       DATE NOT NULL,
                `view_count`        INT UNSIGNED NOT NULL DEFAULT 1,
                `referrer`          VARCHAR(500) NULL,
                `source`            VARCHAR(50) NULL,
                `created_at`        TIMESTAMP NULL,
                `updated_at`        TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `pvl_pkg_date_idx` (`travel_package_id`, `viewed_date`),
                CONSTRAINT `fk_pvl_package`
                    FOREIGN KEY (`travel_package_id`) REFERENCES `travel_packages`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p class='ok'>✅ Tabel berhasil dibuat.</p>";
        // Catat ke migrations
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert(['migration' => '2026_06_08_000004_create_package_view_logs_table', 'batch' => $maxBatch + 1]);
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $errors++;
}

// ── 2. Diagnosa GA4 config ────────────────────────────────────────────────
echo "<h3>2. Diagnosa Konfigurasi GA4</h3>";

$ga4PropertyId   = env('GA4_PROPERTY_ID', '');
$ga4CredPath     = env('GA4_CREDENTIALS_PATH', '');

echo "<p><strong>GA4_PROPERTY_ID:</strong> <code>" . htmlspecialchars($ga4PropertyId ?: '(kosong)') . "</code></p>";
echo "<p><strong>GA4_CREDENTIALS_PATH:</strong> <code>" . htmlspecialchars($ga4CredPath ?: '(kosong)') . "</code></p>";

if (empty($ga4PropertyId)) {
    echo "<p class='err'>❌ GA4_PROPERTY_ID belum diset di .env</p>";
    $errors++;
} else {
    echo "<p class='ok'>✅ GA4_PROPERTY_ID: " . htmlspecialchars($ga4PropertyId) . "</p>";
}

if (empty($ga4CredPath)) {
    echo "<p class='err'>❌ GA4_CREDENTIALS_PATH belum diset di .env</p>";
    $errors++;
} elseif (!file_exists($ga4CredPath)) {
    echo "<p class='err'>❌ File tidak ditemukan di path: <code>" . htmlspecialchars($ga4CredPath) . "</code></p>";
    echo "<p class='warn'>Coba path alternatif berikut:</p><ul>";

    // Cari file JSON di beberapa lokasi umum
    $candidates = [
        __DIR__ . '/morratravel-0d88fbae3177.json',
        __DIR__ . '/../morratravel-0d88fbae3177.json',
        __DIR__ . '/../storage/app/morratravel-0d88fbae3177.json',
        '/home/u127727849/domains/hmtourtravel.com/morratravel-0d88fbae3177.json',
        '/home/u127727849/domains/hmtourtravel.com/storage/app/morratravel-0d88fbae3177.json',
    ];
    foreach ($candidates as $c) {
        $found = file_exists($c);
        echo "<li class='" . ($found ? 'ok' : 'info') . "'>" . ($found ? '✅ DITEMUKAN' : '🔍 tidak ada') . ": <code>" . htmlspecialchars($c) . "</code></li>";
    }
    echo "</ul>";
    $errors++;
} else {
    echo "<p class='ok'>✅ File JSON ditemukan: <code>" . htmlspecialchars($ga4CredPath) . "</code></p>";

    // Validasi isi file
    $json = json_decode(file_get_contents($ga4CredPath), true);
    if (!$json) {
        echo "<p class='err'>❌ File JSON tidak valid / tidak bisa dibaca.</p>";
        $errors++;
    } else {
        echo "<p class='ok'>✅ File JSON valid.</p>";
        echo "<p><strong>client_email:</strong> <code>" . htmlspecialchars($json['client_email'] ?? '(tidak ada)') . "</code></p>";
        echo "<p><strong>project_id:</strong> <code>" . htmlspecialchars($json['project_id'] ?? '(tidak ada)') . "</code></p>";
        echo "<p><strong>type:</strong> <code>" . htmlspecialchars($json['type'] ?? '(tidak ada)') . "</code></p>";

        if (($json['type'] ?? '') !== 'service_account') {
            echo "<p class='err'>❌ Tipe bukan service_account. File mungkin salah.</p>";
            $errors++;
        }
        if (empty($json['private_key'])) {
            echo "<p class='err'>❌ private_key tidak ada di file JSON.</p>";
            $errors++;
        } else {
            echo "<p class='ok'>✅ private_key ada.</p>";
        }
    }
}

// ── 3. Tampilkan path yang benar untuk .env ────────────────────────────────
echo "<h3>3. Path yang Disarankan untuk .env</h3>";
echo "<p>Berdasarkan struktur server ini, gunakan salah satu path berikut di .env:</p>";
echo "<pre>
# Opsi 1 - jika file JSON ada di root domain (public_html)
GA4_CREDENTIALS_PATH=" . htmlspecialchars(__DIR__ . '/morratravel-0d88fbae3177.json') . "

# Opsi 2 - jika file JSON ada di satu level atas public_html
GA4_CREDENTIALS_PATH=" . htmlspecialchars(dirname(__DIR__) . '/morratravel-0d88fbae3177.json') . "

# Opsi 3 - disarankan: simpan di storage/app (lebih aman)
GA4_CREDENTIALS_PATH=" . htmlspecialchars(dirname(__DIR__) . '/storage/app/google-analytics-credentials.json') . "
</pre>";

echo "<hr>";
if ($errors === 0) {
    echo "<p class='ok' style='font-size:1.1em'>✅ Setup selesai tanpa error.</p>";
} else {
    echo "<p class='err' style='font-size:1.1em'>❌ Ada $errors masalah yang perlu diperbaiki. Periksa output di atas.</p>";
}
echo "<p style='color:#dc2626;font-weight:bold;margin-top:20px'>🔒 HAPUS FILE INI setelah selesai!</p>";
?>
