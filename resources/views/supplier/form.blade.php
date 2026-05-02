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
                    <label for="nama" class="col-md-2 col-md-offset-1 control-label">Nama</label>
                    <div class="col-md-9">
                        <input type="text" name="nama" id="nama" class="form-control" required autofocus>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                @if($outlets->count() > 1)
                <div class="form-group row">
                        <label for="id_outlet" class="col-md-2 col-md-offset-1 control-label">Outlet</label>
                        <div class="col-md-9">
                            <select name="id_outlet" id="id_outlet" class="form-control" required>
                                <option value="">Pilih Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                @endif
                <div class="form-group row">
                    <label for="telepon" class="col-md-2 col-md-offset-1 control-label">Telepon</label>
                    <div class="col-md-9">
                        <input type="text" name="telepon" id="telepon" class="form-control" required>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="alamat" class="col-md-2 col-md-offset-1 control-label">Alamat</label>
                    <div class="col-md-9">
                        <input type="text" name="alamat" id="alamat" class="form-control">
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-md-2 col-md-offset-1 control-label">Email</label>
                    <div class="col-md-9">
                        <input type="email" name="email" id="email" class="form-control">
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <hr>
                <h4 class="text-center"><strong>Informasi Pembayaran</strong></h4>
                <div class="form-group row">
                    <label for="bank" class="col-md-2 col-md-offset-1 control-label">Bank</label>
                    <div class="col-md-9">
                        <input type="text" name="bank" id="bank" class="form-control" placeholder="Contoh: BCA, Mandiri, BNI">
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="no_rekening" class="col-md-2 col-md-offset-1 control-label">No. Rekening</label>
                    <div class="col-md-9">
                        <input type="text" name="no_rekening" id="no_rekening" class="form-control" placeholder="Nomor rekening bank">
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="atas_nama" class="col-md-2 col-md-offset-1 control-label">Atas Nama</label>
                    <div class="col-md-9">
                        <input type="text" name="atas_nama" id="atas_nama" class="form-control" placeholder="Nama pemilik rekening">
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i>Simpan</button>
                <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-close"></i>Batal</button>
            </div>
        </div>
    </div>
</div>
