<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Produk:</strong> {{ $produk->kode_produk }} - {{ $produk->nama_produk }}<br>
            <strong>Agen:</strong> {{ $agen->nama }}<br>
            <strong>Periode:</strong> {{ $start_date }} s/d {{ $end_date }}
        </div>
        
        @if($penjualanPerGerobak->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="bg-primary">
                    <th>Tanggal</th>
                    <th>Kode Gerobak</th>
                    <th>Nama Gerobak</th>
                    <th class="text-right">Jumlah Terjual</th>
                    <th class="text-right">Omset</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualanPerGerobak as $penjualan)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($penjualan->tanggal)) }}</td>
                    <td>
                        @if($penjualan->kode_gerobak)
                            {{ $penjualan->kode_gerobak }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($penjualan->nama_gerobak)
                            {{ $penjualan->nama_gerobak }}
                        @else
                            <span class="text-muted">Tidak Ada Gerobak</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $penjualan->jumlah_terjual }}</td>
                    <td class="text-right">Rp {{ number_format($penjualan->total_omset, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-success">
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right">{{ $penjualanPerGerobak->sum('jumlah_terjual') }}</th>
                    <th class="text-right">Rp {{ number_format($penjualanPerGerobak->sum('total_omset'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="alert alert-warning">
            Tidak ada penjualan untuk produk "{{ $produk->kode_produk }} - {{ $produk->nama_produk }}" 
            pada periode {{ $start_date }} s/d {{ $end_date }}.
        </div>
        @endif
    </div>
</div>
