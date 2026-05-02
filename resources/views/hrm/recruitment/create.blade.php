@extends('app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Pelamar</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="position">Posisi</label>
                    <input type="text" class="form-control" id="position" name="position" value="{{ old('position') }}" required>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department" value="{{ old('department') }}" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="menunggu" {{ old('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ old('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="{{ old('salary') }}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga Per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="jobdesk">Jobdesk</label>
                    <div id="jobdesk-container">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="jobdesk[]" placeholder="Masukkan jobdesk" value="{{ old('jobdesk.0') }}">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                            </div>
                        </div>
                        @if(old('jobdesk'))
                            @foreach(old('jobdesk') as $index => $job)
                                @if($index > 0)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="jobdesk[]" value="{{ $job }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addJobdesk()">Tambah Jobdesk</button>
                </div>
                <div class="form-group">
                    <label for="fingerprint_id">Fingerprint ID</label>
                    <input type="number" class="form-control" id="fingerprint_id" name="fingerprint_id" value="{{ old('fingerprint_id') }}">
                </div>
                <div class="form-group">
                    <label for="is_registered_fingerprint">Status Sidik Jari</label>
                    <select class="form-control" id="is_registered_fingerprint" name="is_registered_fingerprint">
                        <option value="0" {{ old('is_registered_fingerprint', 0) == 0 ? 'selected' : '' }}>Belum Terdaftar</option>
                        <option value="1" {{ old('is_registered_fingerprint') == 1 ? 'selected' : '' }}>Terdaftar</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('hrm.recruitment.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function addJobdesk() {
        const container = document.getElementById('jobdesk-container');
        const newInput = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="jobdesk[]" placeholder="Masukkan jobdesk">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newInput);
    }

    function removeJobdesk(button) {
        button.closest('.input-group').remove();
    }
</script>
@endpush
@endsection
