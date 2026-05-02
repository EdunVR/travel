<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    @method('post')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_sparepart">Kode Sparepart</label>
                                <input type="text" name="kode_sparepart" class="form-control" readonly style="background-color: #f8f9fa;">
                                <small class="text-muted">Kode akan digenerate otomatis</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_sparepart">Nama Sparepart</label>
                                <input type="text" name="nama_sparepart" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="merk">Merk <span class="text-danger">*</span></label>
                                <input type="text" name="merk" class="form-control" required>
                                <small class="text-muted">Wajib diisi untuk generate kode</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga">Harga <span class="text-danger">*</span></label>
                                <input type="text" name="harga" class="form-control" required onblur="formatCurrency(this)">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                         <div class="col-md-4">
                            <div class="form-group">
                                <label for="stok">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stok" class="form-control" required min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stok_minimum">Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <select name="satuan" class="form-control" required>
                                    <option value="pcs">Pcs</option>
                                    <option value="unit">Unit</option>
                                    <option value="set">Set</option>
                                    <option value="pack">Pack</option>
                                    <option value="roll">Roll</option>
                                    <option value="meter">Meter</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="spesifikasi">Spesifikasi</label>
                        <textarea name="spesifikasi" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
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
