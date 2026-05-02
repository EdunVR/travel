<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail">
    <div class="modal-dialog modal-xl" role="document" style="width: 95%; max-width: 1400px;">
        <div class="modal-content" style="min-height: 80vh;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detail Agen</h4>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Agen:</label>
                            <p id="detail-nama" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Telepon:</label>
                            <p id="detail-telepon" class="form-control-static"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alamat:</label>
                            <p id="detail-alamat" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Outlet:</label>
                            <p id="detail-outlet" class="form-control-static"></p>
                        </div>
                    </div>
                </div>
                
                <div id="detail-location"></div>

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-penjualan" data-toggle="tab">Laporan Penjualan</a></li>
                    <li><a href="#tab-inventory" data-toggle="tab">Persediaan Produk</a></li>
                    <li><a href="#tab-gerobak" data-toggle="tab" id="tab-gerobak-link">Daftar Gerobak</a></li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-penjualan">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-3">
                                <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <button onclick="filterPenjualan()" class="btn btn-primary">Filter</button>
                            </div>
                            <div class="col-md-4 text-right">
                                <button onclick="tambahLaporan()" class="btn btn-success btn-xs">
                                    <i class="fa fa-plus"></i> Input Laporan Manual
                                </button>
                            </div>
                        </div>

                        <div id="omset-info" class="alert alert-info" style="display: none;">
                            <strong>Periode: </strong> <span id="periode"></span> | 
                            <strong>Total Pembelian: </strong> <span id="total-pembelian"></span> | 
                            <strong>Total Omset: </strong> <span id="total-omset"></span> | 
                            <strong>Total Transaksi: </strong> <span id="total-transaksi"></span>
                        </div>

                        <table class="table table-stiped table-bordered table-penjualan">
                            <thead>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Tipe</th>
                                <th>Stok Awal</th>
                                <th>Pembelian</th>
                                <th>Penjualan</th>
                                <th>Stok Akhir</th>
                                <th>Nominal</th>
                                <th>Gerobak</th>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab-inventory">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-8">
                                <div class="alert alert-info">
                                    <strong>Info Stok:</strong><br>
                                    • <strong>Stok Akhir:</strong> Total stok yang dimiliki agen saat ini (stok akhir)<br>
                                    • <strong>Stok di Gerobak:</strong> Stok yang sudah didistribusikan ke gerobak<br>
                                    • <strong>Stok Tersedia:</strong> Stok yang masih bisa didistribusikan (Stok Akhir - Stok Gerobak)
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <button onclick="syncStok()" class="btn btn-warning btn-xs">
                                    <i class="fa fa-refresh"></i> Sync Stok
                                </button>
                                <button onclick="refreshInventory()" class="btn btn-primary btn-xs">
                                    <i class="fa fa-repeat"></i> Refresh
                                </button>
                            </div>
                        </div>
                        
                        <table class="table table-striped table-bordered table-inventory" style="width: 100%;">
                            <thead>
                                <tr class="bg-primary">
                                    <th width="5%">No</th>
                                    <th width="10%">Kode Produk</th>
                                    <th width="20%">Nama Produk</th>
                                    <th width="10%">Stok Akhir</th>
                                    <th width="10%">Stok di Gerobak</th>
                                    <th width="10%">Stok Tersedia</th>
                                    <th width="35%">Detail per Gerobak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="tab-pane" id="tab-gerobak">
                        <div id="gerobak-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Penjualan per Gerobak -->
<div class="modal fade" id="modal-penjualan-gerobak" tabindex="-1" role="dialog" aria-labelledby="modal-penjualan-gerobak">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Detail Penjualan per Gerobak</h4>
            </div>
            <div class="modal-body">
                <!-- Konten akan diisi oleh JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
