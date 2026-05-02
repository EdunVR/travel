@extends('app')

@section('title', 'Tambah Penggajian')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Penggajian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hrm.payroll.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->position }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" step="0.01">
                </div>
                <div class="form-group">
                    <label for="additional_salary">Tambahan Gaji</label>
                    <div id="additional-salary-container">
                        <!-- Input tambahan gaji akan ditambahkan di sini -->
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addAdditionalSalary()">Tambah Tambahan Gaji</button>
                </div>
                <div class="form-group">
                    <label for="deductions">Potongan Gaji</label>
                    <div id="deductions-container">
                        <!-- Input potongan gaji akan ditambahkan di sini -->
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addDeduction()">Tambah Potongan Gaji</button>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('hrm.payroll.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk menambahkan input tambahan gaji
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

    // Fungsi untuk menghapus input tambahan gaji
    function removeAdditionalSalary(button) {
        button.closest('.input-group').remove();
    }

    // Fungsi untuk menambahkan input potongan gaji
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

    // Fungsi untuk menghapus input potongan gaji
    function removeDeduction(button) {
        button.closest('.input-group').remove();
    }
</script>
@endpush
