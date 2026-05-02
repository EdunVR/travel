<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Satuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Satuan</h2>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
        @if(isset($filterStatus) && $filterStatus !== 'ALL')
            <p>Filter Status: {{ $filterStatus }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="20%">Nama Satuan</th>
                <th width="10%">Simbol</th>
                <th width="25%">Konversi</th>
                <th width="15%">Deskripsi</th>
                <th class="text-center" width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($satuan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_satuan }}</td>
                    <td>{{ $item->nama_satuan }}</td>
                    <td>{{ $item->simbol ?? '-' }}</td>
                    <td>
                        @if($item->nilai_konversi && $item->satuanUtama)
                            1 {{ $item->simbol }} = {{ $item->nilai_konversi }} {{ $item->satuanUtama->simbol }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->deskripsi ?? '-' }}</td>
                    <td class="text-center">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
