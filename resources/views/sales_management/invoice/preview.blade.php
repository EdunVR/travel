<!DOCTYPE html>
<html>
<head>
    <title>Preview Invoice - {{ $invoice->no_invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        .invoice-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>INVOICE PENJUALAN</h2>
        <h3>{{ $setting->nama_perusahaan ?? 'Company Name' }}</h3>
    </div>

    <div class="company-info">
        <p><strong>Alamat:</strong> {{ $setting->alamat ?? '-' }}</p>
        <p><strong>Telepon:</strong> {{ $setting->telepon ?? '-' }}</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td width="30%"><strong>No. Invoice</strong></td>
                <td>: {{ $invoice->no_invoice }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ tanggal_indonesia($invoice->tanggal) }}</td>
            </tr>
            <tr>
                <td><strong>Customer</strong></td>
                <td>: {{ $invoice->member->nama }}</td>
            </tr>
            <tr>
                <td><strong>Alamat</strong></td>
                <td>: {{ $invoice->member->alamat }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th>Keterangan</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td>{{ $item->kuantitas }}</td>
                <td>{{ $item->satuan ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->keterangan)
    <div class="keterangan">
        <strong>Keterangan:</strong> {{ $invoice->keterangan }}
    </div>
    @endif

    <div class="footer" style="margin-top: 50px;">
        <table>
            <tr>
                <td width="50%" class="text-center">
                    <br><br>
                    <p>Dibuat oleh,</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
                <td width="50%" class="text-center">
                    <br><br>
                    <p>Disetujui oleh,</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
