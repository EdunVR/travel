<?php

namespace App\Exports;

use App\Models\WorkflowTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamPerformanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = WorkflowTask::with(['team', 'workflowStage']);

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['team_code'])) {
            $query->where('assigned_to_team', $this->filters['team_code']);
        }

        $tasks = $query->get();

        return $tasks->groupBy('assigned_to_team')->map(function ($teamTasks, $teamCode) {
            $completed = $teamTasks->where('status', 'completed')->count();
            $total = $teamTasks->count();
            $overdue = $teamTasks->filter(function ($task) {
                return $task->isOverdue();
            })->count();

            $completedTasks = $teamTasks->where('status', 'completed');
            $avgCompletionTime = 0;
            if ($completedTasks->count() > 0) {
                $totalTime = $completedTasks->sum(function ($task) {
                    return $task->completed_at->diffInHours($task->created_at);
                });
                $avgCompletionTime = $totalTime / $completedTasks->count();
            }

            return [
                'team_name' => $teamTasks->first()->team->team_name ?? $teamCode,
                'total_tasks' => $total,
                'completed_tasks' => $completed,
                'pending_tasks' => $teamTasks->where('status', 'pending')->count(),
                'in_progress_tasks' => $teamTasks->where('status', 'in_progress')->count(),
                'overdue_tasks' => $overdue,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                'average_completion_hours' => round($avgCompletionTime, 2)
            ];
        })->values();
    }

    public function headings(): array
    {
        return [
            'Nama Tim',
            'Total Tugas',
            'Tugas Selesai',
            'Tugas Pending',
            'Tugas In Progress',
            'Tugas Terlambat',
            'Tingkat Penyelesaian (%)',
            'Rata-rata Waktu Penyelesaian (Jam)'
        ];
    }

    public function title(): string
    {
        return 'Kinerja Tim';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
