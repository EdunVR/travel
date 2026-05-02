<?php

namespace App\Exports;

use App\Models\TravelPackage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperationalReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TravelPackage::with(['workflowHistory']);

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['id_outlet'])) {
            $query->where('id_outlet', $this->filters['id_outlet']);
        }

        $packages = $query->get();

        $stageData = [];
        
        foreach ($packages as $package) {
            $history = $package->workflowHistory->sortBy('transitioned_at');
            
            $previousTransition = null;
            foreach ($history as $transition) {
                if ($previousTransition) {
                    $stage = $previousTransition->to_stage;
                    $duration = $transition->transitioned_at->diffInHours($previousTransition->transitioned_at);
                    
                    if (!isset($stageData[$stage])) {
                        $stageData[$stage] = [
                            'stage_name' => $stage,
                            'total_duration' => 0,
                            'count' => 0
                        ];
                    }
                    
                    $stageData[$stage]['total_duration'] += $duration;
                    $stageData[$stage]['count']++;
                }
                $previousTransition = $transition;
            }
        }

        return collect($stageData)->map(function ($data) {
            return [
                'stage_name' => $data['stage_name'],
                'average_duration_hours' => $data['count'] > 0 ? round($data['total_duration'] / $data['count'], 2) : 0,
                'package_count' => $data['count']
            ];
        })->values();
    }

    public function headings(): array
    {
        return [
            'Workflow Stage',
            'Rata-rata Durasi (Jam)',
            'Jumlah Paket'
        ];
    }

    public function title(): string
    {
        return 'Laporan Operasional';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
