{{-- resources/views/admin/pembelian/purchase-order/print-draft.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle }} - {{ $printNumber }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #374151;
            background: #fff;
        }
        
        .container {
            max-width: 210mm;
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
        
        .document-info {
            margin-top: 10px;
        }
        
        .document-number {
            font-size: 16px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            background: #f3f4f6;
            color: #374151;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #f9fafb;
        }
        
        .info-card h3 {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        
        .info-item {
            margin-bottom: 4px;
        }
        
        .info-label {
            font-weight: 500;
            color: #6b7280;
        }
        
        .info-value {
            color: #374151;
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
        }
        
        .items-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            margin-bottom: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 13px;
            color: #059669;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                padding: 5mm;
                max-width: none;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Sederhana -->
        <div class="header">
            @if($companySettings['logo_url'])
            <div style="margin-bottom: 10px;">
                <img src="{{ $companySettings['logo_url'] }}" alt="Company Logo" style="width: 50px; height: auto;">
            </div>
            @endif
            <div style="margin-bottom: 10px;">
                <strong>{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</strong>
                @if($companySettings['company_address'])
                <br><small>{{ $companySettings['company_address'] }}</small>
                @endif
                @if($companySettings['company_phone'] || $companySettings['company_email'])
                <br><small>
                    @if($companySettings['company_phone'])
                        Telp: {{ $companySettings['company_phone'] }}
                    @endif
                    @if($companySettings['company_email'])
                        @if($companySettings['company_phone']) | @endif
                        Email: {{ $companySettings['company_email'] }}
                    @endif
                </small>
                @endif
            </div>
            <h1>{{ $documentTitle }}</h1>
            <div class="document-info">
                <div class="document-number">{{ $printNumber }}</div>
                <div class="status-badge">{{ strtoupper($purchaseOrder->status) }}</div>
            </div>
        </div>

        <!-- Informasi Dasar -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Informasi Supplier</h3>
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $purchaseOrder->supplier->nama ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value">{{ $purchaseOrder->supplier->telepon ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Alamat:</span>
                    <span class="info-value">{{ $purchaseOrder->supplier->alamat ?? '-' }}</span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Informasi Dokumen</h3>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $purchaseOrder->tanggal->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Outlet:</span>
                    <span class="info-value">{{ $purchaseOrder->outlet->nama_outlet ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ $purchaseOrder->user->name ?? 'System' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi Item</th>
                    <th width="10%">Satuan</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div><strong>{{ $item->deskripsi }}</strong></div>
                        @if($item->keterangan)
                        <div style="font-size: 11px; color: #6b7280;">{{ $item->keterangan }}</div>
                        @endif
                    </td>
                    <td>{{ $item->satuan ?: 'Unit' }}</td>
                    <td class="text-right">{{ number_format($item->kuantitas, 2) }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($purchaseOrder->total_diskon > 0)
            <div class="total-row">
                <span>Total Diskon:</span>
                <span>- Rp {{ number_format($purchaseOrder->total_diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Total:</span>
                <span>Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Keterangan -->
        @if($purchaseOrder->keterangan)
        <div class="info-card">
            <h3>Keterangan</h3>
            <div>{{ $purchaseOrder->keterangan }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini adalah draft dan belum final. Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Print Button for Preview -->
    @if(request()->get('preview'))
    <div class="no-print" style="position: fixed; top: 20px; right: 20px;">
        <button onclick="window.print()" style="
            background: #059669;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        ">
            🖨️ Print Document
        </button>
        <button onclick="window.close()" style="
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        ">
            ✕ Close
        </button>
    </div>
    @endif
</body>
</html>
