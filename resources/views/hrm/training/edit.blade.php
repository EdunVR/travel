@extends('app')

@section('title', 'Edit Pelatihan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Pelatihan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hrm.training.update', $training->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        @foreach($recruitments as $recruitment)
                            <option value="{{ $recruitment->id }}" {{ $training->recruitment_id == $recruitment->id ? 'selected' : '' }}>
                                {{ $recruitment->name }} - {{ $recruitment->position }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="training_name">Nama Pelatihan</label>
                    <input type="text" class="form-control" id="training_name" name="training_name" value="{{ $training->training_name }}" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $training->start_date }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $training->end_date }}" required>
                </div>
                <div class="form-group">
                    <label for="trainer">Pelatih</label>
                    <input type="text" class="form-control" id="trainer" name="trainer" value="{{ $training->trainer }}" required>
                </div>
                <div class="form-group">
                    <label for="location">Lokasi</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ $training->location }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $training->description }}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('hrm.training.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
