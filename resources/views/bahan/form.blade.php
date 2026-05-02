<!-- bahan/form.blade.php -->

<div class="modal fade" id="modal-formx" tabindex="-1" aria-labelledby="modal-formx" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post" class="form-horizontal">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST"> <!-- Default method POST -->

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h1 class="modal-title fs-5"></h1>
                </div>
                <div class="modal-body">
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
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label for="nama_bahan" class="col-md-2 col-md-offset-1 control-label">Nama Bahan</label>
                        <div class="col-md-9">
                            <input type="text" name="nama_bahan" id="nama_bahan" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="merk" class="col-md-2 col-md-offset-1 control-label">Merk</label>
                        <div class="col-md-9">
                            <input type="text" name="merk" id="merk" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_satuan" class="col-md-2 col-md-offset-1 control-label">Satuan</label>
                        <div class="col-md-9">
                            <select name="id_satuan" id="id_satuan" class="form-control" required>
                                <option value="">Pilih Satuan</option>
                                @foreach ($satuan as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-flat btn-primary">Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
