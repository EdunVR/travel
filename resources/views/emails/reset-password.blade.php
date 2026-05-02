<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Reset Password</h1>
        <p>HM Tour & Travel</p>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $affiliator->full_name }}</strong>,</p>
        
        <p>Anda telah meminta reset password untuk akun mitra Anda di HM Tour.</p>
        
        <p>Klik tombol di bawah ini untuk reset password Anda:</p>
        
        <center>
            <a href="{{ $resetLink }}" class="button">Reset Password Sekarang</a>
        </center>
        
        <p>Atau copy dan paste link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background: white; padding: 10px; border: 1px solid #e5e7eb; border-radius: 5px;">
            {{ $resetLink }}
        </p>
        
        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 10px 0;">
                <li>Link ini berlaku selama <strong>1 jam</strong></li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                <li>Jangan bagikan link ini kepada siapapun</li>
            </ul>
        </div>
        
        <p>Jika Anda mengalami kesulitan, silakan hubungi kami melalui WhatsApp di <strong>0897-6688-800</strong></p>
        
        <p>Terima kasih,<br>
        <strong>Tim HM Tour & Travel</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} HM Tour & Travel. All rights reserved.</p>
    </div>
</body>
</html>
