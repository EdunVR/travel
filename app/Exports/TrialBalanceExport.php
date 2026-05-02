<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrialBalanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $filters;
    protected $summary;
    protected $rowNumber = 0;

    public function __construct($data, $filters = [], $summary = [])
    {
        $this->data = collect($data);
        $this->filters = $filters;
        $this->summary = $summary;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            ['NERACA SALDO (TRIAL BALANCE)'],
            ['Outlet: ' . ($this->filters['outlet_name'] ?? 'Semua Outlet')],
            ['Buku: ' . ($this->filters['book_name'] ?? 'Semua Buku')],
            ['Periode: ' . ($this->filters['start_date'] ?? '') . ' s/d ' . ($this->filters['end_date'] ?? '')],
            ['Dicetak: ' . now()->format('d/m/Y H:i:s')],
            [],
            [
                'Kode Akun',
                'Nama Akun',
                'Tipe',
                'Saldo Awal',
                'Debit',
                'Kredit',
                'Saldo Akhir'
            ]
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $this->rowNumber++;
        
        $typeLabel = $this->getAccountTypeLabel($row['type'] ?? '');
        
        return [
            $row['code'] ?? '',
            $row['name'] ?? '',
            $typeLabel,
            $row['opening_balance'] ?? 0,
            $row['debit'] ?? 0,
            $row['credit'] ?? 0,
            $row['ending_balance'] ?? 0
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 7; // 6 header rows + 1 heading row
        $totalRow = $lastRow + 1;
        $summaryStartRow = $totalRow + 2;
        
        // Title styling
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '1E40AF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Info rows styling
        foreach (range(2, 5) as $row) {
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ]);
        }
        
        // Header row styling
        $sheet->getStyle('A7:G7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB']
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
        
        // Data rows styling
        if ($this->data->count() > 0) {
            $sheet->getStyle("A8:G{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1']
                    ]
                ]
            ]);
            
            // Number formatting for amount columns
            $sheet->getStyle("D8:G{$lastRow}")->getNumberFormat()
                ->setFormatCode('#,##0');
            
            // Right align for amount columns
            $sheet->getStyle("D8:G{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        
        // Total row
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->setCellValue("D{$totalRow}", '');
        $sheet->setCellValue("E{$totalRow}", $this->summary['total_debit'] ?? 0);
        $sheet->setCellValue("F{$totalRow}", $this->summary['total_credit'] ?? 0);
        $sheet->setCellValue("G{$totalRow}", ($this->summary['total_debit'] ?? 0) - ($this->summary['total_credit'] ?? 0));
        
        $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        $sheet->getStyle("A{$totalRow}:C{$totalRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$totalRow}:G{$totalRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E{$totalRow}:G{$totalRow}")->getNumberFormat()
            ->setFormatCode('#,##0');
        
        // Summary section
        $sheet->setCellValue("A{$summaryStartRow}", 'RINGKASAN');
        $sheet->mergeCells("A{$summaryStartRow}:G{$summaryStartRow}");
        $sheet->getStyle("A{$summaryStartRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        
        $summaryRow = $summaryStartRow + 1;
        $sheet->setCellValue("A{$summaryRow}", 'Total Debit:');
        $sheet->setCellValue("B{$summaryRow}", $this->summary['total_debit'] ?? 0);
        $sheet->getStyle("B{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
        
        $summaryRow++;
        $sheet->setCellValue("A{$summaryRow}", 'Total Kredit:');
        $sheet->setCellValue("B{$summaryRow}", $this->summary['total_credit'] ?? 0);
        $sheet->getStyle("B{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
        
        $summaryRow++;
        $sheet->setCellValue("A{$summaryRow}", 'Selisih:');
        $sheet->setCellValue("B{$summaryRow}", $this->summary['difference'] ?? 0);
        $sheet->getStyle("B{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
        
        $summaryRow++;
        $sheet->setCellValue("A{$summaryRow}", 'Status:');
        $sheet->setCellValue("B{$summaryRow}", ($this->summary['is_balanced'] ?? true) ? 'Seimbang' : 'Tidak Seimbang');
        $sheet->getStyle("B{$summaryRow}")->getFont()->setColor(
            new \PhpOffice\PhpSpreadsheet\Style\Color(
                ($this->summary['is_balanced'] ?? true) ? '059669' : 'DC2626'
            )
        );
        
        return [];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Neraca Saldo';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Kode Akun
            'B' => 35,  // Nama Akun
            'C' => 15,  // Tipe
            'D' => 18,  // Saldo Awal
            'E' => 18,  // Debit
            'F' => 18,  // Kredit
            'G' => 18,  // Saldo Akhir
        ];
    }

    /**
     * Get account type label
     *
     * @param string $type
     * @return string
     */
    private function getAccountTypeLabel(string $type): string
    {
        $labels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
            'otherrevenue' => 'Pendapatan Lain',
            'otherexpense' => 'Beban Lain'
        ];
        
        return $labels[$type] ?? $type;
    }
}
