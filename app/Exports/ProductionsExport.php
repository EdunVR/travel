<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $productions;
    protected $outlet;
    protected $request;

    public function __construct($productions, $outlet, $request)
    {
        $this->productions = $productions;
        $this->outlet = $outlet;
        $this->request = $request;
    }

    public function collection()
    {
        return $this->productions;
    }

    public function headings(): array
    {
        return [
            'Kode Produksi',
            'Produk',
            'Lini Produksi',
            'Target Quantity',
            'Realisasi Quantity',
            'Progress (%)',
            'Status',
            'Prioritas',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Tanggal Kadaluarsa',
            'Lokasi Gudang',
            'Materials',
            'Jumlah Pekerja',
            'Biaya per Pekerja',
            'Total Biaya Tenaga Kerja',
            'Biaya Operasional',
            'Total Biaya Produksi',
            'HPP per Unit',
            'Catatan',
            'Dibuat Tanggal',
            'Status Terakhir Update'
        ];
    }

    public function map($production): array
    {
        $realizedQty = $production->realizations->sum('quantity_produced');
        $progress = $production->target_quantity > 0 ? ($realizedQty / $production->target_quantity) * 100 : 0;
        
        // Calculate costs
        $laborCost = $production->laborCosts->sum('total_cost');
        $operationalCost = $production->operationalCosts->sum('amount');
        $totalCost = $laborCost + $operationalCost;
        $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
        
        // Format materials
        $materials = $production->materials->map(function($material) {
            $name = $material->material_type === 'bahan' 
                ? ($material->material->nama_bahan ?? 'N/A')
                : ($material->material->nama_produk ?? 'N/A');
            return $name . ' (' . number_format($material->quantity_required, 2) . ' ' . $material->unit . ')';
        })->join('; ');
        
        // Labor costs details
        $laborDetails = $production->laborCosts->first();
        $workerCount = $laborDetails ? $laborDetails->worker_count : 0;
        $costPerWorker = $laborDetails ? $laborDetails->cost_per_worker : 0;
        
        // Operational costs summary
        $operationalDetails = $production->operationalCosts->map(function($cost) {
            return $cost->cost_type . ': Rp ' . number_format($cost->amount);
        })->join('; ');

        return [
            $production->production_code,
            $production->product->nama_produk ?? '-',
            $production->production_line,
            $production->target_quantity,
            $realizedQty,
            number_format($progress, 2),
            ucfirst($production->status),
            ucfirst($production->priority ?? 'normal'),
            $production->start_date ? date('d/m/Y', strtotime($production->start_date)) : '-',
            $production->end_date ? date('d/m/Y', strtotime($production->end_date)) : '-',
            $production->expiry_date ? date('d/m/Y', strtotime($production->expiry_date)) : '-',
            $production->warehouse_location ?? '-',
            $materials ?: '-',
            $workerCount,
            $costPerWorker > 0 ? 'Rp ' . number_format($costPerWorker) : '-',
            $laborCost > 0 ? 'Rp ' . number_format($laborCost) : '-',
            $operationalDetails ?: '-',
            $totalCost > 0 ? 'Rp ' . number_format($totalCost) : '-',
            $hppPerUnit > 0 ? 'Rp ' . number_format($hppPerUnit, 2) : '-',
            $production->notes ?? '-',
            date('d/m/Y H:i', strtotime($production->created_at)),
            date('d/m/Y H:i', strtotime($production->updated_at))
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // All cells border
            'A1:V' . ($this->productions->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]
        ];
    }

    public function title(): string
    {
        return 'Data Produksi - ' . $this->outlet->nama_outlet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Produksi
            'B' => 20, // Produk
            'C' => 12, // Lini
            'D' => 12, // Target
            'E' => 12, // Realisasi
            'F' => 10, // Progress
            'G' => 12, // Status
            'H' => 10, // Prioritas
            'I' => 12, // Tanggal Mulai
            'J' => 12, // Tanggal Selesai
            'K' => 12, // Tanggal Kadaluarsa
            'L' => 15, // Lokasi Gudang
            'M' => 30, // Materials
            'N' => 12, // Jumlah Pekerja
            'O' => 15, // Biaya per Pekerja
            'P' => 18, // Total Biaya Tenaga Kerja
            'Q' => 25, // Biaya Operasional
            'R' => 18, // Total Biaya Produksi
            'S' => 15, // HPP per Unit
            'T' => 20, // Catatan
            'U' => 15, // Dibuat Tanggal
            'V' => 15, // Status Terakhir Update
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add title and info at the top
                $sheet->insertNewRowBefore(1, 4);
                
                // Title
                $sheet->setCellValue('A1', 'LAPORAN DATA PRODUKSI');
                $sheet->mergeCells('A1:V1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
                
                // Outlet info
                $sheet->setCellValue('A2', 'Outlet: ' . $this->outlet->nama_outlet);
                $sheet->mergeCells('A2:V2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
                
                // Export info
                $exportInfo = 'Diekspor pada: ' . date('d F Y H:i:s');
                if ($this->request->filled('start_date') || $this->request->filled('end_date')) {
                    $exportInfo .= ' | Periode: ';
                    $exportInfo .= ($this->request->start_date ? date('d F Y', strtotime($this->request->start_date)) : 'Awal');
                    $exportInfo .= ' - ';
                    $exportInfo .= ($this->request->end_date ? date('d F Y', strtotime($this->request->end_date)) : 'Akhir');
                }
                
                $sheet->setCellValue('A3', $exportInfo);
                $sheet->mergeCells('A3:V3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);
                
                // Empty row
                $sheet->setCellValue('A4', '');
                
                // Auto-fit row heights
                foreach (range(1, $sheet->getHighestRow()) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
                
                // Freeze header row
                $sheet->freezePane('A6');
            }
        ];
    }
}