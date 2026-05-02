@extends('app')

@section('title', isset($subClass) ? 'Edit Subclass' : 'Tambah Subclass')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i data-feather="layers"></i> {{ isset($subClass) ? 'Edit' : 'Tambah' }} Subclass
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" 
                  action="{{ isset($subClass) ? route('financial.book.update_sub_class', $subClass->id) : route('financial.book.store_sub_class') }}">
                @csrf
                @if(isset($subClass))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="accounting_book_id">Buku Akuntansi <span class="text-danger">*</span></label>
                    <select class="form-control" id="accounting_book_id" name="accounting_book_id" required>
                        <option value="">- Pilih Buku -</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" 
                                {{ (isset($subClass) && $subClass->accounting_book_id == $book->id) ? 'selected' : '' }}>
                                {{ $book->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="code">Kode Subclass <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" 
                           value="{{ $subClass->code ?? old('code') }}" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="name">Nama Subclass <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ $subClass->name ?? old('name') }}" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $subClass->description ?? old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('financial.book.sub_classes') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
