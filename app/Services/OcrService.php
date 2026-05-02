<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OcrService
{
    public function processKtp($imagePath)
    {
        try {
            $preprocessedPath = $this->preprocessImage($imagePath);
            $text = $this->extractText($preprocessedPath);
            $data = $this->parseKtpText($text);
            
            unlink($preprocessedPath);
            
            return [
                'success' => true,
                'data' => $data,
                'raw_text' => $text
            ];
            
        } catch (\Exception $e) {
            Log::error('OCR Error: '.$e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function preprocessImage($originalPath)
    {
        $preprocessedPath = storage_path('app/ocr_temp/preprocessed_'.Str::random(40).'.jpg');
        
        $command = "magick convert {$originalPath} ".
            "-resize 200% ".
            "-unsharp 0x1 ".
            "-contrast-stretch 1% ".
            "-type Grayscale ".
            "{$preprocessedPath}";
        
        exec($command, $output, $return);
        
        if ($return !== 0 || !file_exists($preprocessedPath)) {
            throw new \Exception("Gagal memproses gambar: ".implode("\n", $output));
        }
        
        return $preprocessedPath;
    }
    
    private function extractText($imagePath)
    {
        $command = "tesseract {$imagePath} stdout -l ind+eng --psm 6 --oem 3 2>&1";
        $text = shell_exec($command);
        
        if (empty($text)) {
            throw new \Exception("Tidak ada teks yang terdeteksi");
        }
        
        return $text;
    }
    
    private function parseKtpText($text)
    {
        // ... sama seperti method parseKtpText di controller
    }
}