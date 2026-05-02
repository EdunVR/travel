<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h1 class="modal-title fs-5"></h1>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="nama_satuan" class="col-md-2 col-md-offset-1 control-label">Satuan</label>
                    <div class="col-md-9">
                        <input type="text" name="nama_satuan" id="nama_satuan" class="form-control" required autofocus>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-flat btn-primary">Simpan</button>
                <button type="button" class="btn btn-sm btn-flat btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
