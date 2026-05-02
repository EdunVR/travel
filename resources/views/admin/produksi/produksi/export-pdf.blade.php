<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Produksi - {{ $outlet->nama_outlet }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #888;
        }
        
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-item {
            text-align: center;
            flex: 1;
        }
        
        .summary-item h3 {
            margin: 0;
            font-size: 20px;
            color: #2563eb;
        }
        
        .summary-item p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        
        .filters {
            background: #e5e7eb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 11px;
        }
        
        .filters strong {
            color: #374151;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-approved { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #d97706; }
        .status-completed { background: #dcfce7; color: #16a34a; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        
        .priority {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .priority-normal { background: #f1f5f9; color: #64748b; }
        .priority-high { background: #fed7aa; color: #ea580c; }
        .priority-urgent { background: #fee2e2; color: #dc2626; }
        
        .progress-bar {
            width: 50px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #2563eb;
            transition: width 0.3s ease;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .materials-list {
            font-size: 9px;
            line-height: 1.3;
        }
        
        .materials-list div {
            margin-bottom: 2px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA PRODUKSI</h1>
        <h2>{{ $outlet->nama_outlet }}</h2>
        <p>Dicetak pada: {{ date('d F Y H:i:s') }}</p>
        @if($request->filled('start_date') || $request->filled('end_date'))
            <p>
                Periode: 
                {{ $request->start_date ? date('d F Y', strtotime($request->start_date)) : 'Awal' }} - 
                {{ $request->end_date ? date('d F Y', strtotime($request->end_date)) : 'Akhir' }}
            </p>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <h3>{{ $totalProductions }}</h3>
            <p>Total Produksi</p>
        </div>
        <div class="summary-item">
            <h3>{{ number_format($totalTarget) }}</h3>
            <p>Total Target</p>
        </div>
        <div class="summary-item">
            <h3>{{ number_format($totalRealized) }}</h3>
            <p>Total Realisasi</p>
        </div>
        <div class="summary-item">
            <h3>{{ $totalTarget > 0 ? number_format(($totalRealized / $totalTarget) * 100, 1) : 0 }}%</h3>
            <p>Rata-rata Progress</p>
        </div>
    </div>

    <!-- Applied Filters -->
    @if($request->filled('status') || $request->filled('production_line') || $request->filled('start_date') || $request->filled('end_date'))
    <div class="filters">
        <strong>Filter yang Diterapkan:</strong>
        @if($request->filled('status') && $request->status !== 'all')
            Status: {{ ucfirst($request->status) }} |
        @endif
        @if($request->filled('production_line') && $request->production_line !== 'all')
            Lini: {{ $request->production_line }} |
        @endif
        @if($request->filled('start_date'))
            Dari: {{ date('d/m/Y', strtotime($request->start_date)) }} |
        @endif
        @if($request->filled('end_date'))
            Sampai: {{ date('d/m/Y', strtotime($request->end_date)) }}
        @endif
    </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="7%">Kode</th>
                <th width="10%">Produk</th>
                <th width="6%">Lini</th>
                <th width="6%">Target</th>
                <th width="6%">Realisasi</th>
                <th width="5%">Progress</th>
                <th width="6%">Status</th>
                <th width="5%">Prioritas</th>
                <th width="12%">Material</th>
                <th width="7%">Total Material</th>
                <th width="7%">HPP/Unit</th>
                <th width="7%">Biaya Tenaga</th>
                <th width="7%">Biaya Operasional</th>
                <th width="9%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $production)
                @php
                    $realizedQty = $production->realizations->sum('quantity_produced');
                    $progress = $production->target_quantity > 0 ? ($realizedQty / $production->target_quantity) * 100 : 0;
                    
                    // Calculate material cost
                    $materialCost = 0;
                    foreach ($production->materials as $material) {
                        if ($material->material_type === 'bahan') {
                            $bahan = $material->material;
                            if ($bahan && $bahan->hargaBahan && $bahan->hargaBahan->isNotEmpty()) {
                                $hargaBahan = $bahan->hargaBahan->first();
                                $materialCost += $material->quantity_required * ($hargaBahan->harga_beli ?? 0);
                            }
                        } else {
                            $produk = $material->material;
                            if ($produk && method_exists($produk, 'calculateHpp')) {
                                $materialCost += $material->quantity_required * ($produk->calculateHpp() ?? 0);
                            }
                        }
                    }
                    
                    $laborCost = $production->laborCosts->sum('total_cost');
                    $operationalCost = $production->operationalCosts->sum('amount');
                    $totalCost = $materialCost + $laborCost + $operationalCost;
                    $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                @endphp
                <tr>
                    <td>{{ $production->production_code }}</td>
                    <td>{{ $production->product->nama_produk ?? '-' }}</td>
                    <td class="text-center">{{ $production->production_line }}</td>
                    <td class="text-right">{{ number_format($production->target_quantity) }}</td>
                    <td class="text-right">{{ number_format($realizedQty) }}</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                        {{ number_format($progress, 1) }}%
                    </td>
                    <td class="text-center">
                        <span class="status status-{{ $production->status }}">
                            {{ ucfirst($production->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="priority priority-{{ $production->priority ?? 'normal' }}">
                            {{ ucfirst($production->priority ?? 'Normal') }}
                        </span>
                    </td>
                    <td>
                        <div class="materials-list">
                            @foreach($production->materials->take(3) as $material)
                                <div>
                                    @if($material->material_type === 'bahan')
                                        {{ $material->material->nama_bahan ?? 'N/A' }}
                                    @else
                                        {{ $material->material->nama_produk ?? 'N/A' }}
                                    @endif
                                    ({{ number_format($material->quantity_required, 2) }} {{ $material->unit }})
                                </div>
                            @endforeach
                            @if($production->materials->count() > 3)
                                <div><em>+{{ $production->materials->count() - 3 }} lainnya</em></div>
                            @endif
                        </div>
                    </td>
                    <td class="text-right">
                        {{ $materialCost > 0 ? 'Rp ' . number_format($materialCost) : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $hppPerUnit > 0 ? 'Rp ' . number_format($hppPerUnit) : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $laborCost > 0 ? 'Rp ' . number_format($laborCost) : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $operationalCost > 0 ? 'Rp ' . number_format($operationalCost) : '-' }}
                    </td>
                    <td class="text-center">
                        {{ date('d/m/Y', strtotime($production->start_date)) }}<br>
                        <small>s/d {{ date('d/m/Y', strtotime($production->end_date)) }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data produksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Status Summary -->
    @if($statusCounts->count() > 0)
    <div style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px;">Ringkasan Status:</h3>
        <div style="display: flex; gap: 20px;">
            @foreach($statusCounts as $status => $count)
                <div style="text-align: center;">
                    <div style="font-size: 16px; font-weight: bold; color: #2563eb;">{{ $count }}</div>
                    <div style="font-size: 11px; color: #666;">{{ ucfirst($status) }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ date('d F Y H:i:s') }}</p>
        <p>{{ config('app.name') }} - Sistem Manajemen Produksi</p>
    </div>
</body>
</html>