<div class="modal fade" id="modal-due-date" tabindex="-1" role="dialog" aria-labelledby="modal-due-dateLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-due-dateLabel">Pilih Tanggal Jatuh Tempo</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="tanggal_tempo">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control" id="tanggal_tempo" name="tanggal_tempo" 
                           value="{{ date('Y-m-d', strtotime('+1 week')) }}" min="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-due-date">Simpan</button>
            </div>
        </div>
    </div>
</div>
