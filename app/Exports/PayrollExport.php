<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $outletFilter;
    protected $periodFilter;
    protected $statusFilter;
    protected $userOutletIds;

    public function __construct($outletFilter = 'all', $periodFilter = null, $statusFilter = 'all', $userOutletIds = [])
    {
        $this->outletFilter = $outletFilter;
        $this->periodFilter = $periodFilter;
        $this->statusFilter = $statusFilter;
        $this->userOutletIds = $userOutletIds;
    }

    public function collection()
    {
        $query = Payroll::with(['outlet', 'employee']);

        if (!empty($this->userOutletIds)) {
            $query->whereIn('outlet_id', $this->userOutletIds);
        }

        if ($this->outletFilter !== 'all') {
            $query->where('outlet_id', $this->outletFilter);
        }

        if ($this->periodFilter) {
            $query->where('period', $this->periodFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Outlet',
            'Nama Karyawan',
            'Posisi',
            'Periode',
            'Tgl Pembayaran',
            'Gaji Pokok',
            'Lembur',
            'Bonus',
            'Tunjangan',
            'Potongan',
            'Pajak',
            'Gaji Kotor',
            'Gaji Bersih',
            'Status',
        ];
    }

    public function map($payroll): array
    {
        static $no = 0;
        $no++;

        $statusLabels = [
            'draft' => 'Draft',
            'approved' => 'Approved',
            'paid' => 'Dibayar'
        ];

        return [
            $no,
            $payroll->outlet ? $payroll->outlet->nama_outlet : '-',
            $payroll->employee->name,
            $payroll->employee->position,
            $payroll->period,
            $payroll->payment_date->format('d/m/Y'),
            $payroll->basic_salary,
            $payroll->overtime_pay,
            $payroll->bonus,
            $payroll->allowance,
            $payroll->deduction + $payroll->late_penalty + $payroll->absent_penalty + $payroll->loan_deduction,
            $payroll->tax,
            $payroll->gross_salary,
            $payroll->net_salary,
            $statusLabels[$payroll->status] ?? $payroll->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
