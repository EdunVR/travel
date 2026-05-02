@extends('app')

@section('title', 'Edit Absensi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Absensi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hrm.attendance.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        @foreach($recruitments as $recruitment)
                            <option value="{{ $recruitment->id }}" {{ $attendance->recruitment_id == $recruitment->id ? 'selected' : '' }}>
                                {{ $recruitment->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $attendance->date }}" required>
                </div>
                <div class="form-group">
                    <label for="clock_in">Jam Masuk</label>
                    <input type="time" class="form-control" id="clock_in" name="clock_in" value="{{ $attendance->clock_in }}" required>
                </div>
                <div class="form-group">
                    <label for="clock_out">Jam Keluar</label>
                    <input type="time" class="form-control" id="clock_out" name="clock_out" value="{{ $attendance->clock_out }}">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('hrm.attendance.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
