<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aktiva Tetap</title>
    <style>
        @page {
            margin: 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
            padding: 5mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 10px;
            color: #4a4a4a;
        }
        
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #10B981;
        }
        
        .filter-info p {
            margin: 3px 0;
            font-size: 9pt;
        }
        
        .category-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .category-header {
            background-color: #10B981;
            color: white;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #059669;
            color: white;
        }
        
        table th {
            padding: 7px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            border: 1px solid #ddd;
        }
        
        table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-active {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-inactive {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .status-disposed {
            background-color: #E5E7EB;
            color: #374151;
        }
        
        .summary-box {
            margin-top: 15px;
            padding: 12px;
            background-color: #f5f5f5;
            border: 2px solid #10B981;
            page-break-inside: avoid;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            font-size: 10pt;
        }
        
        .summary-row.total {
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #10B981;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $filters['company_name'] ?? 'Nama Perusahaan' }}</h1>
        <h2>Daftar Aktiva Tetap</h2>
        @if(isset($filters['outlet_name']))
            <p style="font-size: 10pt; color: #666;">{{ $filters['outlet_name'] }}</p>
        @endif
    </div>

    @if(!empty($filters))
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        @if(isset($filters['status']) && $filters['status'] !== 'all')
            <p>Status: {{ ucfirst($filters['status']) }}</p>
        @endif
        @if(isset($filters['category']) && $filters['category'] !== 'all')
            <p>Kategori: {{ ucfirst($filters['category']) }}</p>
        @endif
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    @endif

    @php
        $grandTotalAcquisition = 0;
        $grandTotalDepreciation = 0;
        $grandTotalBookValue = 0;
        
        // Group assets by category if grouping is enabled
        $groupedAssets = collect($data);
        if (isset($filters['group_by_category']) && $filters['group_by_category']) {
            $groupedAssets = $groupedAssets->groupBy('category');
        } else {
            $groupedAssets = collect(['all' => $groupedAssets]);
        }
    @endphp

    @foreach($groupedAssets as $category => $assets)
        @if($category !== 'all')
        <div class="category-section">
            <div class="category-header">
                {{ $category === 'building' ? 'Bangunan' : 
                   ($category === 'vehicle' ? 'Kendaraan' : 
                   ($category === 'equipment' ? 'Peralatan' : 
                   ($category === 'furniture' ? 'Furniture' : 
                   ($category === 'electronics' ? 'Elektronik' : 
                   ($category === 'computer' ? 'Komputer & IT' : 
                   ($category === 'land' ? 'Tanah' : 
                   ($category === 'other' ? 'Lainnya' : ucfirst($category)))))))) }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 16%;">Nama Aset</th>
                    @if($category === 'all')
                    <th style="width: 10%;">Kategori</th>
                    @endif
                    <th style="width: 9%;">Tgl Perolehan</th>
                    <th style="width: 12%;" class="text-right">Harga Perolehan</th>
                    <th style="width: 8%;">Metode</th>
                    <th style="width: 6%;" class="text-center">Umur</th>
                    <th style="width: 12%;" class="text-right">Akum. Penyusutan</th>
                    <th style="width: 12%;" class="text-right">Nilai Buku</th>
                    <th style="width: 6%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $categoryTotalAcquisition = 0;
                    $categoryTotalDepreciation = 0;
                    $categoryTotalBookValue = 0;
                @endphp
                @forelse($assets as $index => $asset)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="font-family: 'Courier New', monospace;">{{ $asset->code }}</td>
                        <td>{{ $asset->name }}</td>
                        @if($category === 'all')
                        <td>
                            {{ $asset->category === 'building' ? 'Bangunan' : 
                               ($asset->category === 'vehicle' ? 'Kendaraan' : 
                               ($asset->category === 'equipment' ? 'Peralatan' : 
                               ($asset->category === 'furniture' ? 'Furniture' : 
                               ($asset->category === 'electronics' ? 'Elektronik' : 
                               ($asset->category === 'computer' ? 'Komputer & IT' : 
                               ($asset->category === 'land' ? 'Tanah' : 
                               ($asset->category === 'other' ? 'Lainnya' : ucfirst($asset->category)))))))) }}
                        </td>
                        @endif
                        <td>{{ \Carbon\Carbon::parse($asset->acquisition_date)->format('d/m/Y') }}</td>
                        <td class="text-right amount">{{ number_format($asset->acquisition_cost, 0, ',', '.') }}</td>
                        <td style="font-size: 7pt;">
                            {{ $asset->depreciation_method === 'straight_line' ? 'Garis Lurus' : 
                               ($asset->depreciation_method === 'declining_balance' ? 'Saldo Menurun' : 
                               ($asset->depreciation_method === 'double_declining' ? 'Saldo Menurun 2x' : 
                               ($asset->depreciation_method === 'units_of_production' ? 'Unit Produksi' : ucfirst($asset->depreciation_method)))) }}
                        </td>
                        <td class="text-center">{{ $asset->useful_life }} th</td>
                        <td class="text-right amount" style="color: #DC2626;">{{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}</td>
                        <td class="text-right amount" style="color: #059669;">{{ number_format($asset->book_value, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $asset->status }}">
                                {{ $asset->status === 'active' ? 'Aktif' : 
                                   ($asset->status === 'inactive' ? 'Nonaktif' : 
                                   ($asset->status === 'disposed' ? 'Dibuang' : ucfirst($asset->status))) }}
                            </span>
                        </td>
                    </tr>
                    @php
                        $categoryTotalAcquisition += $asset->acquisition_cost;
                        $categoryTotalDepreciation += $asset->accumulated_depreciation;
                        $categoryTotalBookValue += $asset->book_value;
                    @endphp
                @empty
                    <tr>
                        <td colspan="{{ $category === 'all' ? 11 : 10 }}" class="text-center" style="padding: 20px; color: #999;">
                            Tidak ada data aktiva tetap
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($assets) > 0)
            <tfoot>
                <tr style="background-color: #e5e7eb; font-weight: bold;">
                    <td colspan="{{ $category === 'all' ? 5 : 4 }}" class="text-right" style="padding: 8px;">
                        {{ $category !== 'all' ? 'SUBTOTAL ' . strtoupper($category) : 'TOTAL' }}:
                    </td>
                    <td class="text-right amount" style="padding: 8px;">{{ number_format($categoryTotalAcquisition, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                    <td class="text-right amount" style="padding: 8px; color: #DC2626;">{{ number_format($categoryTotalDepreciation, 0, ',', '.') }}</td>
                    <td class="text-right amount" style="padding: 8px; color: #059669;">{{ number_format($categoryTotalBookValue, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>

        @php
            $grandTotalAcquisition += $categoryTotalAcquisition;
            $grandTotalDepreciation += $categoryTotalDepreciation;
            $grandTotalBookValue += $categoryTotalBookValue;
        @endphp

        @if($category !== 'all')
        </div>
        @endif
    @endforeach

    @if(count($data) > 0)
    <div class="summary-box">
        <div class="summary-row">
            <span>Total Harga Perolehan:</span>
            <span class="amount">Rp {{ number_format($grandTotalAcquisition, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Akumulasi Penyusutan:</span>
            <span class="amount" style="color: #DC2626;">Rp {{ number_format($grandTotalDepreciation, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row total">
            <span>Total Nilai Buku:</span>
            <span class="amount" style="color: #059669;">Rp {{ number_format($grandTotalBookValue, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="font-size: 9pt; margin-top: 8px; border-top: 1px solid #ccc; padding-top: 8px;">
            <span>Tingkat Penyusutan:</span>
            <span class="amount">{{ $grandTotalAcquisition > 0 ? number_format(($grandTotalDepreciation / $grandTotalAcquisition) * 100, 2) : 0 }}%</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
