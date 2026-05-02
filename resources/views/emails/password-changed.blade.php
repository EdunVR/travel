<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Berhasil Direset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .success-box {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-radius: 0 0 10px 10px;
        }
        .warning {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Password Berhasil Direset</h1>
        <p>HM Tour & Travel</p>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $affiliator->full_name }}</strong>,</p>
        
        <div class="success-box">
            <strong>✓ Password Anda telah berhasil direset!</strong>
        </div>
        
        <p>Anda sekarang dapat login ke dashboard mitra menggunakan password baru Anda.</p>
        
        <center>
            <a href="{{ route('affiliate.login') }}" class="button">Login Sekarang</a>
        </center>
        
        <div class="warning">
            <strong>⚠️ Perhatian:</strong>
            <p style="margin: 10px 0;">
                Jika Anda <strong>TIDAK</strong> melakukan perubahan password ini, segera hubungi kami melalui WhatsApp di <strong>0897-6688-800</strong> untuk keamanan akun Anda.
            </p>
        </div>
        
        <p><strong>Tips Keamanan:</strong></p>
        <ul>
            <li>Gunakan password yang kuat dan unik</li>
            <li>Jangan bagikan password Anda kepada siapapun</li>
            <li>Ganti password secara berkala</li>
        </ul>
        
        <p>Terima kasih,<br>
        <strong>Tim HM Tour & Travel</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} HM Tour & Travel. All rights reserved.</p>
    </div>
</body>
</html>
