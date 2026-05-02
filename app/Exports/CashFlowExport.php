<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CashFlowExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        $rows = collect();

        // Header Information
        $rows->push(['LAPORAN ARUS KAS']);
        $rows->push([config('app.name')]);
        $rows->push(['Periode: ' . ($this->filters['start_date'] ?? '') . ' s/d ' . ($this->filters['end_date'] ?? '')]);
        $rows->push(['Metode: ' . ($this->filters['method'] === 'direct' ? 'Langsung (Direct)' : 'Tidak Langsung (Indirect)')]);
        $rows->push(['Outlet: ' . ($this->filters['outlet_name'] ?? 'Semua Outlet')]);
        $rows->push(['Buku: ' . ($this->filters['book_name'] ?? 'Semua Buku')]);
        $rows->push([]); // Empty row

        // Operating Activities
        $rows->push(['AKTIVITAS OPERASI']);
        foreach ($this->data['operating'] as $item) {
            $rows->push([
                $item['name'],
                $item['amount']
            ]);
        }
        $rows->push([
            'Kas Bersih dari Aktivitas Operasi',
            $this->data['netOperating']
        ]);
        $rows->push([]); // Empty row

        // Investing Activities
        $rows->push(['AKTIVITAS INVESTASI']);
        foreach ($this->data['investing'] as $item) {
            $rows->push([
                $item['name'],
                $item['amount']
            ]);
        }
        $rows->push([
            'Kas Bersih dari Aktivitas Investasi',
            $this->data['netInvesting']
        ]);
        $rows->push([]); // Empty row

        // Financing Activities
        $rows->push(['AKTIVITAS PENDANAAN']);
        foreach ($this->data['financing'] as $item) {
            $rows->push([
                $item['name'],
                $item['amount']
            ]);
        }
        $rows->push([
            'Kas Bersih dari Aktivitas Pendanaan',
            $this->data['netFinancing']
        ]);
        $rows->push([]); // Empty row

        // Summary
        $rows->push(['RINGKASAN']);
        $rows->push(['Kenaikan (Penurunan) Kas Bersih', $this->data['netCashFlow']]);
        $rows->push(['Kas Awal Periode', $this->data['beginningCash']]);
        $rows->push(['Kas Akhir Periode', $this->data['endingCash']]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Keterangan', 'Jumlah (Rp)']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styles
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('A5:B5');
        $sheet->mergeCells('A6:B6');

        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->getStyle('A2:B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Section headers (AKTIVITAS OPERASI, etc.)
        $sectionRows = [];
        $currentRow = 8;
        
        foreach ($this->collection() as $index => $row) {
            if (isset($row[0]) && in_array($row[0], ['AKTIVITAS OPERASI', 'AKTIVITAS INVESTASI', 'AKTIVITAS PENDANAAN', 'RINGKASAN'])) {
                $sectionRows[] = $currentRow + $index;
            }
        }

        foreach ($sectionRows as $row) {
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ]);
        }

        // Number format for amounts
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode('#,##0');

        // Borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A8:B{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Laporan Arus Kas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 20,
        ];
    }
}
