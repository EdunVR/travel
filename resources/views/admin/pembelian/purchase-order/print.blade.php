{{-- resources/views/admin/pembelian/purchase-order/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->no_po }}</title>
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
            padding: 15mm;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .document-info {
            text-align: right;
        }
        
        .document-info h2 {
            font-size: 20px;
            font-weight: 600;
            color: #059669;
            margin-bottom: 10px;
        }
        
        .document-info .po-number {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
        }
        
        .info-card h3 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .info-item {
            margin-bottom: 5px;
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
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .items-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
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
        
        .totals {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .total-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 14px;
            color: #059669;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #374151;
            margin: 60px 0 10px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-draft { background: #f3f4f6; color: #374151; }
        .status-diproses { background: #fef3c7; color: #92400e; }
        .status-dikirim { background: #ffedd5; color: #9a3412; }
        .status-diterima { background: #d1fae5; color: #065f46; }
        .status-selesai { background: #dbeafe; color: #1e40af; }
        .status-dibatalkan { background: #fee2e2; color: #991b1b; }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                padding: 10mm;
                max-width: none;
            }
            
            .no-print {
                display: none;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
        
        .terms-conditions {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .terms-conditions h4 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #374151;
        }
        
        .terms-conditions ul {
            list-style: none;
            padding-left: 0;
        }
        
        .terms-conditions li {
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }
        
        .terms-conditions li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @if($companySettings['logo_url'])
                <div style="float: left; margin-right: 15px;">
                    <img src="{{ $companySettings['logo_url'] }}" alt="Company Logo" style="width: 60px; height: auto;">
                </div>
                @endif
                <div>
                    <h1>{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</h1>
                    @if($companySettings['company_address'])
                    <p>{{ $companySettings['company_address'] }}</p>
                    @endif
                    <p>
                        @if($companySettings['company_phone'])
                            Telp: {{ $companySettings['company_phone'] }}
                        @endif
                        @if($companySettings['company_email'])
                            @if($companySettings['company_phone']) | @endif
                            Email: {{ $companySettings['company_email'] }}
                        @endif
                    </p>
                    @if($companySettings['company_website'])
                    <p>Website: {{ $companySettings['company_website'] }}</p>
                    @endif
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="document-info">
                <h2>
                    @if($purchaseOrder->status === 'vendor_bill')
                        VENDOR BILL / INVOICE
                    @elseif($purchaseOrder->status === 'payment')
                        PAYMENT RECEIPT
                    @elseif($purchaseOrder->status === 'permintaan_pembelian' || $purchaseOrder->status === 'request_quotation')
                        DRAFT PURCHASE ORDER
                    @else
                        PURCHASE ORDER
                    @endif
                </h2>
                <div class="po-number">
                    @if(isset($printNumber))
                        {{ $printNumber }}
                    @elseif($purchaseOrder->status === 'vendor_bill' && $purchaseOrder->no_vendor_bill)
                        {{ $purchaseOrder->no_vendor_bill }}
                    @else
                        {{ $purchaseOrder->no_po }}
                    @endif
                </div>
                <div class="status-badge status-{{ $purchaseOrder->status }}">
                    {{ strtoupper($purchaseOrder->status) }}
                </div>
            </div>
        </div>

        <!-- Informasi PO -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Informasi Supplier</h3>
                <div class="info-item">
                    <span class="info-label">Nama Supplier:</span>
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
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $purchaseOrder->supplier->email ?? '-' }}</span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Informasi Pengiriman</h3>
                <div class="info-item">
                    <span class="info-label">Tanggal PO:</span>
                    <span class="info-value">{{ $purchaseOrder->tanggal->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jatuh Tempo:</span>
                    <span class="info-value">{{ $purchaseOrder->due_date ? $purchaseOrder->due_date->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Outlet:</span>
                    <span class="info-value">{{ $purchaseOrder->outlet->nama_outlet ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Metode Pengiriman:</span>
                    <span class="info-value">{{ $purchaseOrder->metode_pengiriman ?: '-' }}</span>
                </div>
                @if($purchaseOrder->alamat_pengiriman)
                <div class="info-item">
                    <span class="info-label">Alamat Pengiriman:</span>
                    <span class="info-value">{{ $purchaseOrder->alamat_pengiriman }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Deskripsi Item</th>
                    <th width="10%">Satuan</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="10%" class="text-right">Diskon</th>
                    <th width="20%" class="text-right">Subtotal</th>
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
                    <td class="text-right">
                        @if($item->diskon > 0)
                        Rp {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-card">
                <h3>Ringkasan Pembayaran</h3>
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total Diskon:</span>
                    <span>- Rp {{ number_format($purchaseOrder->total_diskon, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total:</span>
                    <span>Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="total-card">
                <h3>Informasi Tambahan</h3>
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ $purchaseOrder->user->name ?? 'System' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Dibuat:</span>
                    <span class="info-value">{{ $purchaseOrder->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($purchaseOrder->keterangan)
                <div class="info-item">
                    <span class="info-label">Keterangan:</span>
                    <span class="info-value">{{ $purchaseOrder->keterangan }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="terms-conditions">
            <h4>Syarat dan Ketentuan:</h4>
            <ul>
                <li>Barang harus sesuai dengan spesifikasi yang tercantum dalam purchase order</li>
                <li>Pengiriman sesuai dengan tanggal yang disepakati</li>
                <li>Pembayaran sesuai dengan terms yang disetujui</li>
                <li>Klaim harus diajukan dalam waktu 7 hari setelah penerimaan barang</li>
            </ul>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div>Disetujui Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top: 10px;">(___________________)</div>
                <div>Manager Purchasing</div>
            </div>
            <div class="signature-box">
                <div>Diterima Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top: 10px;">(___________________)</div>
                <div>Supplier</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak secara elektronik. Tidak memerlukan tanda tangan basah.</p>
            <p>Cetak pada: {{ date('d/m/Y H:i:s') }}</p>
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

    <script>
        // Auto print jika parameter print=true
        @if(request()->get('print'))
        window.onload = function() {
            window.print();
        }
        @endif

        // Close window after print
        window.onafterprint = function() {
            @if(request()->get('preview'))
            // Tetap buka untuk preview
            @else
            setTimeout(function() {
                window.close();
            }, 500);
            @endif
        };
    </script>
</body>
</html>
