<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form method="post" class="form-horizontal">
                    @csrf
                    @method('post')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama" class="col-lg-4 control-label">Nama Agen</label>
                                <div class="col-lg-8">
                                    <input type="text" name="nama" id="nama" class="form-control" required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="telepon" class="col-lg-4 control-label">Telepon</label>
                                <div class="col-lg-8">
                                    <input type="text" name="telepon" id="telepon" class="form-control" required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_outlet" class="col-lg-4 control-label">Outlet</label>
                                <div class="col-lg-8">
                                    <select name="id_outlet" id="id_outlet" class="form-control" required>
                                        <option value="">Pilih Outlet</option>
                                        @foreach ($outlets as $outlet)
                                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="alamat" class="col-lg-4 control-label">Alamat</label>
                                <div class="col-lg-8">
                                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="latitude" class="col-lg-4 control-label">Latitude</label>
                                <div class="col-lg-8">
                                    <input type="text" name="latitude" id="latitude" class="form-control">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="longitude" class="col-lg-4 control-label">Longitude</label>
                                <div class="col-lg-8">
                                    <input type="text" name="longitude" id="longitude" class="form-control">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Pilih Lokasi pada Peta:</label>
                        <div id="map" style="height: 300px; width: 100%;">
                            <div class="text-center" style="padding: 100px 0;">
                                <i class="fa fa-spinner fa-spin"></i> Loading map...
                            </div>
                        </div>
                        <small class="text-muted">Klik pada peta untuk menandai lokasi (Map akan load ketika modal dibuka)</small>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
