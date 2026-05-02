<?php

namespace App\Http\Controllers;

trait VisaParserHelper
{
    /**
     * Parse visa document using OCR
     */
    protected function parseVisaDocument($imagePath)
    {
        try {
            $text = $this->performOcr($imagePath);
            
            $data = [
                'visa_nomor' => $this->extractVisaNumber($text),
                'visa_tipe' => $this->extractVisaType($text),
                'visa_tanggal_terbit' => $this->extractVisaIssueDate($text),
                'visa_tanggal_kadaluarsa' => $this->extractVisaExpiryDate($text),
                'visa_negara' => $this->extractVisaCountry($text),
            ];
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Visa OCR Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Extract visa number from OCR text
     */
    private function extractVisaNumber($text)
    {
        // Pattern for visa number (various formats)
        $patterns = [
            '/(?:VISA\s*(?:NO|NUMBER|#)?[\s:]*)?([A-Z0-9]{8,15})/i',
            '/(?:NO[\s.]*VISA[\s:]*)?([A-Z0-9]{8,15})/i',
            '/(?:CONTROL\s*NO[\s:]*)?([A-Z0-9]{8,15})/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return strtoupper(trim($matches[1]));
            }
        }
        
        return null;
    }
    
    /**
     * Extract visa type from OCR text
     */
    private function extractVisaType($text)
    {
        $types = [
            'TOURIST' => 'Tourist',
            'BUSINESS' => 'Business',
            'WORK' => 'Work',
            'STUDENT' => 'Student',
            'TRANSIT' => 'Transit',
            'UMRAH' => 'Umrah',
            'HAJJ' => 'Hajj',
            'PILGRIMAGE' => 'Pilgrimage',
        ];
        
        foreach ($types as $keyword => $type) {
            if (stripos($text, $keyword) !== false) {
                return $type;
            }
        }
        
        return null;
    }
    
    /**
     * Extract visa issue date from OCR text
     */
    private function extractVisaIssueDate($text)
    {
        $patterns = [
            '/(?:ISSUE\s*DATE|DATE\s*OF\s*ISSUE|ISSUED\s*ON)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i',
            '/(?:TANGGAL\s*TERBIT)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->parseDate($matches[1]);
            }
        }
        
        return null;
    }
    
    /**
     * Extract visa expiry date from OCR text
     */
    private function extractVisaExpiryDate($text)
    {
        $patterns = [
            '/(?:VALID\s*UNTIL|EXPIRY\s*DATE|EXPIRES\s*ON|VALID\s*TO)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i',
            '/(?:BERLAKU\s*SAMPAI|KADALUARSA)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->parseDate($matches[1]);
            }
        }
        
        return null;
    }
    
    /**
     * Extract visa country from OCR text
     */
    private function extractVisaCountry($text)
    {
        $countries = [
            'SAUDI ARABIA' => 'Saudi Arabia',
            'KINGDOM OF SAUDI ARABIA' => 'Saudi Arabia',
            'ARAB SAUDI' => 'Saudi Arabia',
            'UNITED ARAB EMIRATES' => 'United Arab Emirates',
            'UAE' => 'United Arab Emirates',
            'TURKEY' => 'Turkey',
            'MALAYSIA' => 'Malaysia',
            'SINGAPORE' => 'Singapore',
            'THAILAND' => 'Thailand',
        ];
        
        foreach ($countries as $keyword => $country) {
            if (stripos($text, $keyword) !== false) {
                return $country;
            }
        }
        
        return null;
    }
    
    /**
     * Parse date string to Y-m-d format
     */
    private function parseDate($dateString)
    {
        try {
            // Remove extra spaces and normalize separators
            $dateString = preg_replace('/\s+/', ' ', trim($dateString));
            $dateString = str_replace(['/', ' '], '-', $dateString);
            
            // Try different date formats
            $formats = ['d-m-Y', 'd-m-y', 'Y-m-d', 'm-d-Y', 'm-d-y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
