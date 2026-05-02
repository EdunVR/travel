<div class="modal fade" id="modal-form-hpp" tabindex="-1" aria-labelledby="modal-form-hpp" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post" class="form-horizontal">
            @csrf
            <input type="hidden" name="_method" id="_method" value=""> <!-- Biarkan kosong, diatur oleh JavaScript -->
            <input type="hidden" name="id_hpp" id="id_hpp">
            <input type="hidden" name="id_produk" id="id_produk">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h1 class="modal-title fs-5"></h1>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="hpp" class="col-md-2 col-md-offset-1 control-label">HPP</label>
                        <div class="col-md-9">
                            <input type="text" name="hpp" id="hpp" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="stok" class="col-md-2 col-md-offset-1 control-label">Stok</label>
                        <div class="col-md-9">
                            <input type="text" name="stok" id="stok" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i>Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-close"></i>Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
