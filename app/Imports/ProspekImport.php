<?php

namespace App\Imports;

use App\Models\Prospek;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ProspekImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows, WithCustomValueBinder
{
    private $rowCount = 0;
    private $skippedRows = 0;
    private $processedRows = 0;
    private $errors = [];

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'A') {
            // Biarkan sebagai teks mentah
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        // Handle kolom telepon untuk memaksa format text
        if (in_array($cell->getColumn(), ['E'])) { // Kolom D adalah telepon
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip baris yang seluruh kolomnya kosong
            if ($this->isRowEmpty($row)) {
                \Log::info("Skipping empty row ".($index+2));
                $this->skippedRows++;
                continue;
            }
            
            try {
                // Debug: Log data row
                \Log::info("Processing row ".($index+2).": ", $row->toArray());
                
                // Normalisasi data
                $telepon = $this->normalizeTelepon($row['telepon'] ?? $row['telepon'] ?? null);
                $email = $this->normalizeEmail($row['email'] ?? 'belum ada email');
                $status = $this->normalizeStatus($row['status'] ?? null);

                \Log::info("Nilai asli: {$row['tanggal']} | Hasil konversi: " . $this->parseTanggal($row['tanggal']));
                
                $prospek = Prospek::create([
                    'tanggal' => $this->parseTanggal($row['tanggal'] ?? null),
                    'nama' => $this->nullIfEmpty($row['nama_lengkap'] ?? $row['nama lengkap'] ?? null),
                    'nama_perusahaan' => $this->nullIfEmpty($row['nama_perusahaan'] ?? $row['nama perusahaan'] ?? null),
                    'jenis' => $this->nullIfEmpty($row['jenis_usaha'] ?? $row['jenis usaha'] ?? null),
                    'telepon' => $telepon,
                    'email' => $email,
                    'alamat' => $this->nullIfEmpty($row['alamat'] ?? null),
                    'provinsi_id' => $this->nullIfEmpty($row['provinsi_id'] ?? null),
                    'kabupaten_id' => $this->nullIfEmpty($row['kabupaten_id'] ?? null),
                    'kecamatan_id' => $this->nullIfEmpty($row['kecamatan_id'] ?? null),
                    'desa_id' => $this->nullIfEmpty($row['desa_id'] ?? null),
                    'pemilik_manager' => $this->nullIfEmpty($row['pemilik_manager'] ?? $row['pemilik manager'] ?? null),
                    'kapasitas_produksi' => $this->nullIfEmpty($row['kapasitas_produksi'] ?? $row['kapasitas produksi'] ?? null),
                    'sistem_produksi' => $this->nullIfEmpty($row['sistem_produksi'] ?? $row['sistem produksi'] ?? null),
                    'bahan_bakar' => $this->nullIfEmpty($row['bahan_bakar'] ?? $row['bahan bakar'] ?? null),
                    'informasi_perusahaan' => $this->nullIfEmpty($row['informasi_perusahaan'] ?? $row['informasi perusahaan'] ?? null),
                    'latitude' => $this->nullIfEmpty($row['latitude'] ?? null),
                    'longitude' => $this->nullIfEmpty($row['longitude'] ?? null),
                    'current_status' => $status,
                    'recruitment_id' => $this->nullIfEmpty($row['recruitment_id'] ?? $row['recruitment id'] ?? 1),
                    'id_outlet' => $this->nullIfEmpty($row['outlet_id'] ?? $row['outlet id'] ?? 1),
                    'menggunakan_boiler' => $this->nullIfEmpty($row['menggunakan_boiler'] ?? $row['menggunakan boiler'] ?? null)
                ]);
                
                $this->rowCount++;
                $this->processedRows++;
                
                \Log::info("Successfully imported row ".($index+2)." with ID: ".$prospek->id);
                
            } catch (\Exception $e) {
                $errorMsg = "Error processing row ".($index+2).": " . $e->getMessage();
                $this->errors[] = $errorMsg;
                \Log::error($errorMsg);
                \Log::error("Row data: ".json_encode($row));
                continue;
            }
        }
    }

    private function parseTanggal($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle angka serial Excel (seperti 45666)
        if (is_numeric($value) && $value > 1000) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::error("Excel date conversion error: {$value} - " . $e->getMessage());
            }
        }

        // Handle format teks (12/3/2024)
        try {
            // Coba parsing format d/m/Y
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            
            // Coba parsing otomatis untuk format lain
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning("Gagal parsing tanggal: {$value}");
            return null;
        }
    }

    private function normalizeTelepon($value)
    {
        // Jika null, kembalikan null
        if ($this->nullIfEmpty($value) === null) {
            return 'belum ada telepon';
        }

        // Konversi ke string
        $value = (string)$value;

        // Handle scientific notation (e.g., 8.96E+10)
        if (preg_match('/^\d+\.\d+E\+\d+$/', $value)) {
            $value = number_format((float)$value, 0, '', '');
        }

        // Hapus semua karakter non-digit
        $cleaned = preg_replace('/[^0-9]/', '', $value);

        // Jika hasil cleaning kosong, kembalikan null
        return $cleaned === '' ? null : $cleaned;
    }
    
    // Normalisasi email
    private function normalizeEmail($email)
    {
        $email = $this->nullIfEmpty($email);
        
        if ($email === null) {
            return null;
        }
        
        // Bersihkan email dari spasi
        $email = trim($email);
        
        // Jika email tidak valid, kembalikan null
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        
        return strtolower($email);
    }
    
    // Normalisasi status
    private function normalizeStatus($status)
    {
        if (empty($status)) {
            return 'prospek';
        }
        
        $status = strtolower(trim($status));
        $allowedStatuses = ['prospek', 'followup', 'negosiasi', 'closing', 'deposit', 'gagal'];
        
        if (!in_array($status, $allowedStatuses)) {
            return 'prospek';
        }
        
        return $status;
    }
    
    // Helper method untuk mengecek baris kosong (versi lebih fleksibel)
    private function isRowEmpty($row)
    {
        $requiredColumns = ['nama_lengkap', 'nama_perusahaan', 'telepon', 'email'];
        
        // Cek dengan berbagai variasi penulisan header
        $altHeaders = [
            'nama_lengkap' => ['nama lengkap'],
            'nama_perusahaan' => ['nama perusahaan'],
        ];
        
        foreach ($requiredColumns as $column) {
            // Cek nama kolom utama
            if (!empty($row[$column])) {
                return false;
            }
            
            // Cek alternatif penulisan header
            if (isset($altHeaders[$column])) {
                foreach ($altHeaders[$column] as $alt) {
                    if (!empty($row[$alt])) {
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
    
    // Helper method untuk mengubah string kosong menjadi null
    private function nullIfEmpty($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        return ($value === '' || $value === null) ? null : $value;
    }
    
    public function getRowCount()
    {
        return $this->rowCount;
    }
    
    public function getSkippedRows()
    {
        return $this->skippedRows;
    }
    
    public function getProcessedRows()
    {
        return $this->processedRows;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'nullable',
            'nama_lengkap' => 'required_without_all:nama_perusahaan,telepon,email,alamat|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'jenis_usaha' => 'nullable|string|max:255',
            'telepon' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // Validasi custom untuk menerima berbagai format
                    if ($value === null || $value === '') {
                        return;
                    }
                    
                    // Konversi scientific notation
                    if (is_float($value) && strpos(strtoupper((string)$value), 'E') !== false) {
                        return;
                    }
                    
                    // Jika bukan string atau numeric, beri error
                    if (!is_string($value) && !is_numeric($value)) {
                        $fail('Format telepon tidak valid');
                    }
                },
                'max:20'
            ],
            'email' => 'nullable|string', // Diubah menjadi nullable
            'alamat' => 'nullable|string',
            'status' => 'nullable|string|in:prospek,followup,negosiasi,closing,deposit,gagal',
            'using_boiler' => 'nullable|string',
            // Kolom lainnya juga nullable
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            'email.email' => 'Format email tidak valid, akan diubah menjadi null',
            // Pesan validasi custom lainnya
        ];
    }
}