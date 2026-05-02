@extends('app')

@section('title', 'Edit Kinerja Karyawan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Kinerja Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hrm.performance.update', $performance->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        @foreach($recruitments as $recruitment)
                            <option value="{{ $recruitment->id }}" {{ $performance->recruitment_id == $recruitment->id ? 'selected' : '' }}>
                                {{ $recruitment->name }} - {{ $recruitment->position }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="evaluation_date">Tanggal Penilaian</label>
                    <input type="date" class="form-control" id="evaluation_date" name="evaluation_date" value="{{ $performance->evaluation_date }}" required>
                </div>
                <div class="form-group">
                    <label for="criteria">Kriteria</label>
                    <input type="text" class="form-control" id="criteria" name="criteria" value="{{ $performance->criteria }}" required>
                </div>
                <div class="form-group">
                    <label for="score">Nilai (0 - 10)</label>
                    <input type="number" class="form-control" id="score" name="score" value="{{ $performance->score }}" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="remarks">Keterangan</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $performance->remarks }}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('hrm.performance.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
