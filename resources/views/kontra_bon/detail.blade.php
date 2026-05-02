<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th>Kode Kontra Bon</th>
                <td>{{ $kontraBon->kode_kontra_bon }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ tanggal_indonesia($kontraBon->tanggal) }}</td>
            </tr>
            <tr>
                <th>Customer</th>
                <td>{{ $kontraBon->member->nama }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th>Total Pembayaran</th>
                <td>{{ format_uang($kontraBon->total_pembayaran) }}</td>
            </tr>
            <tr>
                <th>Sisa Hutang</th>
                <td>{{ format_uang($kontraBon->sisa_hutang) }}</td>
            </tr>
            <tr>
                <th>Outlet</th>
                <td>{{ $kontraBon->outlet->nama_outlet }}</td>
            </tr>
        </table>
    </div>
</div>

<h4>Detail Pembayaran</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>TrxID</th>
            <th>Tanggal</th>
            <th>Total Hutang</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kontraBon->details as $detail)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>TRX00{{ $detail->penjualan->id_penjualan }}</td>
            <td>{{ tanggal_indonesia($detail->penjualan->created_at) }}</td>
            <td>Rp {{ format_uang($kontraBon->details->sum('nominal')) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
