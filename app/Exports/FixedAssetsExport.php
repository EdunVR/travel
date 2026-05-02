<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

/**
 * Fixed Assets Export Class
 * 
 * Exports fixed asset data to Excel format with proper formatting for
 * financial values and localized category/status labels.
 * 
 * Columns exported:
 * - Kode Aset (Asset Code)
 * - Nama Aset (Asset Name)
 * - Kategori (Category)
 * - Lokasi (Location)
 * - Tanggal Perolehan (Acquisition Date)
 * - Harga Perolehan (Acquisition Cost)
 * - Nilai Residu (Salvage Value)
 * - Metode Penyusutan (Depreciation Method)
 * - Umur Ekonomis (Useful Life in Years)
 * - Akumulasi Penyusutan (Accumulated Depreciation)
 * - Nilai Buku (Book Value)
 * - Status
 * - Outlet
 * 
 * @package App\Exports
 * @author ERP System
 * @version 1.0.0
 */
class FixedAssetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
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
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Lokasi',
            'Tanggal Perolehan',
            'Harga Perolehan',
            'Nilai Residu',
            'Metode Penyusutan',
            'Umur Ekonomis (Tahun)',
            'Akumulasi Penyusutan',
            'Nilai Buku',
            'Status',
            'Outlet'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->code ?? '',
            $asset->name ?? '',
            $this->formatCategory($asset->category ?? ''),
            $asset->location ?? '',
            $asset->acquisition_date ? date('d/m/Y', strtotime($asset->acquisition_date)) : '',
            (float) ($asset->acquisition_cost ?? 0),
            (float) ($asset->salvage_value ?? 0),
            $this->formatDepreciationMethod($asset->depreciation_method ?? ''),
            (int) ($asset->useful_life ?? 0),
            (float) ($asset->accumulated_depreciation ?? 0),
            (float) ($asset->book_value ?? 0),
            $this->formatStatus($asset->status ?? ''),
            $asset->outlet->nama_outlet ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Harga Perolehan
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Nilai Residu
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Akumulasi Penyusutan
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Nilai Buku
        ];
    }

    protected function formatCategory($category): string
    {
        $categories = [
            'building' => 'Bangunan',
            'vehicle' => 'Kendaraan',
            'equipment' => 'Peralatan',
            'furniture' => 'Furniture',
            'electronics' => 'Elektronik',
            'computer' => 'Komputer & IT',
            'land' => 'Tanah',
            'other' => 'Lainnya'
        ];

        return $categories[$category] ?? ucfirst($category);
    }

    protected function formatDepreciationMethod($method): string
    {
        $methods = [
            'straight_line' => 'Garis Lurus',
            'declining_balance' => 'Saldo Menurun',
            'double_declining' => 'Saldo Menurun Ganda',
            'units_of_production' => 'Unit Produksi'
        ];

        return $methods[$method] ?? ucfirst(str_replace('_', ' ', $method));
    }

    protected function formatStatus($status): string
    {
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'disposed' => 'Dijual/Dihapus',
            'sold' => 'Terjual'
        ];

        return $statuses[$status] ?? ucfirst($status);
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
                
                // Style header with better colors
                $event->sheet->getDelegate()->getStyle('A1:M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '10B981']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
            }
        ];
    }
}
