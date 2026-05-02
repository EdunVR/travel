@extends('app')

@section('title', 'Edit Penggajian')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Penggajian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hrm.payroll.update', $payroll->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $payroll->recruitment_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->position }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="{{ $payroll->salary }}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $payroll->hourly_rate }}" step="0.01">
                </div>
                <div class="form-group">
                    <label for="additional_salary">Tambahan Gaji</label>
                    <div id="additional-salary-container">
                        @if($payroll->additional_salary)
                            @php
                                $additionalSalaries = json_decode($payroll->additional_salary, true); // Decode sebagai array
                            @endphp
                            @if(is_array($additionalSalaries)) <!-- Pastikan data adalah array -->
                                @foreach($additionalSalaries as $additional)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="additional_salary[]" value="{{ $additional['amount'] ?? '' }}" placeholder="Jumlah">
                                        <input type="text" class="form-control" name="additional_salary_description[]" value="{{ $additional['description'] ?? '' }}" placeholder="Deskripsi">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" onclick="removeAdditionalSalary(this)">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addAdditionalSalary()">Tambah Tambahan Gaji</button>
                </div>

                <div class="form-group">
                    <label for="deductions">Potongan Gaji</label>
                    <div id="deductions-container">
                        @if($payroll->deductions)
                            @php
                                $deductions = json_decode($payroll->deductions, true); // Decode sebagai array
                            @endphp
                            @if(is_array($deductions)) <!-- Pastikan data adalah array -->
                                @foreach($deductions as $deduction)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="deductions[]" value="{{ $deduction['amount'] ?? '' }}" placeholder="Jumlah">
                                        <input type="text" class="form-control" name="deductions_description[]" value="{{ $deduction['description'] ?? '' }}" placeholder="Deskripsi">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" onclick="removeDeduction(this)">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addDeduction()">Tambah Potongan Gaji</button>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('hrm.payroll.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function addAdditionalSalary() {
        const container = document.getElementById('additional-salary-container');
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control" name="additional_salary[]" placeholder="Jumlah">
            <input type="text" class="form-control" name="additional_salary_description[]" placeholder="Deskripsi">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger" onclick="removeAdditionalSalary(this)">Hapus</button>
            </div>
        `;
        container.appendChild(div);
    }

    function removeAdditionalSalary(button) {
        button.closest('.input-group').remove();
    }

    function addDeduction() {
        const container = document.getElementById('deductions-container');
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control" name="deductions[]" placeholder="Jumlah">
            <input type="text" class="form-control" name="deductions_description[]" placeholder="Deskripsi">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger" onclick="removeDeduction(this)">Hapus</button>
            </div>
        `;
        container.appendChild(div);
    }

    function removeDeduction(button) {
        button.closest('.input-group').remove();
    }
</script>
@endpush
