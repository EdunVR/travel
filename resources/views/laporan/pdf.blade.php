<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Umum</title>

    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 10px;
        }
        h3, h4 {
            color: #343a40;
            text-align: center;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 12px; /* Ukuran font lebih kecil */
        }
        .table th, .table td {
            padding: 5px; /* Mengurangi padding agar lebih hemat ruang */
            vertical-align: middle;
            border: 1px solid #dee2e6;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            font-size: 12px; /* Font lebih kecil */
            padding: 6px;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        /* Atur lebar kolom agar lebih proporsional */
        th:nth-child(1), td:nth-child(1) { width: 5%; }  /* No */
        th:nth-child(2), td:nth-child(2) { width: 12%; } /* Tanggal */
        th:nth-child(3), td:nth-child(3),
        th:nth-child(4), td:nth-child(4),
        th:nth-child(5), td:nth-child(5),
        th:nth-child(6), td:nth-child(6),
        th:nth-child(7), td:nth-child(7),
        th:nth-child(8), td:nth-child(8) { width: 11%; }

        /* Mencegah tabel terpotong saat di-print sebagai PDF */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 5px;
            }
            .table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            h3, h4 {
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Laporan Umum</h3>
        <h4>
            Tanggal {{ tanggal_indonesia($awal, false) }} s/d Tanggal {{ tanggal_indonesia($akhir, false) }}
        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Penjualan</th>
                    <th>Pembelian</th>
                    <th>Pengeluaran</th>
                    <th>Profit</th>
                    <th>Hutang</th>
                    <th>Piutang</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $col)
                            <td>{{ $col }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">Total</td>
                    <td>{{ number_format($totalPenjualan, 2, ',', '.') }}</td>
                    <td>{{ number_format($totalPembelian, 2, ',', '.') }}</td>
                    <td>{{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
                    <td>{{ number_format($totalPendapatan, 2, ',', '.') }}</td>
                    <td>{{ number_format($totalHutang, 2, ',', '.') }}</td>
                    <td>{{ number_format($totalPiutang, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
