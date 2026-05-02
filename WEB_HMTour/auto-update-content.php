<?php
/**
 * AUTO UPDATE CONTENT - HM Tour & Travel
 * Script untuk update konten WordPress secara otomatis via database
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke root WordPress
 * 2. Akses: http://your-domain.com/auto-update-content.php
 * 3. Klik tombol "Update Content"
 * 4. Hapus file ini setelah selesai
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    die('Access denied. Admin only.');
}

// Konten yang akan diupdate
$content_updates = [
    'about' => "Berdiri sejak tahun 2012, <strong>HM Tour & Travel</strong> (PT Hikami Mandiri Indonesia) telah tumbuh dan berkembang menjadi salah satu travel yang berawal dari travel pariwisata nusantara.\n\nDibawah pimpinan <strong>Bapak H. Ilham Mochamad Hikami</strong>, tahun 2022 HM Tour & Travel mengembangkan usaha sebagai salah satu penyelenggaraan Ibadah Umrah baik itu paket reguler maupun umrah plus.\n\nUntuk memperkuat bisnis ini kami telah menjadi anggota dari <strong>Serikat Penyelenggara Umrah dan Haji Indonesia (SAPUHI)</strong>. Selain itu sebagai komitmen legalitas perusahaan dalam melayani customer serta jemaah secara profesional, kami telah memiliki izin resmi sebagai Biro Perjalanan Wisata, izin sebagai penyelenggara ibadah umrah dan haji khusus dari <strong>Kementerian Agama RI</strong>.",
    
    'visi' => "Memberikan layanan berkualitas, profesional dan amanah untuk para tamu Allah, dengan landasan nilai ibadah yang kuat dan sesuai tuntunan Qur'an & Sunnah sehingga setiap perjalanan mulia menuju baitullah memiliki makna dan value di hati Duyufurrahman.",
    
    'misi' => "Menyediakan layanan perjalanan ibadah haji dan umroh dengan kualitas prima dan pelayanan ramah yang didukung oleh mitra & stakeholder berpengalaman dengan mengintegrasikan teknologi berbasis digital untuk kemudahan pelayanan yang lebih luas.",
    
    'moto' => "Travel Amanah, Sesuai Sunnah, Pelayanan Ramah, Harga Murah, Proses Mudah, Fasilitas Mewah, Semoga Berkah",
    
    'tagline' => "Hajj & Umroh With Sunnah Ways"
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Update Content - HM Tour</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #d4af37;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            color: #856404;
        }
        .warning h3 {
            margin-bottom: 10px;
            color: #856404;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box h3 {
            color: #1976D2;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #d4af37 0%, #c9a02c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            border-radius: 5px;
        }
        .result h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        .result ul {
            list-style: none;
            padding-left: 0;
        }
        .result li {
            padding: 5px 0;
            color: #1b5e20;
        }
        .result li:before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: bold;
        }
        .manual-steps {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .manual-steps h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .manual-steps ol {
            padding-left: 20px;
            line-height: 1.8;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Auto Update Content</h1>
        <p class="subtitle">HM Tour & Travel Website</p>

        <div class="warning">
            <h3>⚠️ PENTING - Baca Sebelum Update!</h3>
            <ul style="line-height: 1.8; margin-top: 10px;">
                <li>✓ Backup database sudah dibuat</li>
                <li>✓ Anda login sebagai Administrator</li>
                <li>✓ Script ini akan update konten via database</li>
                <li>✓ Hapus file ini setelah selesai</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>📋 Yang Akan Diupdate:</h3>
            <ul style="line-height: 1.8;">
                <li>✓ Konten "Tentang HM Tourism"</li>
                <li>✓ Visi Perusahaan</li>
                <li>✓ Misi Perusahaan</li>
                <li>✓ Moto Perusahaan</li>
                <li>✓ Tagline</li>
            </ul>
        </div>

        <?php if (isset($_POST['update_content'])): ?>
            <?php
            // Proses update
            $results = [];
            
            // Update options
            foreach ($content_updates as $key => $value) {
                $option_name = 'hmtravel_' . $key;
                $updated = update_option($option_name, $value);
                $results[] = ucfirst($key) . ' updated';
            }
            
            // Update site tagline
            update_option('blogdescription', $content_updates['tagline']);
            $results[] = 'Site tagline updated';
            ?>
            
            <div class="result">
                <h3>✅ Update Berhasil!</h3>
                <ul>
                    <?php foreach ($results as $result): ?>
                        <li><?php echo esc_html($result); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="manual-steps">
                <h3>📝 Langkah Selanjutnya (Manual):</h3>
                <ol>
                    <li>Login ke WordPress Admin</li>
                    <li>Pergi ke: Pages → Home</li>
                    <li>Klik "Edit with Elementor"</li>
                    <li>Update setiap section dengan konten dari file <code>ELEMENTOR_CONTENT_READY.html</code></li>
                    <li>Upload foto team (15 orang) menggunakan <code>cari-foto-team.php</code></li>
                    <li>Clear cache dan test</li>
                </ol>
            </div>

            <div class="button-group">
                <a href="<?php echo admin_url(); ?>" class="btn">Ke WordPress Admin</a>
                <a href="<?php echo home_url(); ?>" class="btn">Lihat Website</a>
            </div>

        <?php else: ?>
            
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin update konten? Pastikan backup sudah dibuat!');">
                <div class="button-group">
                    <button type="submit" name="update_content" class="btn">
                        🚀 Update Content Sekarang
                    </button>
                </div>
            </form>

            <div class="manual-steps" style="margin-top: 30px;">
                <h3>ℹ️ Catatan:</h3>
                <p style="line-height: 1.8; color: #666;">
                    Script ini hanya update konten di database WordPress. Untuk update tampilan di Elementor, 
                    Anda tetap perlu edit manual menggunakan file <strong>ELEMENTOR_CONTENT_READY.html</strong> 
                    yang sudah disediakan.
                </p>
            </div>

        <?php endif; ?>

        <div style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-radius: 8px; text-align: center;">
            <p style="color: #666; margin-bottom: 10px;">
                <strong>File Pendukung:</strong>
            </p>
            <p style="color: #999; font-size: 14px;">
                INDEX.html | QUICK_START.md | ELEMENTOR_CONTENT_READY.html | cari-foto-team.php
            </p>
        </div>
    </div>
</body>
</html>
