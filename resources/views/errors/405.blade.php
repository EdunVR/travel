<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 - Method Not Allowed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 20px;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .error-message {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: background 0.3s;
            margin: 5px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🚫</div>
        <div class="error-code">405</div>
        <div class="error-title">Method Not Allowed</div>
        <div class="error-message">
            Maaf, metode HTTP yang Anda gunakan tidak diizinkan untuk halaman ini.<br>
            Silakan gunakan metode yang benar atau kembali ke halaman utama.
        </div>
        <div>
            <a href="{{ route('login') }}" class="btn">Ke Halaman Login</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>
        <div style="margin-top: 20px; font-size: 12px; color: #bdc3c7;">
            Jika masalah ini terus terjadi, silakan hubungi administrator sistem.
        </div>
    </div>

    <script>
        // Auto redirect to login after 10 seconds
        setTimeout(function() {
            window.location.href = '{{ route("login") }}';
        }, 10000);
    </script>
</body>
</html>