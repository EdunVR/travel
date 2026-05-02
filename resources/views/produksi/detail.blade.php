<div class="modal-dialog modal-xl">
    <div class="modal-content modal-glass">
        <div class="modal-header bg-gradient-pastel-primary">
            <h5 class="modal-title font-weight-bold text-dark">
                <i data-feather="clipboard" class="icon-lg mr-2"></i>Detail Produksi
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i data-feather="x"></i></span>
            </button>
        </div>
        <div class="modal-body bg-light">
            <!-- Header Info -->
            <div class="row mb-4 animate-fade-in">
                <div class="col-md-6">
                    <div class="card card-pastel-primary">
                        <div class="card-body">
                            <h6 class="card-title text-primary font-weight-bold mb-3">
                                <i data-feather="info" class="icon-sm mr-2"></i>Informasi Produk
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-item mb-2">
                                        <small class="text-muted d-block">Produk</small>
                                        <span class="font-weight-bold text-dark">{{ $produksi->produk->nama_produk ?? 'Produk Telah Dihapus' }}</span>
                                    </div>
                                    <div class="info-item mb-2">
                                        <small class="text-muted d-block">Outlet</small>
                                        <span class="font-weight-bold text-dark">{{ $produksi->outlet->nama_outlet ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-item mb-2">
                                        <small class="text-muted d-block">Jumlah Produksi</small>
                                        <span class="font-weight-bold text-info">{{ format_uang($produksi->jumlah) }} unit</span>
                                    </div>
                                    <div class="info-item mb-2">
                                        <small class="text-muted d-block">Tanggal</small>
                                        <span class="font-weight-bold text-dark">{{ tanggal_indonesia($produksi->created_at, true) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-pastel-info h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <i data-feather="dollar-sign" class="icon-lg text-success mb-2"></i>
                                <h6 class="text-muted mb-1">HPP Per Unit</h6>
                                <h2 class="font-weight-bold text-success mb-0">{{ format_uang($hppUnit) }}</h2>
                            </div>
                            <small class="text-muted">Total Biaya Produksi: {{ format_uang($totalHPP) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Detail -->
            <div class="card card-pastel mb-4 animate-slide-up">
                <div class="card-header bg-pastel-primary">
                    <h6 class="card-title mb-0 font-weight-bold text-dark">
                        <i data-feather="box" class="icon-sm mr-2"></i>Detail Bahan Baku
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-pastel-info">
                                <tr>
                                    <th width="5%" class="text-center border-0">#</th>
                                    <th class="border-0">
                                        <i data-feather="package" class="icon-xs mr-1"></i>Nama Bahan
                                    </th>
                                    <th class="text-center border-0">
                                        <i data-feather="hash" class="icon-xs mr-1"></i>Jumlah
                                    </th>
                                    <th class="text-center border-0">
                                        <i data-feather="divide" class="icon-xs mr-1"></i>Satuan
                                    </th>
                                    <th class="text-center border-0">
                                        <i data-feather="dollar-sign" class="icon-xs mr-1"></i>Harga Beli/Unit
                                    </th>
                                    <th class="text-center border-0">
                                        <i data-feather="calendar" class="icon-xs mr-1"></i>Tanggal Pembelian
                                    </th>
                                    <th class="text-right border-0">
                                        <i data-feather="bar-chart-2" class="icon-xs mr-1"></i>Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalHPP = 0;
                                    $no = 1;
                                @endphp
                                @foreach($produksi->detail as $detail)
                                    @php
                                        $subtotal = $detail->harga_beli * $detail->jumlah;
                                        $totalHPP += $subtotal;
                                    @endphp
                                    <tr class="animate-fade-in" style="animation-delay: {{ $no * 0.1 }}s">
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="font-weight-bold">{{ $detail->bahan->nama_bahan ?? 'Bahan Telah Dihapus' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-pastel-primary">{{ format_uang($detail->jumlah) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pastel-info">{{ $detail->bahan->satuan->nama_satuan ?? '-' }}</span>
                                        </td>
                                        <td class="text-center font-weight-bold text-success">{{ format_uang($detail->harga_beli) }}</td>
                                        <td class="text-center">{{ tanggal_indonesia($detail->tanggal_harga, false) }}</td>
                                        <td class="text-right font-weight-bold text-primary">{{ format_uang($subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-pastel-primary">
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right border-0">Total HPP</td>
                                    <td class="text-right border-0 text-success">{{ format_uang($totalHPP) }}</td>
                                </tr>
                                <tr class="table-pastel-info">
                                    <td colspan="6" class="text-right border-0">HPP Per Unit</td>
                                    <td class="text-right border-0 font-weight-bold text-success">
                                        {{ format_uang($produksi->jumlah > 0 ? round($totalHPP / $produksi->jumlah) : 0) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row animate-fade-in">
                <div class="col-md-4">
                    <div class="card card-pastel-primary text-center btn-hover-lift">
                        <div class="card-body py-3">
                            <i data-feather="box" class="icon-lg text-primary mb-2"></i>
                            <h5 class="mb-1 text-primary font-weight-bold">{{ $produksi->detail->count() }}</h5>
                            <small class="text-muted">Jumlah Bahan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pastel-info text-center btn-hover-lift">
                        <div class="card-body py-3">
                            <i data-feather="dollar-sign" class="icon-lg text-info mb-2"></i>
                            <h5 class="mb-1 text-info font-weight-bold">{{ format_uang($totalHPP) }}</h5>
                            <small class="text-muted">Total Biaya</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pastel-success text-center btn-hover-lift">
                        <div class="card-body py-3">
                            <i data-feather="trending-up" class="icon-lg text-success mb-2"></i>
                            <h5 class="mb-1 text-success font-weight-bold">{{ format_uang($produksi->jumlah > 0 ? round($totalHPP / $produksi->jumlah) : 0) }}</h5>
                            <small class="text-muted">Biaya per Unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-pastel-secondary btn-hover-grow" data-dismiss="modal">
                <i data-feather="x" class="icon-xs mr-1"></i>Tutup
            </button>
        </div>
    </div>
</div>

<style>
.badge-pastel-primary {
    background: linear-gradient(135deg, #a8c0ff 0%, #b6fbff 100%);
    color: #1a237e;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
}

.badge-pastel-info {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
    color: #00695c;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
}

.card-pastel-success {
    background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(76,175,80,0.2);
}

.info-item {
    padding: 0.5rem 0;
}

.table > tbody > tr > td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-color: #e3f2fd;
}

.border-0 {
    border: none !important;
}
</style>

<script>
feather.replace();
</script>
