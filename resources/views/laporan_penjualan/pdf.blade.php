<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan</title>

    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px; /* Reduced base font size */
        }
        .container {
            padding: 5px;
        }
        h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        h4 {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            font-size: 9px; /* Smaller font for table */
            table-layout: fixed;
        }
        .table th, .table td {
            padding: 3px;
            border: 1px solid #ddd;
            word-wrap: break-word;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            font-size: 8px; /* Smaller badge font */
            padding: 2px 5px;
            margin: 1px 0;
            display: inline-block;
            line-height: 1.2;
        }
        .total-row td {
            padding: 2px;
        }
        .payment-totals {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        /* Adjusted column widths */
        th:nth-child(1), td:nth-child(1) { width: 4%; }  /* No */
        th:nth-child(2), td:nth-child(2) { width: 8%; } /* Tanggal */
        th:nth-child(3), td:nth-child(3) { width: 18%; } /* Nama Produk */
        th:nth-child(4), td:nth-child(4) { width: 8%; } /* HPP */
        th:nth-child(5), td:nth-child(5) { width: 10%; } /* Harga Jual */
        th:nth-child(6), td:nth-child(6) { width: 6%; }  /* Jumlah */
        th:nth-child(7), td:nth-child(7) { width: 12%; } /* Cash/Bon - Wider column */
        th:nth-child(8), td:nth-child(8) { width: 8%; } /* Profit */

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
            .table {
                page-break-inside: avoid;
            }
            .badge {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center">Laporan Penjualan</h3>
        <h4 class="text-center">
            {{ tanggal_indonesia($awal, false) }} s/d {{ tanggal_indonesia($akhir, false) }}
        </h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>HPP</th>
                    <th>Harga</th>
                    <th>Jml</th>
                    <th>Pembayaran</th>
                    @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                    <th>Profit</th>
                    @endif
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                    <td><span class="badge bg-primary">{{ format_uang($totalHPP) }}</span></td>
                    <td><span class="badge bg-warning">{{ format_uang($totalHargaJual) }}</span></td>
                    <td><span class="badge bg-success">{{ $totalJumlah }}</span></td>
                    <td>
                        <div class="payment-totals">
                            <span class="badge bg-info">Cash: {{ format_uang($totalCash) }}</span>
                            <span class="badge bg-danger">Bon: {{ format_uang($totalBon) }}</span>
                        </div>
                    </td>
                    @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                    <td><span class="badge bg-danger">{{ format_uang($totalProfit) }}</span></td>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row['DT_RowIndex'] }}</td>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ $row['nama_produk'] }}</td>
                        <td>{{ $row['hpp'] }}</td>
                        <td>{{ $row['harga_jual'] }}</td>
                        <td>{{ $row['jumlah'] }}</td>
                        <td>{{ $row['payment_type'] }}</td>
                        @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                        <td>{{ $row['profit'] }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
