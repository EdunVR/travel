<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode Sparepart</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .barcode-container {
            display: inline-block;
            margin: 5mm;
            padding: 3mm;
            border: 1px solid #ddd;
            text-align: center;
            width: 60mm;
            height: 25mm;
            page-break-inside: avoid;
        }
        
        .barcode-title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1mm;
            line-height: 1.2;
        }
        
        .barcode-kode {
            font-size: 8px;
            color: #666;
            margin-bottom: 1mm;
        }
        
        .barcode-image {
            margin: 1mm 0;
            font-family: 'Libre Barcode 128', cursive;
            font-size: 20px;
            letter-spacing: 2px;
        }
        
        .no-print {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            .barcode-container {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Barcode</button>
        <button onclick="window.close()" class="btn btn-default">Tutup</button>
        <hr>
    </div>
    
    <div style="text-align: center;">
        @foreach($spareparts as $sparepart)
        <div class="barcode-container">
            <div class="barcode-title">{{ Str::limit($sparepart->nama_sparepart, 30) }}</div>
            <div class="barcode-kode">{{ $sparepart->kode_sparepart }}</div>
            <div class="barcode-image">*{{ $sparepart->kode_sparepart }}*</div>
            <div style="font-size: 7px; margin-top: 1mm;">
                {{ $sparepart->merk ?: '-' }} | Stok: {{ $sparepart->stok }}
            </div>
        </div>
        @endforeach
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
