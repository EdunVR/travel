<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recruitment;
use App\Models\Payroll;
use PDF;
use App\Models\Attendance;
use App\Services\JournalEntryService;
use App\Models\ChartOfAccount;
use Log;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari request
        $month = $request->input('month', date('Y-m')); // Default: bulan dan tahun saat ini
        $startDate = date('Y-m-01', strtotime($month)); // Tanggal awal bulan
        $endDate = date('Y-m-t', strtotime($month)); // Tanggal akhir bulan

        // Ambil data karyawan dengan status "diterima"
        $employees = Recruitment::where('status', 'diterima')->get();

        // Ambil data penggajian berdasarkan periode yang dipilih
        $payrolls = Payroll::whereBetween('created_at', [$startDate, $endDate])->get();

        // Hitung total jam kerja dari tabel attendances
        foreach ($payrolls as $payroll) {
            $totalHours = Attendance::where('recruitment_id', $payroll->recruitment_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('hours_worked');
            $payroll->total_hours_worked = $totalHours;
        }

        return view('hrm.payroll.index', compact('employees', 'payrolls', 'month'));
    }

    public function create()
    {
        // Ambil data karyawan dengan status "diterima"
        $employees = Recruitment::where('status', 'diterima')->get();
        return view('hrm.payroll.create', compact('employees'));
    }

    public function edit(Payroll $payroll)
    {
        // Ambil data karyawan dengan status "diterima"
        $employees = Recruitment::where('status', 'diterima')->get();
        return view('hrm.payroll.edit', compact('payroll', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'salary' => 'required|numeric',
            'benefits' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric',
            'additional_salary' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);

        // Cek apakah sudah ada data payroll untuk bulan ini
        $month = date('Y-m');
        $existingPayroll = Payroll::where('recruitment_id', $request->recruitment_id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->first();

        if ($existingPayroll) {
            return redirect()->route('hrm.payroll.index')->with('error', 'Data payroll untuk bulan ini sudah ada.');
        }

        // Simpan data penggajian
        $data = $request->all();
        if($request->additional_salary){
            $data['additional_salary'] = json_encode($request->additional_salary);
        } else {
            $data['additional_salary'] = null;
        }

        if($request->deductions){
            $data['deductions'] = json_encode($request->deductions);
        } else {
            $data['deductions'] = null;
        }

        Payroll::create($data);

        return redirect()->route('hrm.payroll.index')->with('success', 'Data penggajian berhasil ditambahkan.');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'salary' => 'required|numeric',
            'benefits' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric',
            'additional_salary' => 'nullable|array',
            'additional_salary_description' => 'nullable|array',
            'deductions' => 'nullable|array',
            'deductions_description' => 'nullable|array',
        ]);

        // Format data tambahan gaji dan potongan gaji
        $additionalSalaries = [];
        foreach ($request->additional_salary as $index => $amount) {
            $additionalSalaries[] = [
                'amount' => $amount,
                'description' => $request->additional_salary_description[$index],
            ];
        }

        $deductions = [];
        foreach ($request->deductions as $index => $amount) {
            $deductions[] = [
                'amount' => $amount,
                'description' => $request->deductions_description[$index],
            ];
        }

        // Update data penggajian
        $data = $request->only(['recruitment_id', 'salary', 'benefits', 'hourly_rate']);
        $data['additional_salary'] = json_encode($additionalSalaries);
        $data['deductions'] = json_encode($deductions);

        $payroll->update($data);
        return redirect()->route('hrm.payroll.index')->with('success', 'Data penggajian berhasil diperbarui.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('hrm.payroll.index')->with('success', 'Data penggajian berhasil dihapus.');
    }

    public function print($id, Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $startDate = date('Y-m-01', strtotime($month));
        $endDate = date('Y-m-t', strtotime($month));

        $payroll = Payroll::with(['attendances' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->where('id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->firstOrFail();

        if ($payroll->benefits !== 'final') {
            return redirect()->back()->with('error', 'Payroll must be finalized before printing.');
        }

        $totalHours = $payroll->attendances->sum('hours_worked');
        $payroll->total_hours_worked = $totalHours;

        // Hitung total tambahan gaji
        $totalAdditionalSalary = 0;
        $additionalSalaryData = json_decode($payroll->additional_salary, true);
        if (is_array($additionalSalaryData)) {
            foreach ($additionalSalaryData as $additional) {
                $totalAdditionalSalary += $additional['amount'] ?? 0;
            }
        }

        // Hitung total potongan
        $totalDeductions = 0;
        $deductionsData = json_decode($payroll->deductions, true);
        if (is_array($deductionsData)) {
            foreach ($deductionsData as $deduction) {
                $totalDeductions += $deduction['amount'] ?? 0;
            }
        }

        // Hitung total gaji
        $totalSalary = $payroll->salary 
                    + ($payroll->hourly_rate * $payroll->total_hours_worked)
                    + $totalAdditionalSalary 
                    - $totalDeductions;
        

        return view('hrm.payroll.print', compact('payroll', 'month', 'totalSalary', 'totalAdditionalSalary', 'totalDeductions'));
    }

    public function exportPdf(Request $request)
    {
        // Ambil bulan dan tahun dari request
        $month = $request->input('month', date('Y-m')); // Default: bulan dan tahun saat ini
        $startDate = date('Y-m-01', strtotime($month)); // Tanggal awal bulan
        $endDate = date('Y-m-t', strtotime($month)); // Tanggal akhir bulan

        // Ambil data penggajian berdasarkan periode yang dipilih
        $payrolls = Payroll::whereBetween('created_at', [$startDate, $endDate])->get();

        // Hitung total jam kerja dari tabel attendances
        foreach ($payrolls as $payroll) {
            $totalHours = Attendance::where('recruitment_id', $payroll->recruitment_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('hours_worked');
            $payroll->total_hours_worked = $totalHours;
        }

        // Generate PDF
        $pdf = Pdf::loadView('hrm.payroll.export_pdf', compact('payrolls', 'month'));
        return $pdf->download('laporan_penggajian_' . $month . '.pdf');
    }

    public function finalize(Payroll $payroll)
    {
        $payroll->update([
            'benefits' => 'final',
        ]);

        // Pindahkan logika journal entry dari print ke sini
        $journalService = new JournalEntryService();

        $salaryExpenseAccount = ChartOfAccount::where('code', '5301')->first(); // Salary expense
        $taxPayableAccount = ChartOfAccount::where('code', '2102')->first(); // Tax payable
        $cashAccount = ChartOfAccount::where('code', '1101')->first(); // Cash account

        // Hitung total gaji
        $month = $payroll->created_at->format('Y-m');
        $startDate = date('Y-m-01', strtotime($month));
        $endDate = date('Y-m-t', strtotime($month));

        $totalHours = Attendance::where('recruitment_id', $payroll->recruitment_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('hours_worked');

        // Hitung total tambahan gaji
        $totalAdditionalSalary = 0;
        $additionalSalaryData = json_decode($payroll->additional_salary, true);
        if (is_array($additionalSalaryData)) {
            foreach ($additionalSalaryData as $additional) {
                $totalAdditionalSalary += $additional['amount'] ?? 0;
            }
        }

        // Hitung total potongan
        $totalDeductions = 0;
        $deductionsData = json_decode($payroll->deductions, true);
        if (is_array($deductionsData)) {
            foreach ($deductionsData as $deduction) {
                $totalDeductions += $deduction['amount'] ?? 0;
            }
        }

        // Hitung total gaji
        $totalSalary = $payroll->salary 
                    + ($payroll->hourly_rate * $totalHours)
                    + $totalAdditionalSalary 
                    - $totalDeductions;

        // Buat journal entries
        $entries = [
            [
                'account_id' => $salaryExpenseAccount->id,
                'debit' => $totalSalary,
                'memo' => 'Penggajian untuk '.$payroll->employee->name
            ],
            [
                'account_id' => $cashAccount->id,
                'credit' => $totalSalary,
                'memo' => 'Pembayaran gaji untuk '.$payroll->employee->name
            ]
        ];

        $journalService->createAutomaticJournal(
            'payroll',
            $payroll->id,
            $payroll->created_at,
            'Penggajian '.$payroll->employee->name,
            $entries
        );
        
        return redirect()->route('hrm.payroll.index')
            ->with('success', 'Payroll telah difinalisasi dan jurnal akuntansi telah dibuat.');
    }

    public function showDetailLedger($id, Request $request)
    {
        // Dapatkan payroll dengan relasi yang diperlukan
        $payroll = Payroll::with(['employee', 'attendances'])
            ->where('id', $id)
            ->firstOrFail();

        // Validasi jika payroll belum final
        if ($payroll->benefits !== 'final') {
            return redirect()->back()->with('error', 'Payroll must be finalized before viewing details.');
        }

        // Gunakan bulan dari created_at payroll, bukan dari input request
        $payrollMonth = $payroll->created_at->format('Y-m');
        $startDate = $payroll->created_at->startOfMonth()->format('Y-m-d');
        $endDate = $payroll->created_at->endOfMonth()->format('Y-m-d');

        // \Log::info('Payroll details:', [
        //     'payroll_id' => $payroll->id,
        //     'payroll_month' => $payrollMonth,
        //     'created_at' => $payroll->created_at,
        //     'start_date' => $startDate,
        //     'end_date' => $endDate
        // ]);

        // Filter attendance berdasarkan periode payroll
        $payroll->attendances = $payroll->attendances->filter(function($attendance) use ($startDate, $endDate) {
            return $attendance->created_at >= $startDate && 
                $attendance->created_at <= $endDate;
        });

        // Hitung total jam kerja
        $totalHours = $payroll->attendances->sum('hours_worked');
        $payroll->total_hours_worked = $totalHours;

        // \Log::info('Total hours worked for payroll ID '.$payroll->id.': '.$totalHours);

        // Handle additional_salary
        $totalAdditionalSalary = 0;
        $additionalSalaries = [];
        
        if (!empty($payroll->additional_salary)) {
            try {
                $additionalSalaries = json_decode($payroll->additional_salary, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($additionalSalaries)) {
                    foreach ($additionalSalaries as $additional) {
                        $totalAdditionalSalary += $additional['amount'] ?? 0;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to decode additional_salary for payroll ID: '.$payroll->id, [
                    'error' => $e->getMessage(),
                    'additional_salary' => $payroll->additional_salary
                ]);
                $additionalSalaries = [];
            }
        }

        // Handle deductions
        $totalDeductions = 0;
        $deductions = [];
        
        if (!empty($payroll->deductions)) {
            try {
                $deductions = json_decode($payroll->deductions, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($deductions)) {
                    foreach ($deductions as $deduction) {
                        $totalDeductions += $deduction['amount'] ?? 0;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to decode deductions for payroll ID: '.$payroll->id, [
                    'error' => $e->getMessage(),
                    'deductions' => $payroll->deductions
                ]);
                $deductions = [];
            }
        }

        // Hitung total gaji
        $totalSalary = $payroll->salary 
                    + ($payroll->hourly_rate * $payroll->total_hours_worked)
                    + $totalAdditionalSalary 
                    - $totalDeductions;

        return view('hrm.payroll.detail_ledger', compact(
            'payroll',
            'payrollMonth',
            'totalSalary',
            'totalAdditionalSalary',
            'totalDeductions',
            'additionalSalaries',
            'deductions'
        ));
    }
}