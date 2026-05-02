<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Egg Tofu Mentah - {{ $production->production_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        
        .production-info {
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .production-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .production-info td {
            padding: 5px 10px;
            border: none;
        }
        
        .production-info .label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .qc-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .qc-section-header {
            background: #6366f1;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .qc-section-content {
            padding: 15px;
        }
        
        .qc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .qc-table th,
        .qc-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        
        .qc-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #555;
        }
        
        .qc-table .value {
            font-weight: bold;
            color: #333;
        }
        
        .qc-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .qc-row {
            display: table-row;
        }
        
        .qc-col {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .qc-col.label {
            background: #f8f9fa;
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        
        .qc-col.value {
            width: 60%;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
            color: #555;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .highlight {
            background: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .unit {
            color: #666;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN QUALITY CONTROL</h1>
        <h2>EGG TOFU MENTAH</h2>
    </div>

    <!-- Production Information -->
    <div class="production-info">
        <table>
            <tr>
                <td class="label">Kode Produksi:</td>
                <td><strong>{{ $production->production_code }}</strong></td>
                <td class="label">Tanggal Produksi:</td>
                <td><strong>{{ $production->start_date ? $production->start_date->format('d/m/Y') : '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Produk:</td>
                <td><strong>{{ $productNameDisplay }}</strong></td>
                <td class="label">Lini Produksi:</td>
                <td><strong>{{ $production->production_line }}</strong></td>
            </tr>
            <tr>
                <td class="label">Outlet:</td>
                <td><strong>{{ $production->outlet ? $production->outlet->nama_outlet : '-' }}</strong></td>
                <td class="label">Target Quantity:</td>
                <td><strong>{{ number_format($production->target_quantity) }} unit</strong></td>
            </tr>
        </table>
    </div>

    <!-- QC Data Sections -->
    
    <!-- 1. Perendaman -->
    <div class="qc-section">
        <div class="qc-section-header">1. PERENDAMAN</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Waktu Perendaman</div>
                    <div class="qc-col value">
                        {{ $tofuData['perendaman_waktu'] ?? '-' }} <span class="unit">jam</span>
                    </div>
                </div>
                <div class="qc-row">
                    <div class="qc-col label">Quantity Perendaman</div>
                    <div class="qc-col value">
                        {{ $tofuData['perendaman_qty'] ?? '-' }} <span class="unit">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Rijek Telur -->
    <div class="qc-section">
        <div class="qc-section-header">2. JUMLAH RIJEK TELUR</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Jumlah Rijek Telur</div>
                    <div class="qc-col value">
                        {{ $tofuData['rijek_telur'] ?? '-' }} <span class="unit">pcs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Pasteurisasi -->
    <div class="qc-section">
        <div class="qc-section-header">3. PASTEURISASI</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Waktu Pasteurisasi</div>
                    <div class="qc-col value">
                        {{ $tofuData['pasteurisasi_waktu'] ?? '-' }} <span class="unit">menit</span>
                    </div>
                </div>
                <div class="qc-row">
                    <div class="qc-col label">Suhu Pasteurisasi</div>
                    <div class="qc-col value">
                        {{ $tofuData['pasteurisasi_suhu'] ?? '-' }} <span class="unit">°C</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Berat Sari Kedelai -->
    <div class="qc-section">
        <div class="qc-section-header">4. BERAT AKHIR SARI KEDELAI</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Berat Akhir Sari Kedelai</div>
                    <div class="qc-col value">
                        {{ $tofuData['berat_sari_kedelai'] ?? '-' }} <span class="unit">kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Waktu Pencampuran -->
    <div class="qc-section">
        <div class="qc-section-header">5. WAKTU PENCAMPURAN</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Waktu Pencampuran</div>
                    <div class="qc-col value">
                        {{ $tofuData['waktu_pencampuran'] ?? '-' }} <span class="unit">menit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Filling -->
    <div class="qc-section">
        <div class="qc-section-header">6. FILLING</div>
        <div class="qc-section-content">
            <table class="qc-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Nilai</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Waktu Filling</td>
                        <td class="value">{{ $tofuData['filling_waktu'] ?? '-' }}</td>
                        <td class="unit">jam</td>
                    </tr>
                    <tr>
                        <td>Mesin 1</td>
                        <td class="value">{{ $tofuData['filling_mesin1'] ?? '-' }}</td>
                        <td class="unit">unit</td>
                    </tr>
                    <tr>
                        <td>Mesin 2</td>
                        <td class="value">{{ $tofuData['filling_mesin2'] ?? '-' }}</td>
                        <td class="unit">unit</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td><strong>Total Filling</strong></td>
                        <td class="value highlight">{{ $tofuData['filling_total'] ?? '-' }}</td>
                        <td class="unit">unit</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 7. Rijek Mentah -->
    <div class="qc-section">
        <div class="qc-section-header">7. JUMLAH RIJEK MENTAH</div>
        <div class="qc-section-content">
            <div class="qc-grid">
                <div class="qc-row">
                    <div class="qc-col label">Jumlah Rijek Mentah</div>
                    <div class="qc-col value">
                        {{ $tofuData['rijek_mentah'] ?? '-' }} <span class="unit">unit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">QC Inspector</div>
            <div class="signature-line">Nama & Tanda Tangan</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Supervisor Produksi</div>
            <div class="signature-line">Nama & Tanda Tangan</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Manager Produksi</div>
            <div class="signature-line">Nama & Tanda Tangan</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis pada {{ date('d/m/Y H:i:s') }}</p>
        <p>{{ $production->outlet ? $production->outlet->nama_outlet : 'Sistem Produksi' }} - Quality Control Report</p>
    </div>
</body>
</html>