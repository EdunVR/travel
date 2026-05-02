<?php

// Create a simple test logo for testing
$logoPath = 'storage/app/public/logos/test-logo.png';

// Create a simple 60x60 PNG image
$image = imagecreate(60, 60);
$backgroundColor = imagecolorallocate($image, 240, 240, 240); // Light gray
$textColor = imagecolorallocate($image, 0, 0, 0); // Black

// Fill background
imagefill($image, 0, 0, $backgroundColor);

// Add text
imagestring($image, 3, 15, 25, 'LOGO', $textColor);

// Save image
if (imagepng($image, $logoPath)) {
    echo "✓ Test logo created: $logoPath\n";
    echo "✓ File size: " . filesize($logoPath) . " bytes\n";
    echo "✓ URL should be: http://localhost/storage/logos/test-logo.png\n";
} else {
    echo "✗ Failed to create test logo\n";
}

// Clean up
imagedestroy($image);

// Also create a company settings record if needed
echo "\n=== DATABASE SETUP ===\n";
echo "Run this SQL to create test company settings:\n\n";
echo "INSERT INTO company_settings (outlet_id, company_name, company_logo, is_active, created_at, updated_at) \n";
echo "VALUES (1, 'Test Company', 'logos/test-logo.png', 1, NOW(), NOW()) \n";
echo "ON DUPLICATE KEY UPDATE company_logo = 'logos/test-logo.png', updated_at = NOW();\n\n";

echo "Or update existing record:\n";
echo "UPDATE company_settings SET company_logo = 'logos/test-logo.png' WHERE outlet_id = 1;\n\n";

echo "Test logo creation completed!\n";