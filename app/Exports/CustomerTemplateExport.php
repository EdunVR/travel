<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomerTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return collection of sample data
     */
    public function collection()
    {
        return collect([
            [
                'kode_member' => '',
                'nama' => 'John Doe',
                'telepon' => '08123456789',
                'alamat' => 'Jl. Contoh No. 123',
                'tipe_customer' => 'Umum',
                'outlet' => 'PBU',
            ],
            [],
            ['CATATAN:'],
            ['1. kode_member boleh dikosongkan (akan di-generate otomatis)'],
            ['2. nama, telepon, tipe_customer, dan outlet wajib diisi'],
            ['3. tipe_customer harus sesuai dengan data yang ada di sistem'],
            ['4. outlet harus sesuai dengan nama outlet yang ada di sistem'],
        ]);
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'kode_member',
            'nama',
            'telepon',
            'alamat',
            'tipe_customer',
            'outlet',
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style notes (rows 4-8)
        $sheet->getStyle('A4:A8')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666'],
            ],
        ]);

        return [];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 35,
            'E' => 20,
            'F' => 15,
        ];
    }
}
