<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota PDF</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            font-size: 14px;
            padding: 2px;
            vertical-align: top;
        }
        table.data td,
        table.data th {
            border: 1px solid #000;
            padding: 3px;
        }
        table.data {
            border-collapse: collapse;
            width: 100%;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .nama-perusahaan {
            font-size: 18px;
            font-weight: bold;
        }
        .header {
            margin-bottom: 10px;
        }
        .header .barcode img {
            width: 50px;
            height: 20px;
        }
        .body {
            margin: 10px 0;
        }
        .footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
        .total-section {
            margin-top: 10px;
        }
        .date-trxid {
            margin-top: 10px;
        }
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                margin: 0;
                padding: 10px;
            }
            th, td {
                padding: 2px 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo Box -->
                <td style="width: 20%;">
                    <div class="logo-box">
                        {{ strtoupper($setting->nama_perusahaan) }}
                    </div>
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <span style="font-size: 16px; font-weight: bold;">TELP: {{ strtoupper($setting->telepon) }}</span><br>
                    <span>{{ session('isBon') === 'true' ? 'BON PENJUALAN' : 'NOTA PEMBELIAN' }}</span>
                </td>
                <!-- Kolom Kanan: Barcode -->
                <td style="width: 20%; text-align: right;">
                    @php
                        $barcode = 'TRX00' . $penjualan->id_penjualan;
                        $barcodeOptions = [
                            'height' => 15,
                            'width' => 1,
                        ];
                    @endphp
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <table>
            <tr>
                <!-- Kolom Kiri -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ tanggal_indonesia($penjualan->created_at) }}</td>
                        </tr>
                        <tr>
                            <td>Nama Customer</td>
                            <td>: {{ $penjualan->member->nama ?? 'Customer Umum' }}</td>
                        </tr>
                        <tr>
                            <td>Tempo</td>
                            <td>: {{ $tempo ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
                <!-- Kolom Kanan -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>TrxID</td>
                            <td>: TRX00{{ $penjualan->id_penjualan }}</td>
                        </tr>
                        <tr>
                            <td>Operator</td>
                            <td>: {{ auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>: {{ session('isBon') === 'true' ? 'BON' : 'LUNAS' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Detail Penjualan -->
    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jumlah</th>
                <th>Nama</th>
                <th>Harga Satuan</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td class="text-right">{{ $item->jumlah }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td class="text-right">{{ format_uang($item->harga_jual) }}</td>
                    <td class="text-right">{{ $item->diskon . '%' }}</td>
                    <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <table>
            <tr>
                <td colspan="4" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->total_harga) }}</b></td>
            </tr>
            @if (session('isChecked') === 'true')
            <tr>
                <td colspan="4" class="text-right"><b>Hutang Customer</b></td>
                <td class="text-right"><b>{{ format_uang(session('piutang')) }}</b></td>
            </tr>
            @endif
            <tr>
                <td colspan="4" class="text-right"><b>Diskon</b></td>
                <td class="text-right"><b>{{ $penjualan->diskon  . '%' }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Total Bayar</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
            </tr>
            @if (session('isBon') === 'false')
            <tr>
                <td colspan="4" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
            </tr>
            @endif
            @if (session('isCheckedIngatkan') === 'true')
            <tr>
                <td colspan="5" class="text-center"><b>Catatan Hutang : {{ format_uang(session('piutang')) }}</b></td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <!-- Kolom Kiri: Tanda Terima -->
                <td style="width: 33%;">
                    <b>Tanda Terima</b><br>
                    ( {{ $penjualan->member->nama ?? 'Customer Umum' }} )
                </td>
                <!-- Kolom Tengah: Pesan -->
                <td style="width: 34%; text-align: center;">
                    <b>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</b>
                </td>
                <!-- Kolom Kanan: Hormat Kami -->
                <td style="width: 33%; text-align: right;">
                    <b>Hormat Kami</b><br>
                    ( {{ auth()->user()->name }} )
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
