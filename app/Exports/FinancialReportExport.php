<?php

namespace App\Exports;

use App\Models\TravelPackage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TravelPackage::with(['jamaahBookings.payments', 'hppCalculation']);

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('departure_date', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['package_type'])) {
            $query->where('package_type', $this->filters['package_type']);
        }

        if (isset($this->filters['id_outlet'])) {
            $query->where('id_outlet', $this->filters['id_outlet']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode Paket',
            'Nama Paket',
            'Tipe Paket',
            'Tanggal Berangkat',
            'Jumlah Jamaah',
            'HPP per Orang (Rp)',
            'Harga per Orang (Rp)',
            'Total Revenue (Rp)',
            'Total Costs (Rp)',
            'Total Profit (Rp)',
            'Profit Margin (%)'
        ];
    }

    public function map($package): array
    {
        $bookings = $package->jamaahBookings;
        $revenue = $bookings->sum('total_price');
        $costs = $package->hpp * $bookings->count();
        $profit = $revenue - $costs;
        $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            $package->package_code,
            $package->package_name,
            strtoupper($package->package_type),
            $package->departure_date->format('Y-m-d'),
            $bookings->count(),
            $package->hpp,
            $package->price,
            $revenue,
            $costs,
            $profit,
            round($profitMargin, 2)
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
