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
            'kewarganegaraan' => '',
            'title' => '',
            'gender' => '',
            'tanggal_terbit' => '',
            'kantor_terbit' => '',
            'tempat_lahir' => '',
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
            
            // MRZ Line 2: TD3 format (44 chars)
            // Format: [PassportNo 9][Check 1][Country 3][BirthDate 6][Check 1][Sex 1][ExpiryDate 6][Check 1][PersonalNo+Filler]
            // Example: E5444606<1IDN9005271M3310230320505270500654
            // PassportNo can contain < as filler (e.g., E5444606<)
            if (preg_match('/([A-Z0-9<]{9})(\d)([A-Z]{3})(\d{6})(\d)([MF])(\d{6})(\d)/i', $line, $matches)) {
                \Log::info('MRZ Line 2 found (TD3): ' . $line);
                
                // Passport number (9 chars, strip < fillers)
                $passportNo = rtrim($matches[1], '<');
                if (strlen($passportNo) >= 7) {
                    $data['nomor'] = strtoupper($passportNo);
                    \Log::info('✓ Nomor passport found (MRZ): ' . $data['nomor']);
                }
                
                // Birth date (YYMMDD format)
                $birthDate = $matches[4];
                if (strlen($birthDate) == 6) {
                    $data['tanggal_lahir'] = $this->parseMrzDate($birthDate);
                    \Log::info('✓ Tanggal lahir found (MRZ): ' . $data['tanggal_lahir']);
                }
                
                // Expiry date (YYMMDD format)
                $expiryDate = $matches[7];
                if (strlen($expiryDate) == 6) {
                    $data['tanggal_kadaluarsa'] = $this->parseMrzDate($expiryDate);
                    \Log::info('✓ Tanggal kadaluarsa found (MRZ): ' . $data['tanggal_kadaluarsa']);
                }
                
                // Gender from MRZ (M or F)
                $sex = strtoupper($matches[6]);
                if ($sex === 'M') {
                    $data['gender'] = 'Male';
                    $data['title'] = 'MR';
                } elseif ($sex === 'F') {
                    $data['gender'] = 'Female';
                    $data['title'] = 'MRS';
                }
                \Log::info('✓ Gender found (MRZ): ' . $data['gender']);
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
            // Pattern 1: Specific keyword — "TGL LAHIR" or "DATE OF BIRTH" (NOT "TGL HABIS")
            if (preg_match('/(?:TGL\.?\s*LAHIR|DATE\s*OF\s*BIRTH|TANGGAL\s*LAHIR)[:\s\/]*(\d{1,2}\s+[A-Z]{3}\s+\d{4})/i', $allText, $matches)) {
                $dateStr = trim($matches[1]);
                $data['tanggal_lahir'] = $this->parsePassportDate($dateStr);
                if (!empty($data['tanggal_lahir'])) {
                    \Log::info('✓ Tanggal lahir found (VIZ keyword): ' . $data['tanggal_lahir']);
                }
            }
            // Pattern 2: Find all dates and pick the oldest (likely birth date — year < 2010)
            if (empty($data['tanggal_lahir'])) {
                if (preg_match_all('/\b(\d{1,2}\s+(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+\d{4})\b/i', $allText, $matches)) {
                    $dates = [];
                    foreach ($matches[1] as $dateStr) {
                        $parsed = $this->parsePassportDate($dateStr);
                        if (!empty($parsed)) {
                            $year = (int)substr($parsed, 0, 4);
                            // Birth date should be between 1920-2015
                            if ($year >= 1920 && $year <= 2015) {
                                $dates[$parsed] = $dateStr;
                            }
                        }
                    }
                    if (!empty($dates)) {
                        ksort($dates); // Sort by date — oldest first
                        $oldestDate = key($dates);
                        $data['tanggal_lahir'] = $oldestDate;
                        \Log::info('✓ Tanggal lahir found (VIZ oldest date): ' . $data['tanggal_lahir'] . ' from ' . $dates[$oldestDate]);
                    }
                }
            }
        }
        
        // Date of Expiry from VIZ
        if (empty($data['tanggal_kadaluarsa'])) {
            // Pattern 1: Specific keyword — "TGL HABIS BERLAKU" or "DATE OF EXPIRY"
            if (preg_match('/(?:TGL\.?\s*HABIS\s*BERLAKU|DATE\s*OF\s*EXPIRY|TANGGAL\s*KADALUARSA|BERLAKU\s*SAMPAI)[:\s\/]*(\d{1,2}\s+[A-Z]{3}\s+\d{4})/i', $allText, $matches)) {
                $dateStr = trim($matches[1]);
                $data['tanggal_kadaluarsa'] = $this->parsePassportDate($dateStr);
                if (!empty($data['tanggal_kadaluarsa'])) {
                    \Log::info('✓ Tanggal kadaluarsa found (VIZ keyword): ' . $data['tanggal_kadaluarsa']);
                }
            }
            // Pattern 2: Find all dates and pick the latest (year > 2025 likely expiry)
            if (empty($data['tanggal_kadaluarsa'])) {
                if (preg_match_all('/\b(\d{1,2}\s+(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+\d{4})\b/i', $allText, $matches)) {
                    $dates = [];
                    foreach ($matches[1] as $dateStr) {
                        $parsed = $this->parsePassportDate($dateStr);
                        if (!empty($parsed)) {
                            $year = (int)substr($parsed, 0, 4);
                            // Expiry date should be in the future or recent (2024-2050)
                            if ($year >= 2024 && $year <= 2050) {
                                $dates[$parsed] = $dateStr;
                            }
                        }
                    }
                    if (!empty($dates)) {
                        krsort($dates); // Sort by date descending — latest first
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
        
        // ===== EXTRACT ADDITIONAL FIELDS =====
        
        // Gender from VIZ (if not found from MRZ)
        if (empty($data['gender'])) {
            // Indonesian passport format: "KELAMIN / SEX" followed by "L/M" or "P/F"
            if (preg_match('/(?:KELAMIN|SEX)[:\s\/]*([LPMF])[\/\s]*([MF])?/i', $allText, $matches)) {
                $sex = strtoupper(trim($matches[1]));
                if (in_array($sex, ['L', 'M'])) {
                    $data['gender'] = 'Male';
                    if (empty($data['title'])) $data['title'] = 'MR';
                } elseif (in_array($sex, ['P', 'F'])) {
                    $data['gender'] = 'Female';
                    if (empty($data['title'])) $data['title'] = 'MRS';
                }
                \Log::info('✓ Gender found (VIZ): ' . $data['gender']);
            }
            // Fallback: look for standalone MALE/FEMALE/LAKI/PEREMPUAN
            if (empty($data['gender'])) {
                if (preg_match('/\b(MALE|FEMALE|LAKI|PEREMPUAN|PRIA|WANITA)\b/i', $allText, $matches)) {
                    $sex = strtoupper(trim($matches[1]));
                    if (in_array($sex, ['MALE', 'LAKI', 'PRIA'])) {
                        $data['gender'] = 'Male';
                        if (empty($data['title'])) $data['title'] = 'MR';
                    } else {
                        $data['gender'] = 'Female';
                        if (empty($data['title'])) $data['title'] = 'MRS';
                    }
                    \Log::info('✓ Gender found (VIZ fallback): ' . $data['gender']);
                }
            }
        }
        
        // Issued Date from VIZ — "TGL PENGELUARAN / DATE OF ISSUE"
        if (empty($data['tanggal_terbit'])) {
            // Pattern 1: Specific Indonesian passport keywords
            if (preg_match('/(?:TGL\.?\s*PENGELUARAN|DATE\s*OF\s*ISSUE|TANGGAL\s*PENGELUARAN|TANGGAL\s*TERBIT|ISSUED)[:\s\/]*(\d{1,2}\s+[A-Z]{3}\s+\d{4})/i', $allText, $matches)) {
                $data['tanggal_terbit'] = $this->parsePassportDate(trim($matches[1]));
                \Log::info('✓ Tanggal terbit found (VIZ keyword): ' . $data['tanggal_terbit']);
            }
            // Pattern 2: If we have 3 dates, the middle one is issued date
            if (empty($data['tanggal_terbit'])) {
                if (preg_match_all('/\b(\d{1,2}\s+(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+\d{4})\b/i', $allText, $matches)) {
                    $allDates = [];
                    foreach ($matches[1] as $dateStr) {
                        $parsed = $this->parsePassportDate($dateStr);
                        if (!empty($parsed)) {
                            $allDates[] = $parsed;
                        }
                    }
                    // Remove duplicates and sort
                    $allDates = array_unique($allDates);
                    sort($allDates);
                    // If we have 3 dates: birth (oldest), issued (middle), expiry (newest)
                    if (count($allDates) >= 3) {
                        // The middle date(s) that are not birth or expiry = issued date
                        foreach ($allDates as $d) {
                            if ($d !== $data['tanggal_lahir'] && $d !== $data['tanggal_kadaluarsa']) {
                                $year = (int)substr($d, 0, 4);
                                if ($year >= 2000 && $year <= 2026) {
                                    $data['tanggal_terbit'] = $d;
                                    \Log::info('✓ Tanggal terbit found (VIZ 3-date logic): ' . $data['tanggal_terbit']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Office Issued from VIZ — "KANTOR YANG MENGELUARKAN / ISSUING OFFICE"
        // Only parse if we have passport data (nomor found = text contains passport data page)
        if (empty($data['kantor_terbit']) && !empty($data['nomor'])) {
            // Pattern 1: Look for city name AFTER "KANTOR YANG MENGELUARKAN" or "ISSUING OFFICE" on next line
            foreach ($lines as $i => $line) {
                $trimLine = trim($line);
                // Skip lines from "PERHATIAN" section (halaman belakang passport)
                if (preg_match('/\b(Kepolisian|terdekat|setempat|Perwakilan|dilarang|memperhatikan)\b/i', $trimLine)) {
                    continue;
                }
                // Match full or truncated keyword: "KANTOR YANG MENGELUARKAN", "KANTOR YANG MEN", "ISSUING OFFICE"
                if (preg_match('/(?:KANTOR\s*YANG\s*MEN|ISSUING\s*OFFICE)/i', $trimLine)) {
                    // Check if city is on same line after the keyword
                    if (preg_match('/(?:KANTOR\s*YANG\s*MENGE?LUARKAN|ISSUING\s*OFFICE)[:\s\/]*([A-Z][A-Z\s\-]{2,30})$/i', $trimLine, $m)) {
                        $city = trim($m[1]);
                        $city = preg_replace('/\b(ISSUING|OFFICE|KANTOR|YANG|MENGELUARKAN|MENGE)\b/i', '', $city);
                        $city = trim($city);
                        if (strlen($city) >= 3 && !preg_match('/\b(Kepolisian|Negara|Republik|terdekat)\b/i', $city)) {
                            $data['kantor_terbit'] = strtoupper($city);
                            \Log::info('✓ Kantor terbit found (VIZ same line): ' . $data['kantor_terbit']);
                            break;
                        }
                    }
                    // Check next line for city name
                    if (empty($data['kantor_terbit']) && isset($lines[$i+1])) {
                        $nextLine = trim($lines[$i+1]);
                        // City name: 3-30 chars, starts with letter, no common noise keywords
                        if (preg_match('/^([A-Z][A-Za-z\s\-]{2,30})$/i', $nextLine, $m)) {
                            $city = trim($m[1]);
                            if (!preg_match('/\b(PASPOR|PASSPORT|INDONESIA|REPUBLIK|P<IDN|MRZ|Kepolisian|Negara|terdekat|setempat|Polisi|Perwakilan|dilarang|PERHATIAN)\b/i', $city) && strlen($city) >= 3) {
                                $data['kantor_terbit'] = strtoupper($city);
                                \Log::info('✓ Kantor terbit found (VIZ next line): ' . $data['kantor_terbit']);
                                break;
                            }
                        }
                        // Also handle case where next line has MRZ chars (e.g., "TASIKMALAYA<<<<")
                        if (empty($data['kantor_terbit'])) {
                            $cleanNext = preg_replace('/[<\d]+$/', '', $nextLine); // Strip trailing < and digits
                            $cleanNext = trim($cleanNext);
                            if (preg_match('/^([A-Z][A-Z\s\-]{2,30})$/i', $cleanNext, $m)) {
                                $city = trim($m[1]);
                                if (!preg_match('/\b(PASPOR|PASSPORT|INDONESIA|REPUBLIK|Kepolisian|Negara|terdekat|setempat|Polisi|Perwakilan|dilarang|PERHATIAN)\b/i', $city) && strlen($city) >= 3) {
                                    $data['kantor_terbit'] = strtoupper($city);
                                    \Log::info('✓ Kantor terbit found (VIZ next line cleaned): ' . $data['kantor_terbit']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // Pattern 2: Search in allText for "KANTOR YANG MEN" followed by city name
            if (empty($data['kantor_terbit'])) {
                if (preg_match('/KANTOR\s*YANG\s*MEN[A-Z]*\s*(?:\/\s*ISSUING\s*OFFICE\s*)?([A-Z][A-Z\s]{2,25}?)(?:\s*<|$|\s*P<|\s*\d)/i', $allText, $m)) {
                    $city = trim($m[1]);
                    $city = preg_replace('/[<\d]+$/', '', $city);
                    $city = trim($city);
                    if (strlen($city) >= 3 && !preg_match('/\b(Kepolisian|Negara|Republik|terdekat|setempat|Polisi|Perwakilan|dilarang|PERHATIAN|PASPOR)\b/i', $city)) {
                        $data['kantor_terbit'] = strtoupper($city);
                        \Log::info('✓ Kantor terbit found (allText pattern): ' . $data['kantor_terbit']);
                    }
                }
            }
        }
        
        // Birth City/Place from VIZ — "TEMPAT LAHIR / PLACE OF BIRTH"
        if (empty($data['tempat_lahir'])) {
            // Pattern 1: Indonesian passport — tempat lahir biasanya di akhir baris setelah sex
            // Format: "27 MAY 1990 L/M GARUT" atau "27 MAY 1990 P/F JAKARTA"
            if (preg_match('/\d{1,2}\s+[A-Z]{3}\s+\d{4}\s+[LPMF]\/[MF]\s+([A-Z][A-Z\s\-]{2,30})/i', $allText, $matches)) {
                $place = trim($matches[1]);
                // Remove trailing noise
                $place = preg_replace('/\b(TGL|TANGGAL|DATE|PENGELUARAN|ISSUE|HABIS|BERLAKU|EXPIRY)\b.*/i', '', $place);
                $place = trim($place);
                if (strlen($place) >= 3 && !preg_match('/\b(INDONESIA|PASPOR|PASSPORT)\b/i', $place)) {
                    $data['tempat_lahir'] = strtoupper($place);
                    \Log::info('✓ Tempat lahir found (VIZ after sex): ' . $data['tempat_lahir']);
                }
            }
            // Pattern 2: With keyword on separate line — "TEMPAT LAHIR" then city on next line
            if (empty($data['tempat_lahir'])) {
                foreach ($lines as $i => $line) {
                    $trimLine = trim($line);
                    if (preg_match('/(?:TEMPAT\s*LAHIR|PLACE\s*OF\s*BIRTH)/i', $trimLine)) {
                        // Check if city is at end of same line
                        if (preg_match('/(?:TEMPAT\s*LAHIR|PLACE\s*OF\s*BIRTH)[:\s\/]*([A-Z][A-Z\s\-]{2,25})$/i', $trimLine, $m)) {
                            $city = trim($m[1]);
                            $city = preg_replace('/\b(PLACE|OF|BIRTH|TEMPAT|LAHIR)\b/i', '', $city);
                            $city = trim($city);
                            if (strlen($city) >= 3 && !preg_match('/\b(KELAMIN|SEX|INDONESIA)\b/i', $city)) {
                                $data['tempat_lahir'] = strtoupper($city);
                                \Log::info('✓ Tempat lahir found (VIZ keyword same line): ' . $data['tempat_lahir']);
                                break;
                            }
                        }
                        // Check next line
                        if (empty($data['tempat_lahir']) && isset($lines[$i+1])) {
                            $nextLine = trim($lines[$i+1]);
                            if (preg_match('/^([A-Z][A-Z\s\-]{2,25})$/i', $nextLine, $m)) {
                                $city = trim($m[1]);
                                if (!preg_match('/\b(INDONESIA|PASPOR|PASSPORT|KELAMIN|SEX|TGL|TANGGAL)\b/i', $city)) {
                                    $data['tempat_lahir'] = strtoupper($city);
                                    \Log::info('✓ Tempat lahir found (VIZ next line): ' . $data['tempat_lahir']);
                                    break;
                                }
                            }
                        }
                    }
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
        
        // Determine century
        // For passport: birth dates use 19xx if YY > 30, else 20xx
        // Expiry dates are always 20xx (passports don't expire in 1900s)
        // Use threshold of 50: YY >= 50 → 1900s, YY < 50 → 2000s
        if ((int)$yy >= 50) {
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
