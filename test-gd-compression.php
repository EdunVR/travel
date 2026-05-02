<?php
/**
 * Test script untuk GD image compression
 * Simulasi upload foto dan bukti pembayaran
 */

// Cek GD extension
if (!extension_loaded('gd')) {
    die("ERROR: GD extension tidak terinstall!\n");
}

echo "✓ GD Extension: INSTALLED\n";
echo "GD Version: " . gd_info()['GD Version'] . "\n\n";

// Cek supported formats
$gdInfo = gd_info();
echo "Supported Formats:\n";
echo "- JPEG: " . ($gdInfo['JPEG Support'] ? 'YES' : 'NO') . "\n";
echo "- PNG: " . ($gdInfo['PNG Support'] ? 'YES' : 'NO') . "\n";
echo "- GIF: " . ($gdInfo['GIF Read Support'] ? 'YES' : 'NO') . "\n\n";

// Test function (sama seperti di AffiliateController)
function testCompressImage($testImagePath, $folder) {
    echo "Testing compression for: $testImagePath\n";
    
    if (!file_exists($testImagePath)) {
        echo "  ✗ File tidak ditemukan\n\n";
        return false;
    }
    
    $fileSize = filesize($testImagePath);
    echo "  Original size: " . number_format($fileSize / 1024, 2) . " KB\n";
    
    // Get mime type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $testImagePath);
    finfo_close($finfo);
    echo "  MIME type: $mimeType\n";
    
    // Create image resource
    if (strpos($mimeType, 'jpeg') !== false || strpos($mimeType, 'jpg') !== false) {
        $image = @imagecreatefromjpeg($testImagePath);
    } elseif (strpos($mimeType, 'png') !== false) {
        $image = @imagecreatefrompng($testImagePath);
    } elseif (strpos($mimeType, 'gif') !== false) {
        $image = @imagecreatefromgif($testImagePath);
    } else {
        echo "  ✗ Format tidak didukung\n\n";
        return false;
    }
    
    if (!$image) {
        echo "  ✗ Gagal membuat image resource\n\n";
        return false;
    }
    
    // Get dimensions
    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);
    echo "  Original dimensions: {$originalWidth}x{$originalHeight}\n";
    
    // Calculate new dimensions
    if ($originalWidth > 1200) {
        $newWidth = 1200;
        $newHeight = (int) ($originalHeight * ($newWidth / $originalWidth));
        echo "  Resizing to: {$newWidth}x{$newHeight}\n";
    } else {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
        echo "  No resize needed (width <= 1200)\n";
    }
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG
    if (strpos($mimeType, 'png') !== false) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Resize
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Save to temp location
    $filename = 'test_' . time() . '_' . uniqid() . '.jpg';
    $fullPath = __DIR__ . '/storage/app/public/' . $folder;
    
    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "  Created directory: $fullPath\n";
    }
    
    $filePath = $fullPath . '/' . $filename;
    
    // Save with compression
    $result = imagejpeg($newImage, $filePath, 80);
    
    if ($result) {
        $newSize = filesize($filePath);
        $compression = 100 - (($newSize / $fileSize) * 100);
        echo "  ✓ Compressed size: " . number_format($newSize / 1024, 2) . " KB\n";
        echo "  ✓ Compression: " . number_format($compression, 2) . "%\n";
        echo "  ✓ Saved to: $filePath\n";
    } else {
        echo "  ✗ Gagal menyimpan file\n";
    }
    
    // Free memory
    imagedestroy($image);
    imagedestroy($newImage);
    
    echo "\n";
    return $result;
}

// Test dengan gambar yang ada (jika ada)
echo "=== TESTING IMAGE COMPRESSION ===\n\n";

// Cari gambar test di public/images
$testImages = [
    'public/images/hm-tour-logo.png',
    'public/images/logo.png',
    'storage/app/public/affiliator-photos/test.jpg',
];

$foundImage = false;
foreach ($testImages as $testImage) {
    if (file_exists($testImage)) {
        $foundImage = true;
        testCompressImage($testImage, 'test-compression');
        break;
    }
}

if (!$foundImage) {
    echo "Tidak ada gambar test yang ditemukan.\n";
    echo "Silakan upload gambar melalui form pendaftaran untuk test kompresi.\n\n";
}

// Test create directory
echo "=== TESTING DIRECTORY CREATION ===\n\n";
$testDirs = [
    'storage/app/public/affiliator-photos',
    'storage/app/public/payment-proofs',
];

foreach ($testDirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Created: $dir\n";
        } else {
            echo "✗ Failed to create: $dir\n";
        }
    } else {
        echo "✓ Already exists: $dir\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
echo "GD library siap digunakan untuk kompresi gambar.\n";
echo "Silakan test upload foto melalui form pendaftaran affiliator.\n";
