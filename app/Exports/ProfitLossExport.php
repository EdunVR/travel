<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ProfitLossExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    /**
     * Return collection of data for Excel
     */
    public function collection()
    {
        $rows = new Collection();

        // Add header information
        $rows->push(['LAPORAN LABA RUGI']);
        $rows->push(['Outlet: ' . ($this->filters['outlet_name'] ?? '-')]);
        $rows->push(['Periode: ' . ($this->filters['start_date'] ?? '-') . ' s/d ' . ($this->filters['end_date'] ?? '-')]);
        $rows->push(['Tanggal Generate: ' . now()->translatedFormat('d F Y H:i')]);
        $rows->push([]); // Empty row

        // Add comparison header if enabled
        if ($this->filters['comparison_enabled'] ?? false) {
            $rows->push(['Periode Pembanding: ' . ($this->filters['comparison_start_date'] ?? '-') . ' s/d ' . ($this->filters['comparison_end_date'] ?? '-')]);
            $rows->push([]); // Empty row
        }

        // Column headers
        if ($this->filters['comparison_enabled'] ?? false) {
            $rows->push(['Kode Akun', 'Nama Akun', 'Periode Saat Ini', 'Periode Pembanding', 'Selisih', 'Perubahan (%)']);
        } else {
            $rows->push(['Kode Akun', 'Nama Akun', 'Jumlah']);
        }

        // PENDAPATAN
        $rows->push(['', 'PENDAPATAN', '', '', '', '']);
        $this->addAccountRows($rows, $this->data['revenue']['accounts'] ?? [], 0);
        $rows->push(['', 'Total Pendapatan', $this->data['revenue']['total'] ?? 0, '', '', '']);
        $rows->push([]); // Empty row

        // PENDAPATAN LAIN-LAIN
        if (!empty($this->data['other_revenue']['accounts'])) {
            $rows->push(['', 'PENDAPATAN LAIN-LAIN', '', '', '', '']);
            $this->addAccountRows($rows, $this->data['other_revenue']['accounts'] ?? [], 0);
            $rows->push(['', 'Total Pendapatan Lain-Lain', $this->data['other_revenue']['total'] ?? 0, '', '', '']);
            $rows->push([]); // Empty row
        }

        // TOTAL PENDAPATAN
        $rows->push(['', 'TOTAL PENDAPATAN', $this->data['summary']['total_revenue'] ?? 0, '', '', '']);
        $rows->push([]); // Empty row

        // BEBAN OPERASIONAL
        $rows->push(['', 'BEBAN OPERASIONAL', '', '', '', '']);
        $this->addAccountRows($rows, $this->data['expense']['accounts'] ?? [], 0);
        $rows->push(['', 'Total Beban Operasional', $this->data['expense']['total'] ?? 0, '', '', '']);
        $rows->push([]); // Empty row

        // BEBAN LAIN-LAIN
        if (!empty($this->data['other_expense']['accounts'])) {
            $rows->push(['', 'BEBAN LAIN-LAIN', '', '', '', '']);
            $this->addAccountRows($rows, $this->data['other_expense']['accounts'] ?? [], 0);
            $rows->push(['', 'Total Beban Lain-Lain', $this->data['other_expense']['total'] ?? 0, '', '', '']);
            $rows->push([]); // Empty row
        }

        // TOTAL BEBAN
        $rows->push(['', 'TOTAL BEBAN', $this->data['summary']['total_expense'] ?? 0, '', '', '']);
        $rows->push([]); // Empty row

        // LABA/RUGI BERSIH
        $rows->push(['', 'LABA/RUGI BERSIH', $this->data['summary']['net_income'] ?? 0, '', '', '']);
        $rows->push([]); // Empty row

        // RASIO KEUANGAN
        $rows->push(['', 'RASIO KEUANGAN', '', '', '', '']);
        $rows->push(['', 'Gross Profit Margin', ($this->data['summary']['gross_profit_margin'] ?? 0) . '%', '', '', '']);
        $rows->push(['', 'Net Profit Margin', ($this->data['summary']['net_profit_margin'] ?? 0) . '%', '', '', '']);
        $rows->push(['', 'Operating Expense Ratio', ($this->data['summary']['operating_expense_ratio'] ?? 0) . '%', '', '', '']);

        return $rows;
    }

    /**
     * Add account rows recursively
     */
    private function addAccountRows(&$rows, $accounts, $level = 0)
    {
        foreach ($accounts as $account) {
            $indent = str_repeat('  ', $level);
            $rows->push([
                $account['code'] ?? '',
                $indent . ($account['name'] ?? ''),
                $account['amount'] ?? 0,
                '', '', ''
            ]);

            // Add children if exist
            if (!empty($account['children'])) {
                $this->addAccountRows($rows, $account['children'], $level + 1);
            }
        }
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Style header (first 4 rows)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Merge header cells
        $sheet->mergeCells('A1:F1');

        // Style info rows
        $sheet->getStyle('A2:A4')->applyFromArray([
            'font' => ['bold' => true]
        ]);

        // Find and style section headers (PENDAPATAN, BEBAN, etc.)
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            
            // Check if this is a section header
            if (in_array($cellValue, [
                'PENDAPATAN', 
                'PENDAPATAN LAIN-LAIN', 
                'TOTAL PENDAPATAN',
                'BEBAN OPERASIONAL', 
                'BEBAN LAIN-LAIN', 
                'TOTAL BEBAN',
                'LABA/RUGI BERSIH',
                'RASIO KEUANGAN'
            ])) {
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8E8E8']
                    ]
                ]);
            }

            // Check if this is a total row
            if (strpos($cellValue, 'Total') === 0 || $cellValue === 'LABA/RUGI BERSIH') {
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_DOUBLE]
                    ]
                ]);
            }
        }

        // Format number columns
        $sheet->getStyle('C:F')->getNumberFormat()->setFormatCode('#,##0.00');

        // Set alignment for amount columns
        $sheet->getStyle('C:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    /**
     * Set worksheet title
     */
    public function title(): string
    {
        return 'Laporan Laba Rugi';
    }
}
