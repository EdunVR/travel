<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

/**
 * Journal Export Class
 * 
 * Exports journal entry data to Excel format with proper formatting,
 * column headers, and number formatting for financial values.
 * 
 * Columns exported:
 * - Tanggal (Date)
 * - No. Transaksi (Transaction Number)
 * - Kode Akun (Account Code)
 * - Nama Akun (Account Name)
 * - Deskripsi (Description)
 * - Debit
 * - Kredit (Credit)
 * - Outlet
 * - Buku (Book)
 * - Status
 * 
 * @package App\Exports
 * @author ERP System
 * @version 1.0.0
 */
class JournalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting, WithEvents
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    /**
     * Return collection of data to export
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Transaksi',
            'Kode Akun',
            'Nama Akun',
            'Deskripsi',
            'Debit',
            'Kredit',
            'Outlet',
            'Buku',
            'Status'
        ];
    }

    /**
     * Map data to columns
     */
    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
            $row->transaction_number ?? '',
            $row->account_code ?? '',
            $row->account_name ?? '',
            $row->description ?? '',
            floatval($row->debit ?? 0),
            floatval($row->credit ?? 0),
            $row->outlet_name ?? '',
            $row->book_name ?? '',
            $this->getStatusName($row->status ?? 'draft')
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    /**
     * Set worksheet title
     */
    public function title(): string
    {
        return 'Daftar Jurnal';
    }

    /**
     * Format columns
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Debit
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Kredit
        ];
    }

    /**
     * Get status name in Indonesian
     */
    private function getStatusName($status): string
    {
        return match($status) {
            'draft' => 'Draft',
            'posted' => 'Diposting',
            'void' => 'Dibatalkan',
            default => 'Draft'
        };
    }

    /**
     * Register events for additional styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(12);  // Tanggal
                $sheet->getColumnDimension('B')->setWidth(16);  // No. Transaksi
                $sheet->getColumnDimension('C')->setWidth(12);  // Kode Akun
                $sheet->getColumnDimension('D')->setWidth(25);  // Nama Akun
                $sheet->getColumnDimension('E')->setWidth(30);  // Deskripsi
                $sheet->getColumnDimension('F')->setWidth(16);  // Debit
                $sheet->getColumnDimension('G')->setWidth(16);  // Kredit
                $sheet->getColumnDimension('H')->setWidth(18);  // Outlet
                $sheet->getColumnDimension('I')->setWidth(18);  // Buku
                $sheet->getColumnDimension('J')->setWidth(12);  // Status
                
                // Add borders to all cells with data
                $sheet->getStyle('A1:J' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
                
                // Style data rows with alternating colors
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FA']
                            ]
                        ]);
                    }
                }
                
                // Center align specific columns
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                // Right align amount columns
                $sheet->getStyle('F2:G' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                // Add total row if there's data
                if ($highestRow > 1) {
                    $totalRow = $highestRow + 1;
                    $sheet->setCellValue('E' . $totalRow, 'TOTAL:');
                    $sheet->setCellValue('F' . $totalRow, '=SUM(F2:F' . $highestRow . ')');
                    $sheet->setCellValue('G' . $totalRow, '=SUM(G2:G' . $highestRow . ')');
                    
                    // Style total row
                    $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E9ECEF']
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['rgb' => '4F46E5']
                            ]
                        ]
                    ]);
                    
                    $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
                }
                
                // Freeze header row
                $sheet->freezePane('A2');
            }
        ];
    }
}
