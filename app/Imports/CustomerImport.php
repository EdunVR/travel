<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Tipe;
use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class CustomerImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{

    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $lastKodeMember = null;
    protected $nextNumber = 1;

    public function model(array $row)
    {
        try {
            // Skip empty rows (all values are null or empty)
            $hasData = false;
            foreach ($row as $value) {
                if (!empty($value)) {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData) {
                return null; // Skip this row silently
            }
            
            // Manual validation
            $nama = isset($row['nama']) ? trim($row['nama']) : null;
            $telepon = isset($row['telepon']) ? trim((string) $row['telepon']) : null;
            $tipeCustomer = isset($row['tipe_customer']) ? trim($row['tipe_customer']) : null;
            $outletName = isset($row['outlet']) ? trim($row['outlet']) : null;
            
            // Validate required fields
            if (empty($nama)) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Nama wajib diisi";
                $this->errorCount++;
                return null;
            }
            
            if (empty($telepon)) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Telepon wajib diisi";
                $this->errorCount++;
                return null;
            }
            
            if (empty($tipeCustomer)) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Tipe Customer wajib diisi";
                $this->errorCount++;
                return null;
            }
            
            if (empty($outletName)) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Outlet wajib diisi";
                $this->errorCount++;
                return null;
            }
            
            // Cari tipe berdasarkan nama
            $tipe = Tipe::where('nama_tipe', $tipeCustomer)->first();
            if (!$tipe) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Tipe '{$tipeCustomer}' tidak ditemukan";
                $this->errorCount++;
                return null;
            }

            // Cari outlet berdasarkan nama
            $outlet = Outlet::where('nama_outlet', $outletName)->first();
            if (!$outlet) {
                $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": Outlet '{$outletName}' tidak ditemukan";
                $this->errorCount++;
                return null;
            }

            // Generate kode member jika tidak ada
            $kodeMember = isset($row['kode_member']) ? trim((string) $row['kode_member']) : null;
            if (empty($kodeMember)) {
                // Initialize counter hanya sekali
                if ($this->lastKodeMember === null) {
                    // Get max kode_member as integer
                    $maxKode = Member::whereNotNull('kode_member')
                        ->selectRaw('MAX(CAST(kode_member AS UNSIGNED)) as max_kode')
                        ->value('max_kode');
                    
                    if ($maxKode) {
                        $this->nextNumber = intval($maxKode) + 1;
                    } else {
                        $this->nextNumber = 1;
                    }
                    
                    $this->lastKodeMember = 'initialized';
                }
                
                $kodeMember = str_pad($this->nextNumber, 6, '0', STR_PAD_LEFT);
                $this->nextNumber++; // Increment untuk row berikutnya
            }

            $this->successCount++;

            return new Member([
                'kode_member' => $kodeMember,
                'nama' => $nama,
                'telepon' => $telepon,
                'alamat' => isset($row['alamat']) ? trim($row['alamat']) : null,
                'id_tipe' => $tipe->id_tipe,
                'id_outlet' => $outlet->id_outlet,
            ]);

        } catch (\Exception $e) {
            $this->errors[] = "Baris " . ($this->successCount + $this->errorCount + 2) . ": " . $e->getMessage();
            $this->errorCount++;
            return null;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
