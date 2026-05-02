<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrak Kerja - {{ $recruitment->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .contract { margin: 20px; padding: 20px; border: 1px solid #000; }
        .header { text-align: center; }
        .content { margin-top: 20px; }
        .footer { margin-top: 40px; text-align: right; }
        .jobdesk-list { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="contract">
        <div class="header">
            <h2>PERJANJIAN KONTRAK KERJA</h2>
            <p>Nomor: {{ $recruitment->id }}/PK/HRD/{{ date('Y') }}</p>
        </div>
        <div class="content">
            <p>Pada hari ini, {{ date('d F Y') }}, bertempat di {{ config('app.name') }}, telah dibuat perjanjian kontrak kerja antara:</p>
            <p><strong>Pihak Pertama:</strong> {{ $manager->name }} - {{ $manager->position }} ({{ $manager->department }})</p>
            <p><strong>Pihak Kedua:</strong> {{ $recruitment->name }}</p>
            <p>Dengan posisi sebagai <strong>{{ $recruitment->position }}</strong> di departemen <strong>{{ $recruitment->department }}</strong>.</p>
            <p>Jobdesk Pihak Kedua:</p>
            <ul class="jobdesk-list">
                @if($recruitment->jobdesk)
                    @foreach(json_decode($recruitment->jobdesk) as $job)
                        <li>{{ $job }}</li>
                    @endforeach
                @else
                    <li>Tidak ada jobdesk.</li>
                @endif
            </ul>
            <p>Perjanjian ini berlaku sejak tanggal {{ date('d F Y') }} hingga waktu yang ditentukan kemudian.</p>
        </div>
        <div class="footer">
            <p>Mengetahui,</p>
            <p><strong>Pihak Pertama</strong></p>
            <p>_________________________</p>
            <p><strong>Pihak Kedua</strong></p>
            <p>_________________________</p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
