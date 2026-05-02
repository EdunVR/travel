<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .signature { margin-top: 100px; float: right; }
        .footer { margin-top: 50px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal: {{ $date }}</p>
    </div>
    
    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
    
    @if($signature)
    <div class="signature">
        <p>Hormat kami,</p>
        <br><br>
        <p>{{ $signature }}</p>
    </div>
    @endif
    
    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem {{ config('app.name') }}
    </div>
</body>
</html>
