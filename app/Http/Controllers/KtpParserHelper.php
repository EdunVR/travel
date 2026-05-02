<?php

namespace App\Http\Controllers;

trait KtpParserHelper
{
    private function parseKtpTextNew($text)
    {
        $data = [
            'nik' => '',
            'nama' => '',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'alamat' => ''
        ];
        
        \Log::info('=== PARSING KTP TEXT (NEW) ===');
        \Log::info('Raw text: ' . $text);
        
        // Clean text
        $cleanText = str_replace(['———', '——', '—', '»', '...', '..', '–', '"', '"', '«', '»', '–', '€', 'œ'], ' ', $text);
        $allText = preg_replace('/\s+/', ' ', $cleanText);
        
        \Log::info('Cleaned text: ' . $allText);
        
        // ===== NIK EXTRACTION =====
        // Pattern: 520L02)4109L0001 -> 5201024109010001
        if (preg_match('/([0-9lLOo\)\(]{16,})/', $allText, $matches)) {
            $nik = $matches[1];
            // Clean OCR errors: L->1, O->0, o->0, )->1, (->1
            $nik = str_replace(['L', 'l', 'O', 'o', ')', '(', ' '], ['1', '1', '0', '0', '1', '1', ''], $nik);
            $nik = preg_replace('/[^0-9]/', '', $nik);
            $nik = substr($nik, 0, 16);
            
            if (strlen($nik) == 16) {
                $data['nik'] = $nik;
                \Log::info('✓ NIK found: ' . $nik . ' (from: ' . $matches[1] . ')');
            }
        }
        
        // ===== NAMA EXTRACTION =====
        // Skip province/city names, find actual person name
        $skipWords = ['PROVINSI', 'KABUPATEN', 'KOTA', 'NUSA', 'TENGGARA', 'BARAT', 'TIMUR', 'BARA',
                      'JAWA', 'SUMATRA', 'KALIMANTAN', 'SULAWESI', 'PAPUA', 'MALUKU', 'BALI',
                      'INDONESIA', 'REPUBLIK', 'KARYAWAN', 'SWASTA', 'HIDUP', 'BERLAKU', 'SEUMUR',
                      'MENGURUS', 'RUMAH', 'TANGGA', 'PEREMPUAN', 'LAKI'];
        
        // Pattern 1: Look for name after "Nama" keyword and before "Ter" or "Tempat" or date
        // Handle format: "Nama 3 - META LATIFAH: RHADIATUL JANNAH Ter"
        if (preg_match('/(?:Nama|NAMA)[:\s\d\-]*([A-Z][A-Z\s:]{5,100}?)(?:Ter|Tempat|TTL|ahir|Lahir|\d{1,2}[-\/])/i', $allText, $matches)) {
            $nama = trim($matches[1]);
            // Remove colons and clean up
            $nama = str_replace(':', ' ', $nama);
            $nama = preg_replace('/[^A-Z\s]/i', '', $nama);
            $nama = preg_replace('/\s+/', ' ', $nama);
            
            // Filter out skip words
            $namaWords = explode(' ', $nama);
            $validWords = [];
            foreach ($namaWords as $word) {
                $isSkip = false;
                foreach ($skipWords as $skipWord) {
                    if (stripos($word, $skipWord) !== false || strlen($word) < 3) {
                        $isSkip = true;
                        break;
                    }
                }
                if (!$isSkip) {
                    $validWords[] = $word;
                }
            }
            
            // Name can be 2-6 words (allow longer names)
            if (count($validWords) >= 2 && count($validWords) <= 6) {
                $data['nama'] = strtoupper(implode(' ', $validWords));
                \Log::info('✓ Nama found (keyword): ' . $data['nama']);
            }
        }
        
        // Pattern 2: Find all capital word sequences if not found
        if (empty($data['nama'])) {
            preg_match_all('/\b([A-Z]{3,}(?:\s+[A-Z]{3,}){1,5})\b/', $allText, $allMatches);
            
            foreach ($allMatches[1] as $sequence) {
                $words = explode(' ', $sequence);
                $validWords = [];
                
                foreach ($words as $word) {
                    $isSkip = false;
                    foreach ($skipWords as $skipWord) {
                        if (stripos($word, $skipWord) !== false || strlen($word) < 3) {
                            $isSkip = true;
                            break;
                        }
                    }
                    if (!$isSkip) {
                        $validWords[] = $word;
                    }
                }
                
                // Name should be 2-6 words
                if (count($validWords) >= 2 && count($validWords) <= 6) {
                    $data['nama'] = strtoupper(implode(' ', $validWords));
                    \Log::info('✓ Nama found (sequence): ' . $data['nama']);
                    break;
                }
            }
        }
        
        // ===== TANGGAL LAHIR EXTRACTION =====
        // Pattern: 14 10-1901 or 14-10-1991 or 20- -05-1997
        if (preg_match('/\b(\d{1,2})[\s\-\/]+(\d{1,2})[\s\-\/]+(19\d{2}|20\d{2})\b/', $allText, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            
            // Validate
            if ($day <= 31 && $month <= 12 && $year >= 1940 && $year <= date('Y')) {
                $data['tanggal_lahir'] = sprintf('%s-%s-%s', $year, $month, $day);
                \Log::info('✓ Tanggal lahir found: ' . $data['tanggal_lahir']);
                
                // ===== TEMPAT LAHIR EXTRACTION =====
                // Look for word before the date
                $datePattern = preg_quote($matches[0], '/');
                if (preg_match('/([A-Z][A-Za-z]{2,20})[:\s,]+' . $datePattern . '/i', $allText, $placeMatch)) {
                    $tempat = trim($placeMatch[1]);
                    // Remove common prefixes
                    $tempat = preg_replace('/^(Lahir|Tempat|TTL|amat|ma|er|ahir)\s*/i', '', $tempat);
                    $tempat = preg_replace('/[^A-Za-z\s]/', '', $tempat);
                    
                    // Check if not a skip word
                    $isSkip = false;
                    foreach ($skipWords as $skipWord) {
                        if (stripos($tempat, $skipWord) !== false) {
                            $isSkip = true;
                            break;
                        }
                    }
                    
                    if (!$isSkip && strlen($tempat) >= 3) {
                        $data['tempat_lahir'] = ucwords(strtolower(trim($tempat)));
                        \Log::info('✓ Tempat lahir found: ' . $data['tempat_lahir']);
                    }
                }
            }
        }
        
        // ===== ALAMAT EXTRACTION =====
        // Pattern 1: After "Alamat" keyword until RT/RW or Kel/Desa
        if (preg_match('/(?:Alamat|ALAMAT)[:\s\-]*([A-Za-z0-9\s]{5,}?)(?:RT[\/\s]*R?W?|Kel[\/\s]*Desa|Kecamatan|Agama)/i', $allText, $matches)) {
            $alamat = trim($matches[1]);
            $alamat = preg_replace('/\s+/', ' ', $alamat);
            // Clean up OCR artifacts
            $alamat = str_replace(['|', '"', '"', '…', '–', '€', 'œ', 'HK:', 'â€', '&:'], '', $alamat);
            $alamat = trim($alamat);
            
            if (strlen($alamat) > 5) {
                $data['alamat'] = $alamat;
                \Log::info('✓ Alamat found (keyword): ' . $data['alamat']);
            }
        }
        
        // Pattern 2: Look for RT/RW pattern with various formats
        // RT/RW: 003/006 or RT/R HK: BLOK 9037006 or RTRW 003006
        if (empty($data['alamat'])) {
            // Try format: RT/R HK: BLOK 9037006 (6 digits combined)
            if (preg_match('/([A-Za-z\s]{8,}?)\s*RT[\/\s]*R?W?\s*(?:HK|BLOK)?[:\s]*(\d{6,7})/i', $allText, $matches)) {
                $alamat = trim($matches[1]);
                $rtRw = $matches[2];
                
                // Split 6-7 digits into RT and RW (first 3 and last 3)
                if (strlen($rtRw) >= 6) {
                    $rt = substr($rtRw, 0, 3);
                    $rw = substr($rtRw, -3);
                    
                    // Clean up
                    $alamat = preg_replace('/(Gol|Darah|kelamin|jenis|Jenis)\s*/i', '', $alamat);
                    $alamat = preg_replace('/\s+/', ' ', $alamat);
                    $alamat = str_replace(['|', '"', '"', '€', 'œ', '&:'], '', $alamat);
                    $alamat = trim($alamat);
                    
                    if (strlen($alamat) > 5) {
                        $data['alamat'] = $alamat . ' RT/RW ' . $rt . '/' . $rw;
                        \Log::info('✓ Alamat found (RT/RW 6-digit): ' . $data['alamat']);
                    }
                }
            }
            // Try format: RT/RW 003/006 (separated)
            elseif (preg_match('/([A-Za-z\s]{8,}?)\s*(?:RT[\/\s]*RW|RTRW)[:\s]*(?:HK|BLOK)?[:\s]*(\d{3})[\/\s]*(\d{3})/i', $allText, $matches)) {
                $alamat = trim($matches[1]);
                $rt = $matches[2];
                $rw = $matches[3];
                
                // Clean up
                $alamat = preg_replace('/(Gol|Darah|kelamin|jenis|Jenis)\s*/i', '', $alamat);
                $alamat = preg_replace('/\s+/', ' ', $alamat);
                $alamat = str_replace(['|', '"', '"', '€', 'œ', '&:'], '', $alamat);
                $alamat = trim($alamat);
                
                if (strlen($alamat) > 5) {
                    $data['alamat'] = $alamat . ' RT/RW ' . $rt . '/' . $rw;
                    \Log::info('✓ Alamat found (RT/RW separated): ' . $data['alamat']);
                }
            }
        }
        
        // Pattern 3: Look for street/area names before Kel/Desa
        if (empty($data['alamat'])) {
            if (preg_match('/(?:Alamat|kelamin|Darah)[:\s\-]*([A-Za-z\s]{8,}?)(?:Kel[\/\s]*Desa|Kecamatan)/i', $allText, $matches)) {
                $alamat = trim($matches[1]);
                $alamat = preg_replace('/(Gol|Darah|kelamin|jenis|Jenis)\s*/i', '', $alamat);
                $alamat = preg_replace('/\s+/', ' ', $alamat);
                $alamat = str_replace(['|', '"', '"', '€', 'œ', 'HK:', 'â€', '&:'], '', $alamat);
                $alamat = trim($alamat);
                
                if (strlen($alamat) > 5) {
                    $data['alamat'] = $alamat;
                    \Log::info('✓ Alamat found (before Kel/Desa): ' . $data['alamat']);
                }
            }
        }
        
        \Log::info('=== FINAL RESULT ===');
        \Log::info(json_encode($data, JSON_PRETTY_PRINT));
        
        return $data;
    }
}
