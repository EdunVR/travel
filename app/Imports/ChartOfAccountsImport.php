<?php

namespace App\Imports;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountsImport implements ToCollection, WithHeadingRow
{
    protected $outletId;
    protected $created = 0;
    protected $updated = 0;
    protected $errors = [];

    public function __construct($outletId)
    {
        $this->outletId = $outletId;
    }

    public function collection(Collection $rows)
    {
        \Log::info('ChartOfAccountsImport mulai, total rows: ' . $rows->count());
        foreach ($rows as $index => $row) {
            \Log::info('Row keys:', $row->keys()->toArray());
            $line = $index + 2; // +2 karena heading row + base 1 index

            $row = collect($row)->mapWithKeys(function ($value, $key) {
                $key = strtolower(str_replace([' ', '-', '.'], '_', trim($key)));
                return [$key => $value];
            });

            // pastikan kode jadi string
            $row['kode'] = (string) $row['kode'];
            $row['akun_induk'] = isset($row['akun_induk']) ? (string) $row['akun_induk'] : null;


            try {
                
                $validator = Validator::make($row->toArray(), [
                    'kode' => ['required', 'max:50'],
                    'nama_akun' => 'required|string|max:255',
                    'tipe'   => ['required', 'regex:/^(asset|liability|equity|revenue|expense|otherrevenue|otherexpense)$/i'],
                    'status' => ['required', 'regex:/^(active|inactive)$/i'],
                    'kategori' => 'nullable|string|max:255',
                    'akun_induk' => 'nullable|max:50',
                    'deskripsi' => 'nullable|string',
                    'level' => 'required|integer|min:1'
                ]);

                if ($validator->fails()) {
                     $errorMsg = implode(', ', $validator->errors()->all());
                    \Log::warning("Validasi gagal di baris {$line}: {$errorMsg}");
                    $this->errors[] = [
                        'line' => $line,
                        'message' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Cari parent account jika ada
                $parentId = null;
                if (!empty($row['akun_induk'])) {
                    $parent = ChartOfAccount::where('outlet_id', $this->outletId)
                        ->where('code', $row['akun_induk'])
                        ->first();
                    
                    if ($parent) {
                        $parentId = $parent->id;
                    } else {
                        $this->errors[] = [
                            'line' => $line,
                            'message' => 'Akun induk tidak ditemukan: ' . $row['akun_induk']
                        ];
                        continue;
                    }
                }

                // Cek apakah akun sudah ada
                $existingAccount = ChartOfAccount::where('outlet_id', $this->outletId)
                    ->where('code', $row['kode'])
                    ->first();

                if ($existingAccount) {
                    // Update existing account
                    $existingAccount->update([
                        'name' => $row['nama_akun'],
                        'type' => $row['tipe'],
                        'category' => $row['kategori'] ?? null,
                        'parent_id' => $parentId,
                        'status' => $row['status'],
                        'description' => $row['deskripsi'] ?? null,
                        'level' => $row['level']
                    ]);
                    $this->updated++;
                } else {
                    // Create new account
                    ChartOfAccount::create([
                        'outlet_id' => $this->outletId,
                        'code' => $row['kode'],
                        'name' => $row['nama_akun'],
                        'type' => $row['tipe'],
                        'category' => $row['kategori'] ?? null,
                        'parent_id' => $parentId,
                        'status' => $row['status'],
                        'description' => $row['deskripsi'] ?? null,
                        'level' => $row['level'],
                        'balance' => 0
                    ]);
                    $this->created++;
                }

            } catch (\Exception $e) {
                \Log::error("Import error at line {$line}: " . $e->getMessage(), ['row' => $row]);
                $this->errors[] = [
                    'line' => $line,
                    'message' => $e->getMessage()
                ];
            }
        }
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getUpdated(): int
    {
        return $this->updated;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getTotal(): int
    {
        return $this->created + $this->updated;
    }

    public function getResultMessage(): string
    {
        $message = "{$this->getTotal()} data diproses";
        if ($this->created > 0) {
            $message .= ", {$this->created} akun baru";
        }
        if ($this->updated > 0) {
            $message .= ", {$this->updated} akun diupdate";
        }
        if (count($this->errors) > 0) {
            $message .= ", " . count($this->errors) . " error";
        }
        return $message;
    }
}