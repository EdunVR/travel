<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Preview - {{ $purchaseOrder->no_po }}</title>
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
        
        .document-info .invoice-number {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .preview-badge {
            display: inline-block;
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
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
            
            .preview-badge {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $setting->nama_perusahaan ?? 'Nama Perusahaan' }}</h1>
                <p>{{ $setting->alamat_perusahaan ?? 'Alamat Perusahaan' }}</p>
                <p>Telp: {{ $setting->telepon_perusahaan ?? '-' }} | Email: {{ $setting->email_perusahaan ?? '-' }}</p>
            </div>
            <div class="document-info">
                <div class="preview-badge">PREVIEW</div>
                <h2>INVOICE</h2>
                <div class="invoice-number">INV/{{ date('Ym') }}/XXXX</div>
                <div style="font-size: 11px; color: #6b7280; margin-top: 5px;">
                    Berdasarkan PO: {{ $purchaseOrder->no_po }}
                </div>
            </div>
        </div>

        <!-- Informasi Invoice -->
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
            </div>
            
            <div class="info-card">
                <h3>Informasi Invoice</h3>
                <div class="info-item">
                    <span class="info-label">Tanggal Invoice:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($invoiceData['tanggal_invoice'])->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jatuh Tempo:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($invoiceData['tanggal_jatuh_tempo'])->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Metode Pembayaran:</span>
                    <span class="info-value">{{ ucfirst($invoiceData['metode_pembayaran'] ?? '-') }}</span>
                </div>
                @if(!empty($invoiceData['keterangan']))
                <div class="info-item">
                    <span class="info-label">Keterangan:</span>
                    <span class="info-value">{{ $invoiceData['keterangan'] }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Deskripsi Item</th>
                    <th width="10%">Satuan</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="10%" class="text-right">Diskon</th>
                    <th width="10%" class="text-right">Pajak</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoiceData['items'] as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div><strong>{{ $item['deskripsi'] }}</strong></div>
                    </td>
                    <td>{{ $item['satuan'] ?: 'Unit' }}</td>
                    <td class="text-right">{{ number_format($item['kuantitas'], 2) }}</td>
                    <td class="text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($item['diskon'] > 0)
                        Rp {{ number_format($item['diskon'], 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($item['pajak'] > 0)
                        Rp {{ number_format($item['pajak'], 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
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
                    <span>Rp {{ number_format($invoiceData['totals']['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total Diskon:</span>
                    <span>- Rp {{ number_format($invoiceData['totals']['totalDiskon'], 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total Pajak:</span>
                    <span>Rp {{ number_format($invoiceData['totals']['totalPajak'], 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Total:</span>
                    <span>Rp {{ number_format($invoiceData['totals']['grandTotal'], 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="total-card">
                <h3>Informasi Tambahan</h3>
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ auth()->user()->name ?? 'System' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Preview:</span>
                    <span class="info-value">{{ date('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">Preview - Belum Disimpan</span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div>Disetujui Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top: 10px;">(___________________)</div>
                <div>Finance</div>
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
            <p>Dokumen ini adalah preview invoice. Tidak berlaku untuk pembayaran.</p>
            <p>Preview dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Print Button for Preview -->
    @if($isPreview ?? false)
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
            🖨️ Print Preview
        </button>
    </div>
    @endif
</body>
</html>
