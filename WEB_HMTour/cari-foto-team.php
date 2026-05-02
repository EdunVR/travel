<?php
/**
 * Script untuk mencari foto team di WordPress
 * Upload file ini ke root WordPress dan akses via browser
 * URL: http://your-domain.com/cari-foto-team.php
 */

// Load WordPress
require_once('wp-load.php');

// Security check - hanya bisa diakses oleh admin
if (!current_user_can('administrator')) {
    die('Access denied. Admin only.');
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Foto Team - HM Tour & Travel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d4af37;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .search-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .search-form input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .search-form button {
            padding: 10px 25px;
            background: #d4af37;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .search-form button:hover {
            background: #c9a02c;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 15px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
        }
        .filter-btn.active {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #d4af37 0%, #c9a02c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .photo-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .photo-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .photo-info {
            padding: 15px;
        }
        .photo-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .photo-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        .photo-actions {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #d4af37;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .no-results i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #d4af37;
        }
        .team-names {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .team-names h3 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .team-names ul {
            columns: 3;
            list-style: none;
            font-size: 13px;
            color: #856404;
        }
        .team-names li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Pencarian Foto Team HM Tour & Travel</h1>
        <p class="subtitle">Temukan foto team untuk website</p>

        <div class="team-names">
            <h3>📋 Daftar Nama Team yang Dicari:</h3>
            <ul>
                <li>H. Ilham M. Hikami (Direktur)</li>
                <li>Hj. Nurickeu Mutia (Wakil Direktur)</li>
                <li>Husni Mubarok (General Manager)</li>
                <li>Yusup Andrian (Digital Marketing)</li>
                <li>Yugi Adzani (Graphic Designer)</li>
                <li>Hj. Gesa Nachwa (Administrasi)</li>
                <li>Alfi Syahrin (Accounting)</li>
                <li>Riksyan Ilyas (Operasional)</li>
                <li>Faiz Nauval (Video Editor)</li>
                <li>Deni Suryadi (Ticketing)</li>
                <li>M. Abdul Aziz (Finance)</li>
                <li>Nely Puspitasari (Customer Service)</li>
                <li>M. Farhan (Digital Creator)</li>
                <li>M. Rian (Hotel Reservasi)</li>
                <li>Resa Rahman (Administrasi)</li>
            </ul>
        </div>

        <div class="search-section">
            <form class="search-form" method="GET">
                <input type="text" name="search" placeholder="Cari nama, kata kunci..." value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
                <button type="submit">Cari</button>
            </form>
            
            <div class="filter-buttons">
                <button class="filter-btn <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>" onclick="window.location.href='?'">Semua</button>
                <button class="filter-btn <?php echo isset($_GET['filter']) && $_GET['filter'] == 'team' ? 'active' : ''; ?>" onclick="window.location.href='?filter=team'">Kata Kunci: Team</button>
                <button class="filter-btn <?php echo isset($_GET['filter']) && $_GET['filter'] == 'names' ? 'active' : ''; ?>" onclick="window.location.href='?filter=names'">Nama Team</button>
                <button class="filter-btn <?php echo isset($_GET['filter']) && $_GET['filter'] == 'recent' ? 'active' : ''; ?>" onclick="window.location.href='?filter=recent'">Terbaru</button>
                <button class="filter-btn <?php echo isset($_GET['filter']) && $_GET['filter'] == 'formal' ? 'active' : ''; ?>" onclick="window.location.href='?filter=formal'">Formal/Jas</button>
            </div>
        </div>

        <?php
        // Nama-nama team yang dicari
        $team_names = array(
            'ilham', 'nurickeu', 'husni', 'yusup', 'yugi', 
            'gesa', 'alfi', 'riksyan', 'faiz', 'deni', 
            'abdul', 'aziz', 'nely', 'farhan', 'rian', 'resa'
        );

        // Build query arguments
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Apply filters
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $args['s'] = sanitize_text_field($_GET['search']);
        } elseif (isset($_GET['filter'])) {
            $filter = sanitize_text_field($_GET['filter']);
            
            switch ($filter) {
                case 'team':
                    $args['s'] = 'team staff karyawan';
                    break;
                case 'names':
                    // Will be handled with meta query
                    break;
                case 'recent':
                    $args['date_query'] = array(
                        array(
                            'after' => '2 years ago'
                        )
                    );
                    break;
                case 'formal':
                    $args['s'] = 'jas formal suit';
                    break;
            }
        }

        // Get images
        $images = get_posts($args);

        // Filter by team names if needed
        if (isset($_GET['filter']) && $_GET['filter'] == 'names') {
            $images = array_filter($images, function($image) use ($team_names) {
                $title = strtolower($image->post_title);
                $name = strtolower($image->post_name);
                
                foreach ($team_names as $team_name) {
                    if (strpos($title, $team_name) !== false || strpos($name, $team_name) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Count statistics
        $total_images = count($images);
        $team_images = 0;
        $recent_images = 0;

        foreach ($images as $image) {
            $title = strtolower($image->post_title);
            if (strpos($title, 'team') !== false || strpos($title, 'staff') !== false) {
                $team_images++;
            }
            
            $date = strtotime($image->post_date);
            if ($date > strtotime('-1 year')) {
                $recent_images++;
            }
        }
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_images; ?></div>
                <div class="stat-label">Total Foto Ditemukan</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $team_images; ?></div>
                <div class="stat-label">Foto dengan Kata "Team"</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $recent_images; ?></div>
                <div class="stat-label">Foto Tahun Ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15</div>
                <div class="stat-label">Anggota Team</div>
            </div>
        </div>

        <?php if (empty($images)): ?>
            <div class="no-results">
                <i>📷</i>
                <h3>Tidak ada foto ditemukan</h3>
                <p>Coba gunakan kata kunci lain atau filter berbeda</p>
            </div>
        <?php else: ?>
            <div class="gallery">
                <?php foreach ($images as $image): 
                    $image_url = wp_get_attachment_url($image->ID);
                    $image_thumb = wp_get_attachment_image_src($image->ID, 'medium');
                    $image_meta = wp_get_attachment_metadata($image->ID);
                    $file_size = size_format(filesize(get_attached_file($image->ID)));
                    $dimensions = isset($image_meta['width']) ? $image_meta['width'] . ' x ' . $image_meta['height'] : 'N/A';
                ?>
                    <div class="photo-card">
                        <img src="<?php echo esc_url($image_thumb[0]); ?>" alt="<?php echo esc_attr($image->post_title); ?>" loading="lazy">
                        <div class="photo-info">
                            <div class="photo-title"><?php echo esc_html($image->post_title); ?></div>
                            <div class="photo-meta">
                                📅 <?php echo date('d M Y', strtotime($image->post_date)); ?><br>
                                📐 <?php echo $dimensions; ?><br>
                                💾 <?php echo $file_size; ?><br>
                                🆔 ID: <?php echo $image->ID; ?>
                            </div>
                            <div class="photo-actions">
                                <a href="<?php echo esc_url($image_url); ?>" target="_blank" class="btn btn-primary">Lihat</a>
                                <a href="<?php echo admin_url('post.php?post=' . $image->ID . '&action=edit'); ?>" target="_blank" class="btn btn-secondary">Edit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
            <h3 style="color: #d4af37; margin-bottom: 15px;">💡 Tips Pencarian:</h3>
            <ul style="line-height: 1.8; color: #666;">
                <li>Gunakan filter "Nama Team" untuk mencari foto berdasarkan nama anggota team</li>
                <li>Filter "Formal/Jas" untuk mencari foto dengan pakaian formal</li>
                <li>Klik "Lihat" untuk melihat foto ukuran penuh</li>
                <li>Klik "Edit" untuk mengedit informasi foto di WordPress admin</li>
                <li>Catat ID foto yang sesuai untuk digunakan di Elementor</li>
            </ul>
        </div>
    </div>
</body>
</html>
