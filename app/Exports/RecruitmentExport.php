<?php

namespace App\Exports;

use App\Models\Recruitment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $statusFilter;
    protected $departmentFilter;
    protected $outletFilter;
    protected $userOutletIds;

    public function __construct($statusFilter = 'all', $departmentFilter = 'all', $outletFilter = 'all', $userOutletIds = [])
    {
        $this->statusFilter = $statusFilter;
        $this->departmentFilter = $departmentFilter;
        $this->outletFilter = $outletFilter;
        $this->userOutletIds = $userOutletIds;
    }

    public function collection()
    {
        $query = Recruitment::with('outlet');

        // Apply user outlet access
        if (!empty($this->userOutletIds)) {
            $query->whereIn('outlet_id', $this->userOutletIds);
        }

        if ($this->outletFilter !== 'all') {
            $query->where('outlet_id', $this->outletFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->departmentFilter !== 'all') {
            $query->where('department', $this->departmentFilter);
        }

        return $query->orderBy('name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Outlet',
            'Nama',
            'Posisi',
            'Departemen',
            'Status',
            'Telepon',
            'Email',
            'Gaji',
            'Tarif Per Jam',
            'Tanggal Bergabung',
            'ID Fingerprint',
        ];
    }

    public function map($employee): array
    {
        static $no = 0;
        $no++;

        $statusLabels = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'resigned' => 'Resign'
        ];

        return [
            $no,
            $employee->outlet ? $employee->outlet->nama_outlet : '-',
            $employee->name,
            $employee->position,
            $employee->department ?? '-',
            $statusLabels[$employee->status] ?? $employee->status,
            $employee->phone ?? '-',
            $employee->email ?? '-',
            $employee->salary ?? 0,
            $employee->hourly_rate ?? 0,
            $employee->join_date ? date('d/m/Y', strtotime($employee->join_date)) : '-',
            $employee->fingerprint_id ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
