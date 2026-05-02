@extends('app')

@section('title', 'Manajemen Penggajian & Benefit')

@push('css')
<link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<style>
    .total-info {
        font-size: 16px;
        font-weight: bold;
        padding: 8px 12px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        display: inline-block;
        margin-left: 10px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-draft {
        background-color: #ffc107;
        color: #000;
    }
    .status-final {
        background-color: #28a745;
        color: #fff;
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Manajemen Penggajian & Benefit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <form action="{{ route('hrm.payroll.index') }}" method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="month" class="mr-2">Pilih Bulan dan Tahun:</label>
                        <input type="month" name="month" id="month" class="form-control" value="{{ request('month') ?? date('Y-m') }}">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2 mb-2">Filter</button>
                </form>
                <a href="{{ route('hrm.payroll.create') }}" class="btn btn-primary btn-xs btn-flat"><i class="fa fa-plus"></i> Tambah Penggajian</a>
                <a id="exportPdf" href="{{ route('hrm.payroll.export_pdf', ['month' => request('month') ?? date('Y-m')]) }}" target="_blank" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-file-pdf-o"></i> Export PDF
                </a>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th> <!-- Kolom Tanggal -->
                        <th>Nama Karyawan</th>
                        <th>Posisi</th>
                        <th>Gaji Pokok</th>
                        <th>Tambahan dan Potongan Gaji</th>
                        <th>Harga Per Jam</th>
                        <th>Total Jam Kerja</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    @php
                        // Hitung total tambahan gaji
                        $totalAdditionalSalary = 0;
                        if ($payroll->additional_salary) {
                            $additionalSalaries = json_decode($payroll->additional_salary, true);
                            foreach ($additionalSalaries as $additional) {
                                $totalAdditionalSalary += $additional['amount'];
                            }
                        }

                        // Hitung total potongan gaji
                        $totalDeductions = 0;
                        if ($payroll->deductions) {
                            $deductions = json_decode($payroll->deductions, true);
                            foreach ($deductions as $deduction) {
                                $totalDeductions += $deduction['amount'];
                            }
                        }

                        // Hitung total gaji
                        $totalSalary = $payroll->salary + ($payroll->total_hours_worked * $payroll->hourly_rate) + $totalAdditionalSalary - $totalDeductions;

                        $statusClass = $payroll->benefits === 'final' ? 'status-final' : 'status-draft';
                        $statusText = $payroll->benefits === 'final' ? 'Final' : 'Draft';
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $payroll->created_at->format('d-m-Y') }}</td>
                        <td>{{ $payroll->employee->name }}</td>
                        <td>{{ $payroll->employee->position }}</td>
                        <td>{{ format_uang($payroll->salary) }}</td>
                        <td>
                            @if($payroll->additional_salary || $payroll->deductions)
                                <strong>Tambahan Gaji:</strong><br>
                                @if($payroll->additional_salary)
                                    @foreach(json_decode($payroll->additional_salary, true) as $additional)
                                        - {{ format_uang($additional['amount']) }} : {{ $additional['description'] ?? 'Tanpa Deskripsi' }}<br>
                                    @endforeach
                                @else
                                    Tidak ada tambahan gaji.<br>
                                @endif

                                <strong>Potongan Gaji:</strong><br>
                                @if($payroll->deductions)
                                    @foreach(json_decode($payroll->deductions, true) as $deduction)
                                        - {{ format_uang($deduction['amount']) }} : {{ $deduction['description'] ?? 'Tanpa Deskripsi' }}<br>
                                    @endforeach
                                @else
                                    Tidak ada potongan gaji.<br>
                                @endif
                            @else
                                Tidak ada tambahan atau potongan gaji.
                            @endif
                        </td>
                        <td>{{ format_uang($payroll->hourly_rate) }} / Jam</td>
                        <td>{{ $payroll->total_hours_worked }} Jam</td>
                        <td>{{ format_uang($totalSalary) }}</td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            @if($payroll->benefits !== 'final')
                                <a href="{{ route('hrm.payroll.edit', $payroll->id) }}" class="btn btn-warning btn-xs btn-flat"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-danger btn-xs btn-flat delete-payroll" data-id="{{ $payroll->id }}"><i class="fa fa-trash"></i></button>
                            @endif
                            
                            @if($payroll->benefits === 'final')
                                <a href="{{ route('hrm.payroll.print', ['id' => $payroll->id, 'month' => request('month') ?? date('Y-m')]) }}" class="btn btn-info btn-xs btn-flat"><i class="fa fa-print"></i> Cetak</a>
                            @else
                                <button class="btn btn-success btn-xs btn-flat finalize-payroll" data-id="{{ $payroll->id }}"><i class="fa fa-check"></i> Finalisasi</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(function () {
        $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
            bSort: false,
            bPaginate: false,
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }

    // SweetAlert for delete confirmation
        $(document).on('click', '.delete-payroll', function() {
            const payrollId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Data Payroll?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('hrm.payroll.destroy', ['payroll' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', payrollId),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Deleted!', 'Data payroll berhasil dihapus', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal menghapus', 'error');
                        }
                    });
                }
            });
        });

        // SweetAlert for finalize confirmation
        $(document).on('click', '.finalize-payroll', function() {
            const payrollId = $(this).data('id');
            
            Swal.fire({
                title: 'Finalisasi Payroll?',
                text: "Setelah difinalisasi, data tidak bisa diubah lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Finalisasi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('hrm.payroll.finalize', ['payroll' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', payrollId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Success!', 'Payroll berhasil difinalisasi', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal memfinalisasi', 'error');
                        }
                    });
                }
            });
        });
</script>
@endpush
