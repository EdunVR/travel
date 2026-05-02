<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Tambah Rekening Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('irp.investor.account.store', $investor->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="account_number">Nomor Rekening*</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" 
                            value="GTS-{{ time() }}" readonly required>
                            <small class="form-text text-muted">Nomor rekening otomatis dibuat sistem</small>
                    </div>
                    <div class="form-group">
                        <label for="bank_name">Nama Rekening*</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                    </div>
                    <div class="form-group">
                        <label for="account_name">Atas Nama*</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Tanggal*</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="tempo">Jatuh Tempo</label>
                        <input type="date" class="form-control" id="tempo" name="tempo">
                    </div>
                    <div class="form-group">
                        <label for="initial_balance">Modal Rekening*</label>
                        <input type="number" class="form-control" id="initial_balance" name="initial_balance" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="profit_percentage">Persentase Bagi Hasil (%)*</label>
                        <input type="number" step="0.01" class="form-control" id="profit_percentage" name="profit_percentage" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
