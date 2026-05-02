<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 2cm; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .signature { margin-top: 100px; float: right; text-align: center; }
        .footer { margin-top: 50px; font-size: 0.8em; text-align: center; }
        .investor-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Nomor: {{ strtoupper(Str::random(10)) }}</p>
    </div>
    
    <div class="investor-info">
        <p>Nama Investor: <strong>{{ $investor->name }}</strong></p>
        <p>Tanggal: <strong>{{ $date }}</strong></p>
    </div>
    
    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
    
    @if(isset($signature))
    <div class="signature">
        <p>Hormat kami,</p>
        <br><br><br>
        <p><u>{{ $signature }}</u></p>
    </div>
    @endif
    
    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem {{ config('app.name') }}
    </div>
</body>
</html>
