<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Biaya</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #1a1a1a;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #DC2626;
        }
        
        .header h1 {
            font-size: 16pt;
            margin-bottom: 3px;
            color: #1a1a1a;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 13pt;
            margin-bottom: 5px;
            color: #DC2626;
            font-weight: bold;
        }
        
        .header p {
            font-size: 9pt;
            color: #666;
        }
        
        .filter-info {
            margin-bottom: 12px;
            padding: 8px 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #DC2626;
            border-radius: 3px;
        }
        
        .filter-info p {
            margin: 2px 0;
            font-size: 8.5pt;
            color: #495057;
        }
        
        .filter-info strong {
            color: #212529;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        
        table thead {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            color: white;
        }
        
        table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 8.5pt;
            border: 1px solid #DC2626;
        }
        
        table td {
            padding: 6px 6px;
            border: 1px solid #dee2e6;
            font-size: 8.5pt;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #DC2626;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #FCD34D;
        }
        
        .status-approved {
            background-color: #D1FAE5;
            color: #065F46;
            border: 1px solid #6EE7B7;
        }
        
        .status-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }
        
        .category-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
        }
        
        .category-operational {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .category-administrative {
            background-color: #F3E8FF;
            color: #6B21A8;
        }
        
        .category-marketing {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .category-maintenance {
            background-color: #FED7AA;
            color: #9A3412;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 7.5pt;
            color: #6c757d;
        }
        
        .summary {
            margin-top: 12px;
            padding: 12px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #DC2626;
            border-radius: 5px;
        }
        
        .summary-row {
            display: table;
            width: 100%;
            margin: 4px 0;
            font-weight: bold;
            font-size: 9pt;
        }
        
        .summary-row span:first-child {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 15px;
        }
        
        .summary-row span:last-child {
            display: table-cell;
            width: 30%;
            text-align: right;
        }
        
        tfoot tr {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            font-weight: bold;
            border-top: 3px solid #DC2626;
        }
        
        tfoot td {
            padding: 10px 6px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $filters['company_name'] ?? config('app.name', 'Nama Perusahaan') }}</h1>
        <h2>DAFTAR BIAYA</h2>
        @if(isset($filters['outlet_name']))
            <p>{{ $filters['outlet_name'] }}</p>
        @endif
    </div>

    @if(!empty($filters) && (isset($filters['date_from']) || isset($filters['status']) || isset($filters['category'])))
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        @if(isset($filters['date_from']) && isset($filters['date_to']))
            <p>• Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</p>
        @elseif(isset($filters['date_from']))
            <p>• Dari Tanggal: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}</p>
        @elseif(isset($filters['date_to']))
            <p>• Sampai Tanggal: {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</p>
        @endif
        @if(isset($filters['status']) && $filters['status'] !== 'all')
            <p>• Status: {{ ucfirst($filters['status']) }}</p>
        @endif
        @if(isset($filters['category']) && $filters['category'] !== 'all')
            <p>• Kategori: {{ ucfirst($filters['category']) }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 12%;">No. Referensi</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 9%;">Kode Akun</th>
                <th style="width: 15%;">Nama Akun</th>
                <th style="width: 20%;">Deskripsi</th>
                <th style="width: 13%;" class="text-right">Jumlah</th>
                <th style="width: 8%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @forelse($data as $index => $expense)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                    <td>{{ $expense->reference_number }}</td>
                    <td>
                        @php
                            $categoryClass = match($expense->category) {
                                'operational' => 'category-operational',
                                'administrative' => 'category-administrative',
                                'marketing' => 'category-marketing',
                                'maintenance' => 'category-maintenance',
                                default => 'category-operational'
                            };
                            $categoryName = match($expense->category) {
                                'operational' => 'Operasional',
                                'administrative' => 'Administratif',
                                'marketing' => 'Pemasaran',
                                'maintenance' => 'Pemeliharaan',
                                default => ucfirst($expense->category)
                            };
                        @endphp
                        <span class="category-badge {{ $categoryClass }}">{{ $categoryName }}</span>
                    </td>
                    <td>{{ $expense->account_code }}</td>
                    <td>{{ $expense->account_name }}</td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-right amount">
                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $expense->status }}">
                            @if($expense->status === 'pending')
                                Menunggu
                            @elseif($expense->status === 'approved')
                                Disetujui
                            @elseif($expense->status === 'rejected')
                                Ditolak
                            @else
                                {{ ucfirst($expense->status) }}
                            @endif
                        </span>
                    </td>
                </tr>
                @php
                    if($expense->status === 'approved') {
                        $totalAmount += $expense->amount;
                    }
                @endphp
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 25px; color: #999; font-style: italic;">
                        Tidak ada data biaya yang sesuai dengan filter
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr>
                <td colspan="7" class="text-right" style="padding: 10px;"><strong>TOTAL (Disetujui):</strong></td>
                <td class="text-right amount" style="padding: 10px;">
                    <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($data) > 0)
    <div class="summary">
        <div class="summary-row">
            <span>Total Biaya yang Disetujui:</span>
            <span class="amount">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Jumlah Transaksi:</span>
            <span>{{ count($data) }} transaksi</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p style="margin-top: 3px;">Dokumen ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>
</html>
