-- SQL Query untuk mencari foto team di WordPress Database
-- Jalankan query ini di phpMyAdmin atau MySQL client

-- 1. Cari semua attachment (foto) yang mengandung nama team
SELECT 
    ID,
    post_title,
    post_name,
    guid as url,
    post_date
FROM wp_posts 
WHERE post_type = 'attachment' 
AND post_mime_type LIKE 'image/%'
AND (
    post_title LIKE '%ilham%' OR
    post_title LIKE '%nurickeu%' OR
    post_title LIKE '%husni%' OR
    post_title LIKE '%yusup%' OR
    post_title LIKE '%yugi%' OR
    post_title LIKE '%gesa%' OR
    post_title LIKE '%alfi%' OR
    post_title LIKE '%riksyan%' OR
    post_title LIKE '%faiz%' OR
    post_title LIKE '%deni%' OR
    post_title LIKE '%abdul%' OR
    post_title LIKE '%nely%' OR
    post_title LIKE '%farhan%' OR
    post_title LIKE '%rian%' OR
    post_title LIKE '%resa%' OR
    post_title LIKE '%team%' OR
    post_title LIKE '%staff%' OR
    post_title LIKE '%karyawan%' OR
    post_name LIKE '%team%' OR
    post_name LIKE '%staff%'
)
ORDER BY post_date DESC;

-- 2. Cari foto dengan kata kunci "jas" atau "formal"
SELECT 
    ID,
    post_title,
    post_name,
    guid as url,
    post_date
FROM wp_posts 
WHERE post_type = 'attachment' 
AND post_mime_type LIKE 'image/%'
AND (
    post_title LIKE '%jas%' OR
    post_title LIKE '%formal%' OR
    post_title LIKE '%suit%' OR
    post_name LIKE '%jas%' OR
    post_name LIKE '%formal%'
)
ORDER BY post_date DESC;

-- 3. Cari semua foto yang diupload dalam 2 tahun terakhir (kemungkinan foto team terbaru)
SELECT 
    ID,
    post_title,
    post_name,
    guid as url,
    post_date
FROM wp_posts 
WHERE post_type = 'attachment' 
AND post_mime_type LIKE 'image/%'
AND post_date >= DATE_SUB(NOW(), INTERVAL 2 YEAR)
ORDER BY post_date DESC
LIMIT 100;

-- 4. Cari foto berdasarkan meta data (jika ada tag atau description)
SELECT 
    p.ID,
    p.post_title,
    p.post_name,
    p.guid as url,
    pm.meta_key,
    pm.meta_value
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'attachment' 
AND p.post_mime_type LIKE 'image/%'
AND (
    pm.meta_value LIKE '%team%' OR
    pm.meta_value LIKE '%staff%' OR
    pm.meta_value LIKE '%karyawan%' OR
    pm.meta_value LIKE '%direktur%' OR
    pm.meta_value LIKE '%manager%'
)
ORDER BY p.post_date DESC;

-- 5. Cari foto yang digunakan di halaman "About" atau "Team"
SELECT DISTINCT
    p.ID,
    p.post_title,
    p.post_name,
    p.guid as url,
    parent.post_title as used_in_page
FROM wp_posts p
LEFT JOIN wp_posts parent ON p.post_parent = parent.ID
WHERE p.post_type = 'attachment' 
AND p.post_mime_type LIKE 'image/%'
AND (
    parent.post_title LIKE '%about%' OR
    parent.post_title LIKE '%team%' OR
    parent.post_title LIKE '%tentang%' OR
    parent.post_name LIKE '%about%' OR
    parent.post_name LIKE '%team%'
)
ORDER BY p.post_date DESC;

-- 6. Cari foto di direktori uploads berdasarkan nama file
-- (Ini untuk referensi, perlu dijalankan di file system)
-- find WEB_HMTour/wp-content/uploads -type f \( -iname "*team*" -o -iname "*staff*" -o -iname "*ilham*" -o -iname "*jas*" \) -exec ls -lh {} \;

-- 7. Update: Set featured image untuk team member (jika menggunakan custom post type)
-- UPDATE wp_postmeta 
-- SET meta_value = [ID_FOTO]
-- WHERE meta_key = '_thumbnail_id' 
-- AND post_id = [ID_TEAM_MEMBER];

-- 8. Cari semua custom post type "team" atau "staff"
SELECT 
    ID,
    post_title,
    post_type,
    post_status,
    post_date
FROM wp_posts 
WHERE post_type IN ('team', 'staff', 'team_member', 'our_team')
ORDER BY post_date DESC;

-- 9. Cari foto yang digunakan dalam Elementor widgets
SELECT 
    p.ID,
    p.post_title,
    pm.meta_value
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_elementor_data'
AND pm.meta_value LIKE '%team%'
LIMIT 10;

-- 10. List semua foto yang ada di direktori uploads (via database)
SELECT 
    ID,
    post_title,
    post_name,
    guid as url,
    SUBSTRING_INDEX(guid, '/', -1) as filename,
    post_date
FROM wp_posts 
WHERE post_type = 'attachment' 
AND post_mime_type LIKE 'image/%'
AND guid LIKE '%wp-content/uploads%'
ORDER BY post_date DESC
LIMIT 200;
