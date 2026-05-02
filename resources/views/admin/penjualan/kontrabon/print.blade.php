<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kontra Bon PDF</title>
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
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 3px;
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
            width: 60px;
            height: 20px;
        }
        .body {
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .section-title {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Logo -->
                <td style="width: 20%;">
                    <div class="logo-box">
                        {{ strtoupper($companySetting->company_name ?? 'PERUSAHAAN') }}
                    </div>
                </td>
                <!-- Nama Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <div class="nama-perusahaan">{{ $companySetting->company_name ?? 'NAMA PERUSAHAAN' }}</div>
                    {{ $companySetting->company_address ?? 'Alamat Perusahaan' }}<br>
                    Telp: {{ $companySetting->company_phone ?? '-' }}
                </td>
                <!-- Barcode -->
                <td style="width: 20%; text-align: right;">
                    @php
                        $barcode = 'KB00' . $kontraBon->id_kontra_bon;
                        $barcodeOptions = ['height' => 20, 'width' => 1.2];
                    @endphp
                    @if(class_exists('Milon\Barcode\DNS1D'))
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                    @else
                        <div style="border: 1px solid #000; padding: 5px; font-size: 10px;">{{ $barcode }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <!-- Info Kontra Bon -->
        <table>
            <tr>
                <td style="width: 50%;">
                    <table>
                        <tr><td>Kode Kontra Bon</td><td>: {{ $kontraBon->kode_kontra_bon ?? $kontraBon->no_kontra_bon }}</td></tr>
                        <tr><td>Tanggal</td><td>: {{ function_exists('tanggal_indonesia') ? tanggal_indonesia($kontraBon->tanggal ?? $kontraBon->created_at) : $kontraBon->created_at->format('d/m/Y') }}</td></tr>
                        <tr><td>Jatuh Tempo</td><td>: {{ function_exists('tanggal_indonesia') ? tanggal_indonesia($kontraBon->tanggal_jatuh_tempo) : date('d/m/Y', strtotime($kontraBon->tanggal_jatuh_tempo)) }}</td></tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table>
                        <tr><td>Customer</td><td>: {{ $kontraBon->member->nama ?? '-' }}</td></tr>
                        <tr><td>Pembayaran</td><td>: {{ function_exists('format_uang') ? format_uang($kontraBon->total_pembayaran) : 'Rp ' . number_format($kontraBon->total_pembayaran, 0, ',', '.') }}</td></tr>
                        <tr><td>Saldo</td><td>: {{ function_exists('format_uang') ? format_uang($kontraBon->member->saldo ?? 0) : 'Rp ' . number_format($kontraBon->member->saldo ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Total Hutang</td><td>: {{ function_exists('format_uang') ? format_uang($totalHutang) : 'Rp ' . number_format($totalHutang, 0, ',', '.') }}</td></tr>
                        @if($kontraBon->status == 'lunas')
                        <tr><td colspan="2" style="color: green; font-weight: bold; text-align: center;">STATUS: LUNAS</td></tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tabel Hutang Belum Dibayar -->
        @if(isset($startDate) && isset($endDate) && $startDate && $endDate)
        <div class="section-title">Data Hutang yang Ditagihkan ( {{ function_exists('tanggal_indonesia') ? tanggal_indonesia($startDate) : date('d/m/Y', strtotime($startDate)) }} s/d {{ function_exists('tanggal_indonesia') ? tanggal_indonesia($endDate) : date('d/m/Y', strtotime($endDate)) }} )</div>
        @else
        <div class="section-title">Data Hutang yang Ditagihkan</div>
        @endif
        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>TrxID</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
            @if(isset($piutangBelumLunas) && $piutangBelumLunas->count() > 0)
                @foreach ($piutangBelumLunas as $key => $piutang)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ function_exists('tanggal_indonesia') ? tanggal_indonesia($piutang->penjualan->created_at) : $piutang->penjualan->created_at->format('d/m/Y') }}</td>
                        <td>{{ $piutang->penjualan->posSale->no_transaksi ?? 'TRX00' . $piutang->id_penjualan }}</td>
                        <td class="text-right">{{ function_exists('format_uang') ? format_uang($piutang->sisa_piutang) : 'Rp ' . number_format($piutang->sisa_piutang, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data hutang yang belum dibayar.</td>
                </tr>
            @endif
            </tbody>
        </table>

        <!-- Tabel Hutang Sudah Dilunasi -->
        <div class="section-title">Data Hutang yang Sudah Dilunasi</div>
        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>TrxID</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
            @php
                $detailsWithPayment = $kontraBon->details->filter(function($detail) {
                    return $detail->jumlah_bayar > 0;
                });
            @endphp
            @forelse ($detailsWithPayment as $key => $detail)
                @if ($detail->penjualan)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ function_exists('tanggal_indonesia') ? tanggal_indonesia($detail->penjualan->created_at) : $detail->penjualan->created_at->format('d/m/Y') }}</td>
                        <td>{{ $detail->penjualan->posSale->no_transaksi ?? 'TRX00' . $detail->penjualan->id_penjualan }}</td>
                        <td class="text-right">{{ function_exists('format_uang') ? format_uang($detail->nominal) : 'Rp ' . number_format($detail->nominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data hutang yang sudah dilunasi</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 33%;">
                    <b>Pengirim</b><br><br>
                    ( {{ $kontraBon->member->nama ?? '-' }} )
                </td>
                <td style="width: 34%;" class="text-center">
                    <b>Tanda Terima Kontra Bon</b>
                </td>
                <td style="width: 33%;" class="text-right">
                    <b>Penerima</b><br><br>
                    ( {{ $kontraBon->user->name ?? auth()->user()->name ?? '-' }} )
                </td>
            </tr>
        </table>
    </div>
</body>
</html>