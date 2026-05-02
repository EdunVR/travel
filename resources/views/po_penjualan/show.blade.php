<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th width="40%">No. PO</th>
                <td>{{ $poPenjualan->no_po }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ tanggal_indonesia($poPenjualan->tanggal, false) }}</td>
            </tr>
            <tr>
                <th>Customer</th>
                <td>{{ $poPenjualan->member->nama ?? 'Customer Umum' }}</td>
            </tr>
            <tr>
                <th>Outlet</th>
                <td>{{ $poPenjualan->outlet->nama_outlet ?? '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th width="40%">Total Item</th>
                <td>{{ $poPenjualan->total_item }}</td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>{{ format_uang($poPenjualan->total_harga) }}</td>
            </tr>
            <tr>
                <th>Diskon</th>
                <td>{{ $poPenjualan->diskon }}%</td>
            </tr>
            <tr>
                <th>Ongkir</th>
                <td>{{ format_uang($poPenjualan->ongkir) }}</td>
            </tr>
            <tr>
                <th>Total Bayar</th>
                <td>{{ format_uang($poPenjualan->bayar) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($poPenjualan->status == 'menunggu')
                        <span class="label label-warning">Menunggu</span>
                    @elseif($poPenjualan->status == 'lunas')
                        <span class="label label-success">Lunas</span>
                    @else
                        <span class="label label-danger">Gagal</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>Detail Items</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk/Ongkir</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($poPenjualan->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($detail->tipe_item == 'ongkir')
                                {{ $detail->keterangan ?? 'Ongkos Kirim' }}
                            @else
                                {{ $detail->produk->nama_produk ?? '-' }}
                            @endif
                        </td>
                        <td>{{ format_uang($detail->harga_jual) }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>{{ $detail->diskon }}%</td>
                        <td>{{ format_uang($detail->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
