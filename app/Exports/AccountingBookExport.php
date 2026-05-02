<?php

namespace App\Exports;

use App\Models\AccountingBook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AccountingBookExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting, WithEvents
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
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Kode Buku',
            'Nama Buku',
            'Tipe',
            'Mata Uang',
            'Periode Mulai',
            'Periode Berakhir',
            'Saldo Awal',
            'Saldo Akhir',
            'Total Entri',
            'Status',
            'Outlet',
            'Deskripsi',
            'Dibuat Pada'
        ];
    }

    public function map($book): array
    {
        return [
            $book->code,
            $book->name,
            $this->getTypeName($book->type),
            $book->currency,
            $book->start_date ? \Carbon\Carbon::parse($book->start_date)->format('d/m/Y') : '',
            $book->end_date ? \Carbon\Carbon::parse($book->end_date)->format('d/m/Y') : '',
            $book->opening_balance,
            $book->closing_balance,
            $book->total_entries,
            $this->getStatusName($book->status),
            $book->outlet ? $book->outlet->nama_outlet : '',
            $book->description ?? '',
            $book->created_at ? \Carbon\Carbon::parse($book->created_at)->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    public function title(): string
    {
        return 'Accounting Books';
    }

    private function getTypeName($type): string
    {
        $types = [
            'general' => 'Umum',
            'cash' => 'Kas',
            'bank' => 'Bank',
            'sales' => 'Penjualan',
            'purchase' => 'Pembelian',
            'inventory' => 'Persediaan',
            'payroll' => 'Penggajian'
        ];

        return $types[$type] ?? $type;
    }

    private function getStatusName($status): string
    {
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'draft' => 'Draft',
            'closed' => 'Ditutup'
        ];

        return $statuses[$status] ?? $status;
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Saldo Awal
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Saldo Akhir
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size all columns
                foreach (range('A', 'M') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Add borders
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getDelegate()->getStyle('A1:M' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
        ];
    }
}
