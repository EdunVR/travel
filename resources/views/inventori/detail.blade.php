<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="nama-barang-title"></h4>
            </div>
            <div class="modal-body">
                <form id="form-pinjam" method="POST" action="">
                    @csrf
                    <div class="form-group row">
                        <label for="status" class="col-lg-2 col-lg-offset-1 control-label">Status</label>
                        <div class="col-lg-3">
                            <select name="status" id="status" class="form-control" required>
                                <option value="dipinjam">Dipinjam</option>
                                <option value="rusak">Rusak</option>
                                <option value="dikembalikan">Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="keterangan" class="col-lg-2 col-lg-offset-1 control-label">Keterangan</label>
                        <div class="col-lg-3">
                            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jumlah" class="col-lg-2 col-lg-offset-1 control-label">Jumlah</label>
                        <div class="col-lg-3">
                            <input type="number" name="jumlah" id="jumlah" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3 col-lg-offset-1">
                            <button type="submit" class="btn btn-primary">Tambahkan</button>
                        </div>
                    </div>
                </form>

                <h2>History Peminjaman</h2>
                <table class="table table-striped table-bordered table-detail">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data detail akan diisi melalui AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
