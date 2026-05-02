@extends('app')

@section('title', 'Edit Rekening Investor')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Rekening</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('irp.investor.account.update', ['investor' => $investor->id, 'account' => $account->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="account_number">Nomor Rekening*</label>
                    <input type="text" class="form-control" id="account_number" name="account_number" 
                           value="{{ old('account_number', $account->account_number) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="bank_name">Nama Bank*</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" 
                           value="{{ old('bank_name', $account->bank_name) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="account_name">Atas Nama*</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" 
                           value="{{ old('account_name', $account->account_name) }}" required>
                </div>

                <div class="form-group">
                        <label for="date">Tanggal*</label>
                        <input type="date" class="form-control" id="date" name="date"
                               value="{{ old('date', $account->date) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="tempo">Jatuh Tempo</label>
                        <input type="date" class="form-control" id="tempo" name="tempo"
                               value="{{ old('tempo', $account->tempo) }}">
                    </div>
                
                <div class="form-group">
                    <label for="initial_balance">Modal Rekening*</label>
                    <input type="number" class="form-control" id="initial_balance" name="initial_balance" 
                           value="{{ old('initial_balance', $account->initial_balance) }}" required>
                </div>
                <div class="form-group">
                    <label for="saldo_tertahan">Saldo Tertahan*</label>
                    <input type="number" class="form-control" id="saldo_tertahan" name="saldo_tertahan" 
                           value="{{ old('saldo_tertahan', $account->saldo_tertahan) }}">
                </div>
                <div class="form-group">
                    <label for="profit_percentage">Persentase Bagi Hasil (%)*</label>
                    <input type="number" step="0.01" class="form-control" id="profit_percentage" name="profit_percentage" 
                           value="{{ old('profit_percentage', $account->profit_percentage) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status*</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $account->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('irp.investor.show', ['investor' => $investor->id, 'account' => $account->id]) }}" 
                   class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
