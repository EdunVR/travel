<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with('employee');

        if (!empty($this->filters['start_date'])) {
            $query->where('date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Karyawan',
            'Jabatan',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Jam Kerja (jam)',
            'Lembur (jam)',
            'Terlambat (menit)',
            'Keterangan'
        ];
    }

    public function map($attendance): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            \Carbon\Carbon::parse($attendance->date)->format('d/m/Y'),
            $attendance->employee->nama ?? '-',
            $attendance->employee->jabatan ?? '-',
            $attendance->check_in ?? '-',
            $attendance->check_out ?? '-',
            ucfirst($attendance->status),
            number_format($attendance->work_hours, 2),
            number_format($attendance->overtime_hours, 2),
            $attendance->late_minutes,
            $attendance->notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
