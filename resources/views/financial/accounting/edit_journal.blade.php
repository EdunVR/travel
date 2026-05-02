@extends('app')

@section('title', 'Edit Jurnal')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Jurnal #{{ $journal->reference }}</h6>
            <a href="{{ route('financial.accounting.index') }}" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form id="journalForm" method="POST" action="{{ route('financial.journals.update_journal', $journal->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="date" class="form-control" 
                                value="{{ $journal->date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="description" class="form-control" 
                                value="{{ $journal->description }}" required>
                        </div>
                    </div>
                </div>

                <div class="journal-entries">
                    @foreach($journal->entries as $index => $entry)
                    <div class="entry row mb-3">
                        <div class="col-md-4">
                            <select name="entries[{{ $index }}][account_id]" class="form-control account-select" required>
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" 
                                    {{ $entry->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="entries[{{ $index }}][debit]" 
                                class="form-control debit" placeholder="Debit" min="0" step="0.01"
                                value="{{ $entry->debit }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="entries[{{ $index }}][credit]" 
                                class="form-control credit" placeholder="Credit" min="0" step="0.01"
                                value="{{ $entry->credit }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="entries[{{ $index }}][memo]" 
                                class="form-control" placeholder="Memo"
                                value="{{ $entry->memo }}">
                        </div>
                        <div class="col-md-1">
                            @if($index > 1)
                            <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="addEntry" class="btn btn-sm btn-primary">
                            Tambah Entri
                        </button>
                        <button type="submit" class="btn btn-success float-right">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let entryCount = {{ count($journal->entries) }};
    
    // Add journal entry
    $('#addEntry').click(function() {
        const newEntry = $(`<div class="entry row mb-3">
            <div class="col-md-4">
                <select name="entries[${entryCount}][account_id]" class="form-control account-select" required>
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="entries[${entryCount}][debit]" class="form-control debit" placeholder="Debit" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <input type="number" name="entries[${entryCount}][credit]" class="form-control credit" placeholder="Credit" min="0" step="0.01">
            </div>
            <div class="col-md-3">
                <input type="text" name="entries[${entryCount}][memo]" class="form-control" placeholder="Memo">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
            </div>
        </div>`);
        
        $('.journal-entries').append(newEntry);
        entryCount++;
    });

    // Remove journal entry
    $(document).on('click', '.remove-entry', function() {
        if($('.entry').length > 2) {
            $(this).closest('.entry').remove();
        } else {
            alert('Minimal harus ada 2 entri jurnal');
        }
    });
});
</script>
@endpush
