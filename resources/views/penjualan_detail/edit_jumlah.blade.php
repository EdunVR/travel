<!-- Modal Edit Jumlah -->
<div class="modal fade" id="modal-edit-jumlah" tabindex="-1" role="dialog" aria-labelledby="modal-edit-jumlah">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Jumlah Produk</h4>
            </div>
            <div class="modal-body">
                <form id="form-edit-jumlah">
                    @csrf
                    <input type="hidden" id="edit_id_penjualan_detail" name="id_penjualan_detail">
                    <div class="form-group">
                        <label for="edit_jumlah">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="1" placeholder="Masukkan jumlah">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanEditJumlah()">Simpan</button>
            </div>
        </div>
    </div>
</div>
