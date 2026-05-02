<?php

namespace App\Imports;

use App\Models\Bahan;
use App\Models\Outlet;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class BahanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari outlet berdasarkan nama
        $outlet = Outlet::where('nama_outlet', $row['outlet'])->first();
        // Cari satuan berdasarkan nama
        $satuan = Satuan::where('nama_satuan', $row['satuan'])->first();

        // Jika kode_bahan tidak disediakan, generate otomatis
        $kodeBahan = $row['kode_bahan'] ?? Bahan::generateKodeBahan();

        return new Bahan([
            'kode_bahan' => $kodeBahan,
            'nama_bahan' => $row['nama_bahan'] ?? $row['nama_bahan'],
            'id_outlet' => $outlet ? $outlet->id_outlet : auth()->user()->akses_outlet[0] ?? 1,
            'id_satuan' => $satuan ? $satuan->id_satuan : 1,
            'merk' => $row['merk'] ?? $row['merk'] ?? '-',
            'catatan' => $row['catatan'] ?? $row['catatan'] ?? '',
            'is_active' => isset($row['status']) ? 
                (strtoupper($row['status']) === 'AKTIF' ? true : false) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required',
            'outlet' => 'required',
            'satuan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_bahan.required' => 'Nama bahan wajib diisi',
            'outlet.required' => 'Outlet wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
        ];
    }
}