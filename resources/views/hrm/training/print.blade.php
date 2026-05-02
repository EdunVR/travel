<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flyer Pelatihan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }
        .header p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .content {
            margin-top: 20px;
        }
        .content h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        .content p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <p>Pelatihan dan Pengembangan Karyawan</p>
            <h1>{{ $training->training_name }}</h1>
            <p>Dikeluarkan oleh Departemen Sumber Daya Manusia</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Detail Pelatihan</h2>
            <p><strong>Tanggal Mulai:</strong> {{ $training->start_date }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ $training->end_date }}</p>
            <p><strong>Pelatih:</strong> {{ $training->trainer }}</p>
            <p><strong>Lokasi:</strong> {{ $training->location }}</p>
            <p><strong>Deskripsi:</strong> {{ $training->description }}</p>

            <h2>Peserta</h2>
            <p><strong>Nama Karyawan:</strong> {{ $training->recruitment->name }}</p>
            <p><strong>Posisi:</strong> {{ $training->recruitment->position }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $setting->nama_perusahaan }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
