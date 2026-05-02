<div class="modal fade" id="addInvestmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Investasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="investmentForm" method="POST" enctype="multipart/form-data"
                action="{{ route('irp.investor.account.investment.store', ['investor' => $investor->id, 'account' => '__ACCOUNT_ID__']) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="account_id" id="modalAccountId">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Jenis Transaksi*</label>
                        <select name="type" class="form-control" required>
                            <option value="investment">Investasi</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Pencairan</option>
                            <option value="penarikan">Penarikan Modal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="amount" class="form-control" required step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Dokumen (Optional)</label>
                        <input type="file" name="document" class="form-control-file">
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#addInvestmentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var accountId = button.data('account-id');
        var modal = $(this);
        
        // Set account_id di form
        modal.find('#modalAccountId').val(accountId);
        
        // Update form action dengan URL yang benar
        var formAction = modal.find('form').attr('action');
        formAction = formAction.replace('__ACCOUNT_ID__', accountId);
        modal.find('form').attr('action', formAction);
    });
});
</script>
@endpush
