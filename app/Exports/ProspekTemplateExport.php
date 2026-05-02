<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProspekTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Berikan contoh data dengan nilai default untuk status
        return [
            [
                date('d/m/Y'),
                'John Doe', // Nama Lengkap
                'PT Contoh', // Nama Perusahaan
                'Perusahaan', // Jenis Usaha
                '08123456789', // Telepon
                'contoh@email.com', // Email
                'Jl. Contoh No. 123', // Alamat
                '', // Provinsi ID
                '', // Kabupaten ID
                '', // Kecamatan ID
                '', // Desa ID
                'John', // Pemilik/Manager
                '100 unit/hari', // Kapasitas Produksi
                'Manual', // Sistem Produksi
                'Listrik', // Bahan Bakar
                'Perusahaan bergerak di bidang...', // Informasi Perusahaan
                '-6.123456', // Latitude
                '106.123456', // Longitude
                'prospek', // Status (contoh nilai)
                '1', // Recruitment ID
                '1', // Outlet ID
                'tidak'  // Using Boiler
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Lengkap',
            'Nama Perusahaan',
            'Jenis Usaha',
            'Telepon',
            'Email',
            'Alamat',
            'Provinsi ID',
            'Kabupaten ID',
            'Kecamatan ID',
            'Desa ID',
            'Pemilik Manager',
            'Kapasitas Produksi',
            'Sistem Produksi',
            'Bahan Bakar',
            'Informasi Perusahaan',
            'Latitude',
            'Longitude',
            'Status',
            'Recruitment ID',
            'Outlet ID',
            'Menggunakan Boiler'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set format kolom telepon (kolom D) sebagai teks
        $sheet->getStyle('E2:D1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('A2:A1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => ['alignment' => ['wrapText' => true]],
            // Dropdown untuk status
            'S2:S1000' => [
                'dataValidation' => [
                    'type' => 'list',
                    'formula1' => '"prospek,followup,negosiasi,closing,deposit,gagal"',
                    'allowBlank' => true
                ]
            ],
            'E1' => [
                'comment' => [
                    'author' => 'System',
                    'text' => 'Format nomor telepon: 89602087480 (tanpa tanda +, spasi, atau tanda baca). Pastikan kolom diformat sebagai teks.'
                ]
            ],
            // Catatan untuk email
            'F1' => [
                'comment' => [
                    'author' => 'System',
                    'text' => 'Email bersifat optional. Kosongkan jika tidak ada.'
                ]
            ]
        ];
    }
}