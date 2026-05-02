<?php

namespace App\Http\Controllers;

trait PassportParserHelper
{
    /**
     * Parse Indonesian Passport Text (TD3 Format with MRZ)
     * 
     * Format Passport Indonesia:
     * - Visual Zone (VIZ): Informasi yang bisa dibaca manusia
     * - Machine Readable Zone (MRZ): 2 baris x 44 karakter
     * 
     * MRZ Line 1: P<IDNLASTNAME<<FIRSTNAME<<<<<<<<<<<<<<<<<<<<<
     * MRZ Line 2: PASSPORTNUMBER<NATIONALITY<BIRTHDATE<SEX<EXPIRYDATE<<<<<<<<<<<<<
     */
    private function parsePassportTextNew($text)
    {
        $data = [
            'nomor' => '',
            'nama' => '',
            'tanggal_lahir' => '',
            'tanggal_kadaluarsa' => '',
            'kewarganegaraan' => ''
        ];
        
        \Log::info('=== PARSING PASSPORT TEXT (NEW) ===');
        \Log::info('Raw text: ' . $text);
        
        // Clean text
        $cleanText = str_replace(['|', '«', '»', '€', 'œ', 'â€', '˜', "'", "'", '"', '"'], '', $text);
        $lines = preg_split('/[\r\n]+/', $cleanText);
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        
        \Log::info('Total lines: ' . count($lines));
        
        $allText = implode(' ', $lines);
        \Log::info('All text combined: ' . substr($allText, 0, 500));
        
        // ===== EXTRACT FROM MRZ (Machine Readable Zone) =====
        // MRZ is the most reliable source for passport data
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // MRZ Line 1: P<IDNLASTNAME<<FIRSTNAME<<<<<<<<<<<<<<<<<<<<<
            // Format: P<[Country Code][Name with << separators]
            // Handle incomplete MRZ: P<IDNLENGKAP<<NAMA<<<<<<<K<<<<<sssssssssss<<<
            if (preg_match('/P<([A-Z]{3})([A-Z<s]+)/i', $line, $matches)) {
                $countryCode = strtoupper($matches[1]);
                $nameField = strtoupper(str_replace('s', '<', $matches[2])); // OCR error: s -> <
                
                \Log::info('MRZ Line 1 found: ' . $line);
                
                // Extract country
                $countries = [
                    'IDN' => 'Indonesia',
                    'USA' => 'United States',
                    'GBR' => 'United Kingdom',
                    'AUS' => 'Australia',
                    'SGP' => 'Singapore',
                    'MYS' => 'Malaysia',
                    'THA' => 'Thailand',
                    'PHL' => 'Philippines'
                ];
                $data['kewarganegaraan'] = $countries[$countryCode] ?? $countryCode;
                \Log::info('✓ Kewarganegaraan found (MRZ): ' . $data['kewarganegaraan']);
                
                // Extract name (format: LASTNAME<<FIRSTNAME<<MIDDLENAME)
                // << separates last name from first name
                // < separates words within names
                $nameParts = explode('<<', $nameField);
                if (count($nameParts) >= 2) {
                    $lastName = str_replace('<', ' ', trim($nameParts[0]));
                    $firstName = str_replace('<', ' ', trim($nameParts[1]));
                    
                    // Clean up
                    $lastName = preg_replace('/[^A-Z\s]/', '', $lastName);
                    $firstName = preg_replace('/[^A-Z\s]/', '', $firstName);
                    
                    // Indonesian passport format: FIRSTNAME LASTNAME
                    if (strlen($firstName) > 1 && strlen($lastName) > 1) {
                        $data['nama'] = strtoupper(trim($firstName . ' ' . $lastName));
                        \Log::info('✓ Nama found (MRZ): ' . $data['nama']);
                    }
                }
            }
            
            // MRZ Line 2: PASSPORTNUMBER<NATIONALITY<BIRTHDATE<SEX<EXPIRYDATE<<<<<<<<<<<<<
            // Format: [Passport No][Check][Country][Birth Date][Check][Sex][Expiry Date][Check][Personal No]
            // Example: A1234567<IDN9001011<M2501011<<<<<<<<<<<<<<<06
            if (preg_match('/([A-Z0-9]{8,9})([A-Z]{3})(\d{6})(\d)([MF<])(\d{6})(\d)/i', $line, $matches)) {
                \Log::info('MRZ Line 2 found: ' . $line);
                
                // Passport number (8-9 chars including check digit)
                $passportNo = rtrim($matches[1], '<');
                if (strlen($passportNo) >= 7) {
                    $data['nomor'] = strtoupper($passportNo);
                    \Log::info('✓ Nomor passport found (MRZ): ' . $data['nomor']);
                }
                
                // Birth date (YYMMDD format)
                $birthDate = $matches[3];
                if (strlen($birthDate) == 6) {
                    $data['tanggal_lahir'] = $this->parseMrzDate($birthDate);
                    \Log::info('✓ Tanggal lahir found (MRZ): ' . $data['tanggal_lahir']);
                }
                
                // Expiry date (YYMMDD format)
                $expiryDate = $matches[6];
                if (strlen($expiryDate) == 6) {
                    $data['tanggal_kadaluarsa'] = $this->parseMrzDate($expiryDate);
                    \Log::info('✓ Tanggal kadaluarsa found (MRZ): ' . $data['tanggal_kadaluarsa']);
                }
            }
        }
        
        // ===== FALLBACK: EXTRACT FROM VISUAL ZONE (VIZ) =====
        // If MRZ not found or incomplete, try to extract from human-readable text
        
        // Passport Number from VIZ
        if (empty($data['nomor'])) {
            // Pattern 1: With keyword
            if (preg_match('/(?:NO\.?|Passport\s*No|Nomor)[:\s]*([A-Z]\d{6,8})/i', $allText, $matches)) {
                $data['nomor'] = strtoupper($matches[1]);
                \Log::info('✓ Nomor passport found (VIZ keyword): ' . $data['nomor']);
            }
            // Pattern 2: Standalone - look for A-Z followed by 6-8 digits
            elseif (preg_match('/\b([A-Z]\d{6,8})\b/', $allText, $matches)) {
                $data['nomor'] = strtoupper($matches[1]);
                \Log::info('✓ Nomor passport found (VIZ pattern): ' . $data['nomor']);
            }
            // Pattern 3: From MRZ line even if corrupted (X000000)
            elseif (preg_match('/\b([A-Z])(\d{6,7})[<s\d]/i', $allText, $matches)) {
                $data['nomor'] = strtoupper($matches[1] . $matches[2]);
                \Log::info('✓ Nomor passport found (VIZ MRZ pattern): ' . $data['nomor']);
            }
        }
        
        // Name from VIZ
        if (empty($data['nama'])) {
            // Try multiple patterns for name extraction
            
            // Pattern 1: After "NAMA LENGKAP" line, the next line is the actual name
            // Example: "NAMA LENGKAP? FULL: NAME KELAMIN / SEX" then "NAMA LENGKAP"
            foreach ($lines as $i => $line) {
                if (preg_match('/^([A-Z][A-Z\s]{5,50})$/i', trim($line), $matches)) {
                    // Check if previous line contains "NAMA LENGKAP" or "FULL NAME"
                    if ($i > 0 && preg_match('/NAMA\s*LENGKAP|FULL\s*NAME/i', $lines[$i-1])) {
                        $nama = trim($matches[1]);
                        $nama = preg_replace('/[^A-Z\s]/', '', $nama);
                        $nama = preg_replace('/\s+/', ' ', $nama);
                        if (strlen($nama) > 3 && !preg_match('/\b(KELAMIN|SEX|INDONESIA|KEWARGANEGARAAN)\b/i', $nama)) {
                            $data['nama'] = strtoupper($nama);
                            \Log::info('✓ Nama found (VIZ line after keyword): ' . $data['nama']);
                            break;
                        }
                    }
                }
            }
            
            // Pattern 2: Name between "NAMA LENGKAP" and "KELAMIN/SEX"
            if (empty($data['nama'])) {
                if (preg_match('/(?:NAMA\s*LENGKAP|FULL\s*NAME)[:\s\/\?]*([A-Z][A-Z\s]{3,50})(?:KELAMIN|SEX|K\/S)/i', $allText, $matches)) {
                    $nama = trim($matches[1]);
                    $nama = preg_replace('/[^A-Z\s]/', '', $nama);
                    $nama = preg_replace('/\s+/', ' ', $nama);
                    // Remove common words
                    $nama = preg_replace('/\b(FULL|NAME|NAMA|LENGKAP)\b/i', '', $nama);
                    $nama = trim($nama);
                    if (strlen($nama) > 3) {
                        $data['nama'] = strtoupper($nama);
                        \Log::info('✓ Nama found (VIZ between keywords): ' . $data['nama']);
                    }
                }
            }
            
            // Pattern 3: Standalone name line (2-4 words, all caps)
            if (empty($data['nama'])) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    // Match 2-4 word names in all caps
                    if (preg_match('/^([A-Z]{2,}(?:\s+[A-Z]{2,}){1,3})$/', $line, $matches)) {
                        $nama = $matches[1];
                        // Exclude common passport words
                        if (!preg_match('/\b(PASPOR|PASSPORT|INDONESIA|KEWARGANEGARAAN|NATIONALITY|KELAMIN|SEX|TEMPAT|LAHIR|PLACE|BIRTH|PENGELUARAN|ISSUE|BERLAKU|EXPIRY|IMIGRASI|IMMIGRATION|KANTOR|OFFICE)\b/i', $nama)) {
                            $data['nama'] = strtoupper($nama);
                            \Log::info('✓ Nama found (VIZ standalone): ' . $data['nama']);
                            break;
                        }
                    }
                }
            }
        }
        
        // Date of Birth from VIZ
        if (empty($data['tanggal_lahir'])) {
            // Pattern 1: With keyword
            if (preg_match('/(?:DATE\s*OF\s*BIRTH|TANGGAL\s*LAHIR|TGL.*AHIR)[:\s]*(\d{1,2}\s+[A-Z]{3}\s+\d{4})/i', $allText, $matches)) {
                $dateStr = trim($matches[1]);
                $data['tanggal_lahir'] = $this->parsePassportDate($dateStr);
                if (!empty($data['tanggal_lahir'])) {
                    \Log::info('✓ Tanggal lahir found (VIZ keyword): ' . $data['tanggal_lahir']);
                }
            }
            // Pattern 2: Find all dates and pick the oldest (likely birth date)
            if (empty($data['tanggal_lahir'])) {
                if (preg_match_all('/\b(\d{1,2}\s+(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+\d{4})\b/i', $allText, $matches)) {
                    $dates = [];
                    foreach ($matches[1] as $dateStr) {
                        $parsed = $this->parsePassportDate($dateStr);
                        if (!empty($parsed)) {
                            $year = (int)substr($parsed, 0, 4);
                            // Birth date should be between 1920-2010
                            if ($year >= 1920 && $year <= 2010) {
                                $dates[$parsed] = $dateStr;
                            }
                        }
                    }
                    if (!empty($dates)) {
                        ksort($dates); // Sort by date
                        $oldestDate = key($dates);
                        $data['tanggal_lahir'] = $oldestDate;
                        \Log::info('✓ Tanggal lahir found (VIZ oldest date): ' . $data['tanggal_lahir'] . ' from ' . $dates[$oldestDate]);
                    }
                }
            }
        }
        
        // Date of Expiry from VIZ
        if (empty($data['tanggal_kadaluarsa'])) {
            // Pattern 1: With keyword
            if (preg_match('/(?:DATE\s*OF\s*EXPIRY|EXPIRY|BERLAKU|TGL.*HABIS)[:\s]*(\d{1,2}\s+[A-Z]{3}\s+\d{4})/i', $allText, $matches)) {
                $dateStr = trim($matches[1]);
                $data['tanggal_kadaluarsa'] = $this->parsePassportDate($dateStr);
                if (!empty($data['tanggal_kadaluarsa'])) {
                    \Log::info('✓ Tanggal kadaluarsa found (VIZ keyword): ' . $data['tanggal_kadaluarsa']);
                }
            }
            // Pattern 2: Find all dates and pick the latest (likely expiry)
            if (empty($data['tanggal_kadaluarsa'])) {
                if (preg_match_all('/\b(\d{1,2}\s+(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+\d{4})\b/i', $allText, $matches)) {
                    $dates = [];
                    foreach ($matches[1] as $dateStr) {
                        $parsed = $this->parsePassportDate($dateStr);
                        if (!empty($parsed)) {
                            $year = (int)substr($parsed, 0, 4);
                            // Expiry date should be between 2000-2050
                            if ($year >= 2000 && $year <= 2050) {
                                $dates[$parsed] = $dateStr;
                            }
                        }
                    }
                    if (!empty($dates)) {
                        krsort($dates); // Sort by date descending
                        $latestDate = key($dates);
                        $data['tanggal_kadaluarsa'] = $latestDate;
                        \Log::info('✓ Tanggal kadaluarsa found (VIZ latest date): ' . $data['tanggal_kadaluarsa'] . ' from ' . $dates[$latestDate]);
                    }
                }
            }
        }
        
        // Nationality from VIZ
        if (empty($data['kewarganegaraan'])) {
            // Format: "KEWARGANEGARAAN / NATIONALITY: INDONESIA"
            if (preg_match('/(?:NATIONALITY|KEWARGANEGARAAN)[:\s\/\.]*([A-Z]{3,20})/i', $allText, $matches)) {
                $nationality = trim($matches[1]);
                $nationality = preg_replace('/[^A-Z]/', '', $nationality);
                if (strlen($nationality) >= 3) {
                    // Normalize
                    if ($nationality == 'INDONESIAN' || $nationality == 'INDONESIA') {
                        $nationality = 'Indonesia';
                    }
                    $data['kewarganegaraan'] = ucfirst(strtolower($nationality));
                    \Log::info('✓ Kewarganegaraan found (VIZ): ' . $data['kewarganegaraan']);
                }
            }
        }
        
        \Log::info('=== FINAL RESULT ===');
        \Log::info(json_encode($data, JSON_PRETTY_PRINT));
        
        return $data;
    }
    
    /**
     * Parse MRZ date format (YYMMDD) to Y-m-d
     */
    private function parseMrzDate($mrzDate)
    {
        if (strlen($mrzDate) != 6) {
            return '';
        }
        
        $yy = substr($mrzDate, 0, 2);
        $mm = substr($mrzDate, 2, 2);
        $dd = substr($mrzDate, 4, 2);
        
        // Determine century (assume 1900-2099)
        // If YY > current year's last 2 digits, it's 1900s, else 2000s
        $currentYY = (int)date('y');
        if ((int)$yy > $currentYY) {
            $yyyy = '19' . $yy;
        } else {
            $yyyy = '20' . $yy;
        }
        
        // Validate date
        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return '';
        }
        
        return sprintf('%s-%s-%s', $yyyy, $mm, $dd);
    }
    
    /**
     * Parse passport date from various formats
     */
    private function parsePassportDate($dateStr)
    {
        $dateStr = trim($dateStr);
        
        // Format 1: DD MMM YYYY (e.g., "01 JAN 1990")
        $months = [
            'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
            'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
            'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
        ];
        
        if (preg_match('/(\d{1,2})\s*([A-Z]{3})\s*(\d{4})/i', $dateStr, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $monthAbbr = strtoupper($matches[2]);
            $year = $matches[3];
            
            if (isset($months[$monthAbbr])) {
                return sprintf('%s-%s-%s', $year, $months[$monthAbbr], $day);
            }
        }
        
        // Format 2: DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $dateStr, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            
            if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
                return sprintf('%s-%s-%s', $year, $month, $day);
            }
        }
        
        return '';
    }
}
