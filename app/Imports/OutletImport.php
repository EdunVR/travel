<?php

namespace App\Imports;

use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class OutletImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Outlet([
            'kode_outlet' => $row['kode_outlet'] ?? $row['kode_outlet'],
            'nama_outlet' => $row['nama_outlet'] ?? $row['nama_outlet'],
            'kota' => $row['kota'] ?? $row['kota'],
            'alamat' => $row['alamat'] ?? $row['alamat'],
            'telepon' => $row['telepon'] ?? $row['telepon'],
            'is_active' => isset($row['status']) ? 
                (strtoupper($row['status']) === 'AKTIF' ? true : false) : true,
            'catatan' => $row['catatan'] ?? $row['catatan'],
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_outlet' => 'required|unique:outlets,kode_outlet',
            'nama_outlet' => 'required',
            'kota' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_outlet.required' => 'Kode outlet wajib diisi',
            'kode_outlet.unique' => 'Kode outlet sudah ada',
            'nama_outlet.required' => 'Nama outlet wajib diisi',
            'kota.required' => 'Kota wajib diisi',
        ];
    }
}