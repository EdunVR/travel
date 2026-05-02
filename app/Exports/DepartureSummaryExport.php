<?php

namespace App\Exports;

use App\Models\Keberangkatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartureSummaryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Keberangkatan::with(['travelPackage', 'jamaahBookings.payments']);

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('departure_date', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['id_outlet'])) {
            $query->where('id_outlet', $this->filters['id_outlet']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode Keberangkatan',
            'Nama Keberangkatan',
            'Tanggal Berangkat',
            'Jumlah Jamaah',
            'Revenue (Rp)',
            'Expenses (Rp)',
            'Profit (Rp)',
            'Profit Margin (%)'
        ];
    }

    public function map($keberangkatan): array
    {
        $bookings = $keberangkatan->jamaahBookings;
        $revenue = $bookings->sum('total_price');
        $expenses = $keberangkatan->travelPackage->hpp * $bookings->count();
        $profit = $revenue - $expenses;
        $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            $keberangkatan->keberangkatan_code,
            $keberangkatan->keberangkatan_name,
            $keberangkatan->departure_date->format('Y-m-d'),
            $bookings->count(),
            $revenue,
            $expenses,
            $profit,
            round($profitMargin, 2)
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Keberangkatan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
