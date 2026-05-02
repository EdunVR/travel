<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>

@extends('app')

@section('title', 'Detail Payroll')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Payroll #{{ $payroll->id }}</h6>
            <div>
                <!-- <a href="{{ route('hrm.payroll.print', $payroll->id) }}" 
                   class="btn btn-success" target="_blank">
                    Cetak PDF
                </a> -->
                <a href="{{ url()->previous() }}" class="btn btn-danger">
                     Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Karyawan</th>
                            <td>{{ $payroll->employee->name }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ $payroll->employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>{{ $payroll->created_at->format('F Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Gaji Pokok</th>
                            <td>Rp {{ number_format($payroll->salary, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Total Jam Kerja</th>
                            <td>{{ $payroll->total_hours_worked }} jam</td>
                        </tr>
                        <tr>
                            <th>Total Gaji</th>
                            <td>Rp {{ number_format($payroll->salary + ($payroll->total_hours_worked * $payroll->hourly_rate), 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold">Tambahan Gaji</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $additionalSalaries = json_decode($payroll->additional_salary, true) ?? [];
                                $totalAdditional = 0;
                            @endphp
                            
                            @if(count($additionalSalaries) > 0)
                                <table class="table table-bordered">
                                    @foreach($additionalSalaries as $additional)
                                    <tr>
                                        <td>{{ $additional['description'] ?? 'Tambahan' }}</td>
                                        <td class="text-right">Rp {{ number_format($additional['amount'], 0) }}</td>
                                    </tr>
                                    @php $totalAdditional += $additional['amount']; @endphp
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td>Total Tambahan</td>
                                        <td class="text-right">Rp {{ number_format($totalAdditional, 0) }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted">Tidak ada tambahan gaji</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h6 class="m-0 font-weight-bold">Potongan Gaji</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $deductions = json_decode($payroll->deductions, true) ?? [];
                                $totalDeductions = 0;
                            @endphp
                            
                            @if(count($deductions) > 0)
                                <table class="table table-bordered">
                                    @foreach($deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction['description'] ?? 'Potongan' }}</td>
                                        <td class="text-right">Rp {{ number_format($deduction['amount'], 0) }}</td>
                                    </tr>
                                    @php $totalDeductions += $deduction['amount']; @endphp
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td>Total Potongan</td>
                                        <td class="text-right">Rp {{ number_format($totalDeductions, 0) }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted">Tidak ada potongan gaji</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-primary text-black">
                <div class="card-body text-center">
                    <h4 class="font-weight-bold mb-0">
                        Gaji Bersih: Rp {{ number_format(
                            $payroll->salary + 
                            ($payroll->total_hours_worked * $payroll->hourly_rate) + 
                            $totalAdditional - 
                            $totalDeductions
                        , 0) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
