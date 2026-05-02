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
                @if(isset($recruitment))
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="name">Nama Pelamar</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $recruitment->name ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label for="position">Posisi</label>
                    <input type="text" class="form-control" id="position" name="position" value="{{ $recruitment->position ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department" value="{{ $recruitment->department ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="menunggu" {{ isset($recruitment) && $recruitment->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ isset($recruitment) && $recruitment->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ isset($recruitment) && $recruitment->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="{{ $recruitment->salary }}" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga Per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $recruitment->hourly_rate }}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="jobdesk">Jobdesk</label>
                    <div id="jobdesk-container">
                        @if(isset($recruitment) && $recruitment->jobdesk)
                            @foreach(json_decode($recruitment->jobdesk) as $job)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="jobdesk[]" value="{{ $job }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="jobdesk[]" placeholder="Masukkan jobdesk">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addJobdesk()">Tambah Jobdesk</button>
                </div>
                <div class="form-group">
                    <label for="fingerprint_id">Fingerprint ID</label>
                    <input type="number" class="form-control" id="fingerprint_id" name="fingerprint_id" value="{{ $recruitment->fingerprint_id ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="is_registered_fingerprint">Status Sidik Jari</label>
                    <select class="form-control" id="is_registered_fingerprint" name="is_registered_fingerprint">
                        <option value="0" {{ isset($recruitment) && !$recruitment->is_registered_fingerprint ? 'selected' : '' }}>Belum Terdaftar</option>
                        <option value="1" {{ isset($recruitment) && $recruitment->is_registered_fingerprint ? 'selected' : '' }}>Terdaftar</option>
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
