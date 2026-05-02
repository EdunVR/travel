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

class GeneralLedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting, WithEvents
{
    protected $data;
    protected $filters;
    protected $currentRow = 2; // Start after header

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    /**
     * Return collection of data to export
     * We'll flatten the nested structure for Excel
     */
    public function collection()
    {
        $flattenedData = [];
        
        if (!isset($this->data['ledger_entries'])) {
            return collect($flattenedData);
        }

        foreach ($this->data['ledger_entries'] as $accountEntry) {
            // Add opening balance row
            $flattenedData[] = (object)[
                'type' => 'opening_balance',
                'account_code' => $accountEntry['account_code'],
                'account_name' => $accountEntry['account_name'],
                'date' => $this->filters['start_date'] ?? '',
                'reference' => 'SALDO-AWAL',
                'description' => 'Saldo Awal Periode',
                'debit' => $accountEntry['opening_balance'] > 0 ? $accountEntry['opening_balance'] : 0,
                'credit' => $accountEntry['opening_balance'] < 0 ? abs($accountEntry['opening_balance']) : 0,
                'balance' => $accountEntry['opening_balance']
            ];

            // Add transactions
            foreach ($accountEntry['transactions'] as $transaction) {
                $flattenedData[] = (object)[
                    'type' => 'transaction',
                    'account_code' => $accountEntry['account_code'],
                    'account_name' => $accountEntry['account_name'],
                    'date' => $transaction['date'],
                    'reference' => $transaction['reference'],
                    'description' => $transaction['description'],
                    'debit' => $transaction['debit'],
                    'credit' => $transaction['credit'],
                    'balance' => $transaction['balance']
                ];
            }

            // Add account total row
            $flattenedData[] = (object)[
                'type' => 'account_total',
                'account_code' => $accountEntry['account_code'],
                'account_name' => $accountEntry['account_name'],
                'date' => '',
                'reference' => 'TOTAL',
                'description' => 'Total ' . $accountEntry['account_code'],
                'debit' => $accountEntry['total_debit'],
                'credit' => $accountEntry['total_credit'],
                'balance' => $accountEntry['ending_balance']
            ];

            // Add spacer row
            $flattenedData[] = (object)[
                'type' => 'spacer',
                'account_code' => '',
                'account_name' => '',
                'date' => '',
                'reference' => '',
                'description' => '',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0
            ];
        }

        // Add grand total
        if (isset($this->data['summary'])) {
            $flattenedData[] = (object)[
                'type' => 'grand_total',
                'account_code' => '',
                'account_name' => '',
                'date' => '',
                'reference' => '',
                'description' => 'TOTAL BUKU BESAR',
                'debit' => $this->data['summary']['total_debit'],
                'credit' => $this->data['summary']['total_credit'],
                'balance' => $this->data['summary']['balance']
            ];
        }

        return collect($flattenedData);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Kode Akun',
            'Nama Akun',
            'Tanggal',
            'Referensi',
            'Keterangan',
            'Debit',
            'Kredit',
            'Saldo'
        ];
    }

    /**
     * Map data to columns
     */
    public function map($row): array
    {
        return [
            $row->account_code ?? '',
            $row->account_name ?? '',
            $row->date ? date('d/m/Y', strtotime($row->date)) : '',
            $row->reference ?? '',
            $row->description ?? '',
            $row->debit ?? 0,
            $row->credit ?? 0,
            $row->balance ?? 0
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
        return 'Buku Besar';
    }

    /**
     * Format columns
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Debit
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Kredit
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Saldo
        ];
    }

    /**
     * Register events for additional styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowIndex = 2; // Start after header
                
                foreach ($this->collection() as $row) {
                    // Style opening balance rows
                    if ($row->type === 'opening_balance') {
                        $sheet->getStyle("A{$rowIndex}:H{$rowIndex}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'EFF6FF']
                            ],
                            'font' => ['bold' => true]
                        ]);
                    }
                    
                    // Style account total rows
                    if ($row->type === 'account_total') {
                        $sheet->getStyle("A{$rowIndex}:H{$rowIndex}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F1F5F9']
                            ],
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                                    'color' => ['rgb' => '94A3B8']
                                ]
                            ]
                        ]);
                    }
                    
                    // Style grand total row
                    if ($row->type === 'grand_total') {
                        $sheet->getStyle("A{$rowIndex}:H{$rowIndex}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E2E8F0']
                            ],
                            'font' => ['bold' => true, 'size' => 12],
                            'borders' => [
                                'top' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                    'color' => ['rgb' => '64748B']
                                ]
                            ]
                        ]);
                    }
                    
                    $rowIndex++;
                }
                
                // Auto-size columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
