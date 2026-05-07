<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    /**
     * Optimize and compress image using GD library
     * 
     * @param string $path Path to image in storage
     * @param array $options Optimization options
     * @return string|false Optimized image path or false on failure
     */
    public function optimizeImage($path, $options = [])
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            
            if (!file_exists($fullPath)) {
                Log::warning('Image not found for optimization: ' . $path);
                return false;
            }
            
            // Default options
            $maxWidth = $options['max_width'] ?? 1200;
            $maxHeight = $options['max_height'] ?? 800;
            $quality = $options['quality'] ?? 75;
            
            // Get original file size
            $originalSize = filesize($fullPath);
            
            // Get image info
            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                Log::warning('Invalid image file: ' . $path);
                return false;
            }
            
            list($originalWidth, $originalHeight, $imageType) = $imageInfo;
            
            // Create image resource based on type
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($fullPath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($fullPath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($fullPath);
                    break;
                default:
                    Log::warning('Unsupported image type: ' . $imageType);
                    return false;
            }
            
            if (!$source) {
                Log::warning('Failed to create image resource: ' . $path);
                return false;
            }
            
            // Calculate new dimensions
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
            
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                $newWidth = round($originalWidth * $ratio);
                $newHeight = round($originalHeight * $ratio);
            }
            
            // Create new image
            $destination = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($imageType == IMAGETYPE_PNG) {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
                $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            // Resize image
            imagecopyresampled(
                $destination, $source,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );
            
            // Save optimized image
            if ($imageType == IMAGETYPE_PNG) {
                // Convert PNG to JPEG for better compression
                $jpegPath = preg_replace('/\.png$/i', '.jpg', $fullPath);
                imagejpeg($destination, $jpegPath, $quality);
                
                // Update path if converted
                if ($jpegPath !== $fullPath) {
                    unlink($fullPath); // Delete original PNG
                    $path = preg_replace('/\.png$/i', '.jpg', $path);
                }
            } else {
                imagejpeg($destination, $fullPath, $quality);
            }
            
            // Free memory
            imagedestroy($source);
            imagedestroy($destination);
            
            // Get new file size
            $newSize = filesize($jpegPath ?? $fullPath);
            $reduction = round((($originalSize - $newSize) / $originalSize) * 100, 1);
            
            Log::info('Image optimized', [
                'path' => $path,
                'original_size' => $this->formatBytes($originalSize),
                'new_size' => $this->formatBytes($newSize),
                'reduction' => $reduction . '%',
                'dimensions' => "{$originalWidth}x{$originalHeight} → {$newWidth}x{$newHeight}"
            ]);
            
            return $path;
            
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage(), [
                'path' => $path,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Optimize all package images
     * 
     * @return array Results
     */
    public function optimizeAllPackageImages()
    {
        $packages = \App\Models\TravelPackage::whereNotNull('image_path')->get();
        
        $results = [
            'total' => $packages->count(),
            'optimized' => 0,
            'failed' => 0,
            'skipped' => 0
        ];
        
        foreach ($packages as $package) {
            if (!$package->image_path) {
                $results['skipped']++;
                continue;
            }
            
            // Check if needs optimization
            if (!$this->needsOptimization($package->image_path)) {
                $results['skipped']++;
                continue;
            }
            
            $result = $this->optimizeImage($package->image_path, [
                'max_width' => 1200,
                'max_height' => 800,
                'quality' => 75
            ]);
            
            if ($result) {
                // Update package if path changed (PNG to JPG)
                if ($result !== $package->image_path) {
                    $package->image_path = $result;
                    $package->save();
                }
                $results['optimized']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
    
    /**
     * Format bytes to human readable
     * 
     * @param int $bytes
     * @return string
     */
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Check if image needs optimization
     * 
     * @param string $path
     * @return bool
     */
    public function needsOptimization($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return false;
        }
        
        $fileSize = filesize($fullPath);
        $imageInfo = @getimagesize($fullPath);
        
        if (!$imageInfo) {
            return false;
        }
        
        list($width, $height) = $imageInfo;
        
        // Needs optimization if:
        // - File size > 500KB
        // - Width > 1200px
        // - Height > 800px
        return $fileSize > 512000 || $width > 1200 || $height > 800;
    }
}
