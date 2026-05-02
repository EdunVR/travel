<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Streaming Kamera</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        video {
            width: 80%;
            max-width: 800px;
            border: 3px solid #444;
            border-radius: 10px;
            background: black;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🎥 Live Streaming Kamera (Ruang Meeting)</h2>
    <video id="video" controls autoplay playsinline></video>
</div>

<script>
    var video = document.getElementById('video');
    var videoSrc = "{{ asset('stream/index.m3u8') }}?t=" + Date.now();  

    if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc; // Safari
    } else if (Hls.isSupported()) {
        var hls = new Hls({ debug: true });
        hls.loadSource(videoSrc);
        hls.attachMedia(video);

        hls.on(Hls.Events.ERROR, function (event, data) {
            console.error("HLS error:", data);
        });
    } else {
        alert("Browser Anda tidak mendukung HLS.");
    }
</script>
</body>
</html>
