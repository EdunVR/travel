<!DOCTYPE html>
<html>
<head>
    <title>Laporan Log Perubahan Sparepart</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-info {
            font-size: 9px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            page-break-inside: auto;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
        }
        
        td {
            font-size: 8px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 9px;
        }
        
        .badge-stok {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        
        .badge-harga {
            background-color: #ffc107;
            color: black;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        
        .positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 8px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT. GHAVA SHANKARA NUSANTARA</div>
        <div class="report-title">LAPORAN LOG PERUBAHAN SPAREPART</div>
        <div class="report-info">
            @if($sparepart)
                Sparepart: {{ $sparepart->nama_sparepart }} ({{ $sparepart->kode_sparepart }}) | 
            @endif
            @if($start_date && $end_date)
                Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} |
            @elseif($start_date)
                Mulai: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} |
            @elseif($end_date)
                Sampai: {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} |
            @endif
            @if($tipe_perubahan)
                Tipe: {{ strtoupper($tipe_perubahan) }} |
            @endif
            Total Data: {{ $total_records }}
        </div>
    </div>

    <div class="table-container">
        @if($logs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="10%">Kode Sparepart</th>
                    <th width="15%">Nama Sparepart</th>
                    <th width="8%">Tipe</th>
                    <th width="10%">Nilai Lama</th>
                    <th width="10%">Nilai Baru</th>
                    <th width="8%">Selisih</th>
                    <th width="12%">User</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->sparepart->kode_sparepart }}</td>
                    <td>{{ $log->sparepart->nama_sparepart }}</td>
                    <td class="text-center">
                        @if($log->tipe_perubahan == 'stok')
                            <span class="badge-stok">STOK</span>
                        @else
                            <span class="badge-harga">HARGA</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($log->tipe_perubahan == 'stok')
                            {{ number_format($log->nilai_lama, 0, ',', '.') }} {{ $log->sparepart->satuan }}
                        @else
                            Rp {{ number_format($log->nilai_lama, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($log->tipe_perubahan == 'stok')
                            {{ number_format($log->nilai_baru, 0, ',', '.') }} {{ $log->sparepart->satuan }}
                        @else
                            Rp {{ number_format($log->nilai_baru, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right 
                        @if($log->selisih > 0) positive 
                        @elseif($log->selisih < 0) negative 
                        @endif">
                        @if($log->selisih > 0)+@endif
                        @if($log->tipe_perubahan == 'stok')
                            {{ number_format($log->selisih, 0, ',', '.') }} {{ $log->sparepart->satuan }}
                        @else
                            Rp {{ number_format($log->selisih, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            Tidak ada data log perubahan untuk periode yang dipilih
        </div>
        @endif
    </div>

    @if($logs->count() > 0)
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        • Total Perubahan: {{ $total_records }} record<br>
        • Perubahan Stok: {{ $logs->where('tipe_perubahan', 'stok')->count() }} record<br>
        • Perubahan Harga: {{ $logs->where('tipe_perubahan', 'harga')->count() }} record<br>
        • Tanggal Generate: {{ $generated_at }}<br>
        • Digenerate oleh: {{ $generated_by }}
    </div>
    @endif

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem. 
        Dokumen ini sah dan dapat digunakan sebagai referensi audit.
    </div>
</body>
</html>
