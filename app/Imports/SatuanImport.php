<?php

namespace App\Imports;

use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SatuanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari satuan utama berdasarkan nama
        $satuanUtama = null;
        if (!empty($row['satuan_utama'])) {
            $satuanUtama = Satuan::where('nama_satuan', $row['satuan_utama'])->first();
        }

        return new Satuan([
            'kode_satuan' => $row['kode_satuan'] ?? Satuan::generateKodeSatuan(),
            'nama_satuan' => $row['nama_satuan'],
            'simbol' => $row['simbol'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? null,
            'is_active' => isset($row['status']) ? 
                (strtoupper($row['status']) === 'AKTIF' ? true : false) : true,
            'nilai_konversi' => $row['nilai_konversi'] ?? null,
            'satuan_utama_id' => $satuanUtama ? $satuanUtama->id_satuan : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_satuan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_satuan.required' => 'Nama satuan wajib diisi',
        ];
    }
}