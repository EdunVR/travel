<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Akses Kamera & Mikrofon</title>
  <style>
    body {
      font-family: sans-serif;
      text-align: center;
      padding: 2rem;
      background-color: #f4f4f4;
    }
    video {
      width: 640px;
      height: 480px;
      background: black;
      border-radius: 8px;
      margin-top: 20px;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <h1>Aktifkan Kamera & Mikrofon</h1>
  <button onclick="startMedia()">Mulai</button>
  <video id="video" autoplay playsinline></video>

  <script>
    async function startMedia() {
      try {
        const constraints = {
          video: true,
          audio: true
        };
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoElement = document.getElementById('video');
        videoElement.srcObject = stream;
        console.log('Kamera & mikrofon aktif.');
      } catch (err) {
        console.error('Gagal mengakses kamera/mikrofon:', err);
        alert('Izin ditolak atau perangkat tidak ditemukan.');
      }
    }
  </script>
</body>
</html>
