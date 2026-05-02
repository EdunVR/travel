<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Pembelian PDF</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            font-size: 14px;
            font-weight: bold;
            padding: 2px;
            vertical-align: top;
        }
        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
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
            font-size: 24px;
            font-weight: bold;
        }
        .header {
            margin-bottom: 10px;
        }
        .header .barcode img {
            width: 100px;
            height: auto;
        }
        .body {
            margin-bottom: 10px;
            border-top: 1px solid #000;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo -->
                <td style="width: 20%;">
                    <img src="{{ public_path($setting->path_logo) }}" alt="{{ $setting->nama_perusahaan }}" width="80">
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <span class="nama-perusahaan">{{ $setting->nama_perusahaan }}</span><br>
                    {{ $setting->alamat }}<br>
                    Telp: {{ $setting->telepon }}
                </td>
                <!-- Kolom Kanan: Barcode -->
                <td style="width: 20%; text-align: right;">
                    @php
                        $barcode = 'PBL' . $pembelian->id_pembelian;
                        $barcodeOptions = [
                            'height' => 15, // Tinggi barcode diperkecil
                            'width' => 1,   // Lebar barcode diperkecil
                        ];
                    @endphp
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C39',  $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <table>
            <tr>
                <!-- Kolom Kiri -->
                <td style="width: 60%;">
                    <table>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ tanggal_indonesia($pembelian->created_at) }}</td>
                        </tr>
                        <tr>
                            <td>Supplier</td>
                            <td>: {{ $pembelian->supplier->nama }}</td>
                        </tr>
                        <tr>
                            <td>Operator</td>
                            <td>: {{ auth()->user()->name }}</td>
                        </tr>
                    </table>
                </td>
                <!-- Kolom Kanan -->
                <td style="width: 40%;">
                    <table>
                        <tr>
                            <td>TrxID</td>
                            <td>: PBL{{ $pembelian->id_pembelian }}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>: </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Detail Pembelian -->
    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Bahan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $item->bahan->nama_bahan }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td class="text-center">{{ $item->bahan->satuan->nama_satuan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <!-- Kolom Kiri: Tanda Terima -->
                <td style="width: 33%;">
                    <b>Pengirim</b><br>
                    ( {{ $pembelian->supplier->nama }} )
                </td>
                <!-- Kolom Tengah: Pesan -->
                <td style="width: 34%; text-align: center;">
                    <b>Tanda Terima Pembelian</b>
                </td>
                <!-- Kolom Kanan: Hormat Kami -->
                <td style="width: 33%; text-align: right;">
                    <b>Penerima</b><br>
                   ( {{ auth()->user()->name }} )
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
