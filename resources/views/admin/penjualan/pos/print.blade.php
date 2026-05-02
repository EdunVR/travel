<!DOCTYPE html>
<html>
<head>
    <title>Struk POS - {{ $posSale->no_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            padding-bottom: 10px;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 2px 0;
        }
        .right {
            text-align: right;
        }
        .item-row td {
            padding: 4px 0;
        }
        .company-logo {
            width: 40px;
            height: auto;
            margin-bottom: 5px;
        }
        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>
<body>
    @if($companySettings['logo_url'])
    <div class="center">
        <img src="{{ $companySettings['logo_url'] }}" class="company-logo" alt="Logo">
    </div>
    @endif
    
    <div class="center bold" style="font-size: 14px;">
        {{ $companySettings['company_name'] }}
    </div>
    
    @if($companySettings['company_address'])
    <div class="center" style="font-size: 10px;">
        {{ strip_tags($companySettings['formatted_address']) }}
    </div>
    @endif
    
    @if($companySettings['company_phone'] || $companySettings['company_email'])
    <div class="center" style="font-size: 10px;">
        @if($companySettings['company_phone'])
            Telp: {{ $companySettings['company_phone'] }}
        @endif
        @if($companySettings['company_email'])
            @if($companySettings['company_phone']) | @endif
            {{ $companySettings['company_email'] }}
        @endif
    </div>
    @endif
    
    <div class="center" style="font-size: 10px;">
        Point of Sales
    </div>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>No. Transaksi</td>
            <td class="right">{{ $posSale->no_transaksi }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="right">{{ $posSale->tanggal->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="right">{{ $posSale->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td class="right">{{ $posSale->member->nama ?? 'Pelanggan Umum' }}</td>
        </tr>
        @if($posSale->outlet)
        <tr>
            <td>Outlet</td>
            <td class="right">{{ $posSale->outlet->nama_outlet }}</td>
        </tr>
        @endif
    </table>
    
    <div class="line"></div>
    
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posSale->items as $item)
            <tr class="item-row">
                <td>{{ $item->nama_produk }}</td>
                <td style="text-align: center;">{{ $item->kuantitas }}</td>
                <td class="right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right bold">Rp {{ number_format($posSale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($posSale->total_diskon > 0)
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($posSale->total_diskon, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($posSale->ppn > 0)
        <tr>
            <td>PPN {{ $companySettings['tax_rate'] ?? 10 }}%</td>
            <td class="right">Rp {{ number_format($posSale->ppn, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr style="font-size: 14px;">
            <td class="bold">TOTAL</td>
            <td class="right bold">Rp {{ number_format($posSale->total, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <div class="line"></div>
    
    @if($posSale->is_bon)
    <table>
        <tr>
            <td class="bold">PIUTANG</td>
            <td class="right bold">Rp {{ number_format($posSale->total, 0, ',', '.') }}</td>
        </tr>
    </table>
    @else
    <table>
        <tr>
            <td>Bayar ({{ strtoupper($posSale->jenis_pembayaran) }})</td>
            <td class="right">Rp {{ number_format($posSale->jumlah_bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="bold">Kembali</td>
            <td class="right bold">Rp {{ number_format($posSale->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>
    @endif
    
    <div class="line"></div>
    
    @if($posSale->catatan)
    <div style="margin: 8px 0;">
        <div class="bold">Catatan:</div>
        <div>{{ $posSale->catatan }}</div>
    </div>
    <div class="line"></div>
    @endif
    
    <!-- Legal Information (if available) -->
    @if($companySettings['npwp'])
    <div class="center" style="font-size: 9px; margin: 5px 0;">
        NPWP: {{ $companySettings['npwp'] }}
    </div>
    @endif
    
    <div class="center" style="margin-top: 10px;">
        Terima Kasih 🙏
    </div>
    <div class="center" style="font-size: 10px; margin-top: 5px;">
        Powered by {{ $companySettings['company_name'] }}
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>
</html>
