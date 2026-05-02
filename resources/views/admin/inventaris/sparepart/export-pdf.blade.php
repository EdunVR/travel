<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Data Sparepart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .filters p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        /* Simple Table Style for No History */
        .simple-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .simple-table th,
        .simple-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .simple-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .simple-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Detailed Section Style for With History */
        .sparepart-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .sparepart-header {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .sparepart-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .sparepart-info div {
            flex: 1;
        }
        .logs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .logs-table th,
        .logs-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .logs-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .logs-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .no-logs {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA SPAREPART</h1>
        <p>{{ config('app.name') }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="filters">
        <h3>Filter Export:</h3>
        @if(isset($filters['data_type']) && $filters['data_type'] === 'selected')
            <p><strong>Tipe Data:</strong> Data Terpilih</p>
        @else
            <p><strong>Tipe Data:</strong> Semua Data</p>
        @endif
        @if(isset($filters['include_history']))
            <p><strong>Include History:</strong> {{ $filters['include_history'] === 'yes' ? 'Ya' : 'Tidak' }}</p>
        @endif
        @if(isset($filters['log_start_date']) && $filters['log_start_date'])
            <p><strong>Tanggal Log Mulai:</strong> {{ date('d/m/Y', strtotime($filters['log_start_date'])) }}</p>
        @endif
        @if(isset($filters['log_end_date']) && $filters['log_end_date'])
            <p><strong>Tanggal Log Akhir:</strong> {{ date('d/m/Y', strtotime($filters['log_end_date'])) }}</p>
        @endif
        @if(isset($filters['log_category']) && $filters['log_category'])
            <p><strong>Kategori Log:</strong> {{ ucfirst($filters['log_category']) }}</p>
        @endif
        <p><strong>Total Sparepart:</strong> {{ count($spareparts) }}</p>
    </div>

    @if(isset($filters['include_history']) && $filters['include_history'] === 'no')
        {{-- Simple Table View - No History --}}
        <table class="simple-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode</th>
                    <th style="width: 25%;">Nama Sparepart</th>
                    <th style="width: 12%;">Merk</th>
                    <th style="width: 15%;">Harga</th>
                    <th style="width: 8%;">Stok</th>
                    <th style="width: 8%;">Min</th>
                    <th style="width: 10%;">Status Stok</th>
                    <th style="width: 5%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spareparts as $index => $sparepart)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sparepart->kode_sparepart }}</td>
                    <td>{{ $sparepart->nama_sparepart }}</td>
                    <td>{{ $sparepart->merk ?: '-' }}</td>
                    <td class="text-right">Rp {{ number_format($sparepart->harga, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $sparepart->stok }} {{ $sparepart->satuan }}</td>
                    <td class="text-center">{{ $sparepart->stok_minimum }}</td>
                    <td class="text-center">
                        @if($sparepart->stok <= 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($sparepart->stok <= $sparepart->stok_minimum)
                            <span class="badge badge-warning">Minimum</span>
                        @else
                            <span class="badge badge-success">Tersedia</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($sparepart->is_active)
                            <span class="badge badge-active">Aktif</span>
                        @else
                            <span class="badge badge-inactive">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        {{-- Detailed View - With History --}}
        @foreach($spareparts as $sparepart)
        <div class="sparepart-section">
            <div class="sparepart-header">
                <div class="sparepart-info">
                    <div>
                        <strong>{{ $sparepart->kode_sparepart }}</strong> - {{ $sparepart->nama_sparepart }}
                        @if($sparepart->merk)
                            <br><small>Merk: {{ $sparepart->merk }}</small>
                        @endif
                    </div>
                    <div class="text-right">
                        <strong>Rp {{ number_format($sparepart->harga, 0, ',', '.') }}</strong>
                        <br><small>Stok: {{ $sparepart->stok }} {{ $sparepart->satuan }}</small>
                    </div>
                </div>
                @if($sparepart->outlet)
                    <small>Outlet: {{ $sparepart->outlet->nama_outlet }}</small>
                @endif
            </div>

            @if(isset($sparepart->filtered_logs) && count($sparepart->filtered_logs) > 0)
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Kode Log</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th class="text-center">Lama</th>
                        <th class="text-center">Perubahan</th>
                        <th class="text-center">Baru</th>
                        <th>Karyawan</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sparepart->filtered_logs as $log)
                    <tr>
                        <td>LOG-{{ str_pad($log->id_log, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                        <td>
                            @if($log->tipe_perubahan === 'stok')
                                <span class="badge badge-info">Stok</span>
                            @else
                                <span class="badge badge-success">Harga</span>
                            @endif
                        </td>
                        <td>
                            @if($log->kategori)
                                <span class="badge badge-warning">{{ ucfirst($log->kategori) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ $log->nilai_lama }}</td>
                        <td class="text-center">
                            @if($log->selisih > 0)
                                <span style="color: green;">+{{ $log->selisih }}</span>
                            @else
                                <span style="color: red;">{{ $log->selisih }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $log->nilai_baru }}</td>
                        <td>{{ $log->karyawan ? $log->karyawan->name : '-' }}</td>
                        <td>{{ $log->keterangan }}</td>
                        <td>{{ $log->user ? $log->user->name : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-logs">
                Tidak ada log yang sesuai dengan filter
            </div>
            @endif
        </div>
        @endforeach
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>--- Akhir Laporan ---</p>
    </div>
</body>
</html>