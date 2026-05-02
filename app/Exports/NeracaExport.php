<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class NeracaExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = collect($data);
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->data->map(function($item) {
            return [
                'section' => $item->section ?? '',
                'code' => $item->code ?? '',
                'name' => $item->name ?? '',
                'balance' => is_numeric($item->balance) ? $item->balance : ''
            ];
        });
    }

    public function headings(): array
    {
        $outlet = $this->filters['outlet_name'] ?? 'Semua Outlet';
        $endDate = $this->filters['end_date'] ?? date('Y-m-d');
        
        return [
            ['NERACA (BALANCE SHEET)'],
            [$outlet],
            ['Per Tanggal: ' . date('d F Y', strtotime($endDate))],
            [],
            ['Bagian', 'Kode Akun', 'Nama Akun', 'Saldo']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1E40AF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        $sheet->getStyle('A2:D3')->applyFromArray([
            'font' => ['size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Column headers
        $sheet->getStyle('A5:D5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Number format for balance column
        $sheet->getStyle('D6:D' . $lastRow)->getNumberFormat()
            ->setFormatCode('#,##0');
        
        // Right align balance column
        $sheet->getStyle('D6:D' . $lastRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        return [];
    }

    public function title(): string
    {
        return 'Neraca';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 40,
            'D' => 20
        ];
    }
}
