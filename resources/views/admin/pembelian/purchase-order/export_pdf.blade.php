{{-- resources/views/admin/pembelian/purchase-order/export_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Purchase Order</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #374151;
            background: #fff;
        }
        
        .container {
            max-width: 297mm;
            margin: 0 auto;
            padding: 10mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #6b7280;
        }
        
        .filter-info {
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }
        
        .filter-info .filter-item {
            display: inline-block;
            margin-right: 20px;
        }
        
        .filter-info .filter-label {
            font-weight: 600;
            color: #374151;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .summary-card {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .summary-card .count {
            font-size: 18px;
            font-weight: 700;
            color: #059669;
        }
        
        .summary-card .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        
        .items-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-draft { background: #f3f4f6; color: #374151; }
        .status-diproses { background: #fef3c7; color: #92400e; }
        .status-dikirim { background: #ffedd5; color: #9a3412; }
        .status-diterima { background: #d1fae5; color: #065f46; }
        .status-selesai { background: #dbeafe; color: #1e40af; }
        .status-dibatalkan { background: #fee2e2; color: #991b1b; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .total-summary {
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            margin-top: 10px;
        }
        
        .total-summary .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .total-summary .grand-total {
            font-weight: 700;
            font-size: 12px;
            color: #059669;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LAPORAN PURCHASE ORDER</h1>
            <div class="subtitle">{{ $setting->nama_perusahaan ?? 'Nama Perusahaan' }}</div>
        </div>

        <!-- Filter Information -->
        <div class="filter-info">
            <div class="filter-item">
                <span class="filter-label">Periode:</span>
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Semua' }} 
                - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Semua' }}
            </div>
            <div class="filter-item">
                <span class="filter-label">Status:</span>
                {{ $status == 'all' ? 'Semua Status' : ucfirst($status) }}
            </div>
            <div class="filter-item">
                <span class="filter-label">Tanggal Cetak:</span>
                {{ date('d/m/Y H:i:s') }}
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="count">{{ $purchaseOrders->count() }}</div>
                <div class="label">Total PO</div>
            </div>
            <div class="summary-card">
                <div class="count">Rp {{ number_format($purchaseOrders->sum('total'), 0, ',', '.') }}</div>
                <div class="label">Total Nilai</div>
            </div>
            <div class="summary-card">
                <div class="count">{{ $purchaseOrders->where('status', 'draft')->count() }}</div>
                <div class="label">Draft</div>
            </div>
            <div class="summary-card">
                <div class="count">{{ $purchaseOrders->where('status', 'selesai')->count() }}</div>
                <div class="label">Selesai</div>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="12%">No PO</th>
                    <th width="10%">Tanggal</th>
                    <th width="15%">Supplier</th>
                    <th width="10%">Outlet</th>
                    <th width="8%" class="text-right">Items</th>
                    <th width="12%" class="text-right">Subtotal</th>
                    <th width="12%" class="text-right">Diskon</th>
                    <th width="12%" class="text-right">Total</th>
                    <th width="8%">Status</th>
                    <th width="10%">Jatuh Tempo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrders as $index => $po)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $po->no_po }}</td>
                    <td>{{ $po->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $po->supplier->nama_supplier ?? 'N/A' }}</td>
                    <td>{{ $po->outlet->nama_outlet ?? 'N/A' }}</td>
                    <td class="text-center">{{ $po->items->count() }}</td>
                    <td class="text-right">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($po->total_diskon > 0)
                        - Rp {{ number_format($po->total_diskon, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge status-{{ $po->status }}">
                            {{ strtoupper($po->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        {{ $po->due_date ? $po->due_date->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Summary -->
        <div class="total-summary">
            <div class="total-row">
                <span>Total Subtotal:</span>
                <span>Rp {{ number_format($purchaseOrders->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Total Diskon:</span>
                <span>- Rp {{ number_format($purchaseOrders->sum('total_diskon'), 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span>Rp {{ number_format($purchaseOrders->sum('total'), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik dari Sistem ERP</p>
            <p>Halaman 1</p>
        </div>
    </div>
</body>
</html>
