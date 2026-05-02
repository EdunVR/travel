<style>
    .info-window-content {
        max-width: 350px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        padding: 5px 15px 15px 15px;
    }
    
    .info-window-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0 10px 0;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .info-window-title {
        display: flex;
        align-items: center;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        gap: 8px;
    }
    
    .info-window-status {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .info-window-section {
        margin-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 12px;
    }
    
    .info-window-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #3498db;
        margin-bottom: 8px;
        font-size: 0.95rem;
        gap: 8px;
    }
    
    .section-content {
        padding-left: 10px;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 6px;
        font-size: 0.9rem;
        align-items: center;
    }
    
    .info-label {
        display: flex;
        align-items: center;
        width: 150px;
        color: #7f8c8d;
        font-weight: 500;
        gap: 6px;
    }
    
    .info-value {
        flex: 1;
        color: #34495e;
        font-weight: 400;
    }
    
    .info-window-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 10px;
        border-top: 1px solid #eee;
        gap: 10px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 4px;
        gap: 6px;
        flex: 1;
    }
    
    .feather-icon {
        width: 16px;
        height: 16px;
        stroke-width: 2;
    }
    
    .gm-style .gm-style-iw-c {
        padding: 0 !important;
        border-radius: 8px !important;
        max-width: 400px !important;
    }
    
    .gm-style .gm-style-iw-d {
        overflow: auto !important;
        padding: 0 !important;
    }

    body {
        background-color: #f8fafc;
    }
    
    /* Enhanced Card Design */
    .card {
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.12);
    }
    
    /* Map Control Buttons */
    .map-control-container {
        position: absolute;
        top: 180px;
        right: 10px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .map-control-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px !important;
        background: white;
        border: 1px solid #eaeaea;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .map-control-btn:hover {
        background: #f8f9fa;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    /* Button Enhancements */
    .btn {
        transition: all 0.2s ease;
    }
    
    .btn-light {
        background-color: #fff;
        border-color: #eaeaea;
    }
    
    /* Input Group Enhancements */
    .input-group-text {
        transition: all 0.2s ease;
    }
    
    /* Modern Card Design */
    .card {
        border: none;
        overflow: hidden;
    }
    
    /* Header Styling */
    .card-header {
        padding: 1rem 1.5rem;
    }
    
    /* Improved Card Header Layout */
    .card-header-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .card-header-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .card-header-title {
        flex: 1;
        min-width: 0;
    }
    
    .card-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    /* Loading Overlay */
    .map-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    /* Footer Styling */
    .card-footer {
        padding: 0.75rem 1.5rem;
    }
    
    /* Legend Styling */
    .legend {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-right: 1rem;
    }
    
    .legend-color {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    .legend-text {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    /* Input Group Enhancements */
    .input-group {
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border-radius: 8px !important;
    }
    
    .input-group-text {
        background-color: #f8fafc;
        border: none;
        padding: 0.25rem 0.75rem;
    }
    
    .form-control {
        background-color: #f8fafc;
        border: none;
        font-size: 0.85rem;
    }
    
    /* Button Enhancements */
    .btn-outline-primary {
        border-color: #e0e6ed;
        transition: all 0.2s ease;
    }
    
    .btn-outline-primary:hover {
        background-color: #f8fafc;
    }

    @media (min-width: 769px) {
        #allProspekMap {
            height: 60vh !important;
            min-height: 500px;
        }
        
        .card-header {
            padding: 1rem 1.5rem !important;
        }
        
        .card-footer {
            padding: 0.75rem 1.5rem !important;
        }
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        /* Reset body and container */
        body {
            padding: 0 !important;
            margin: 0 !important;
            overflow-x: hidden;
        }
        
        .container-fluid {
            padding: 0;
            margin: 0;
        }
        
        /* Simplify card styling */
        .card {
            border-radius: 0 !important;
            box-shadow: none !important;
            margin: 0;
            border: none;
        }
        
        /* Ultra-compact header */
        .card-header {
            padding: 8px 10px !important;
            background: white;
            border-bottom: 1px solid #eee;
            position: static; /* Remove fixed positioning */
        }
        
        .card-header-top {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        
        /* Smaller back button */
        .card-header-top .btn {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
        
        /* Compact title */
        .card-header-title h5 {
            font-size: 0.9rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        
        .card-header-title p {
            display: none;
        }
        
        /* Smaller action buttons */
        .card-header-actions {
            gap: 6px;
        }
        
        .card-header-actions .btn {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
        
        .card-header-actions .input-group {
            width: 120px !important;
        }
        
        .card-header-actions .input-group-text,
        .card-header-actions .form-control,
        .card-header-actions select {
            padding: 4px 6px;
            font-size: 0.7rem;
            height: 28px;
        }
        
        #allProspekMap {
            height: calc(100vh - 450px) !important; /* 50px for header */
            min-height: 50px;
            width: 100%;
            position: relative;
            top: 0;
            left: 0;
            border-radius: 0 !important;
        }
        
        
        /* Simplify card body */
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Smaller map controls */
        .map-control-container {
            top: 60px !important;
            right: 5px !important;
        }
        
        .map-control-btn {
            width: 28px !important;
            height: 28px !important;
        }
        
        /* Compact footer */
        .card-footer {
            padding: 6px 10px !important;
            font-size: 0.7rem;
            position: static; /* Remove fixed positioning */
            border-top: 1px solid #eee;
        }
        
        .legend {
            justify-content: space-between;
            gap: 5px;
        }
        
        .legend-item {
            margin: 0;
        }
        
        .legend-text {
            font-size: 0.6rem;
        }
        
        /* Remove hover effects */
        .card:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }

    /* Very small devices */
    @media (max-width: 400px) {
        .card-header-title h5 {
            max-width: 100px;
        }
        
        .card-header-actions .input-group {
            width: 100px !important;
        }

        #allProspekMap {
            height: calc(100vh - 450px) !important; /* 50px for header */
            min-height: 50px;
            width: 100%;
            position: relative;
            top: 0;
            left: 0;
            border-radius: 0 !important;
        }
        
    }

    html, body {
        overflow: hidden;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body.map-page {
        overflow: hidden;
        height: 100vh;
    }
    
    .map-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
    }
    
    /* Perbaikan untuk header dan kontrol */
    .map-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 15px;
    }
    
    .map-controls {
        position: fixed;
        top: 80px;
        right: 15px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .legend-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-right: 6px;
        background-size: contain;
        background-repeat: no-repeat;
        vertical-align: middle;
    }
    
    /* Remove old legend color styles */
    .legend-color {
        display: none !important;
    }
	/* Search Box Style */
    .search-box-container {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        width: 50%;
        min-width: 400px;
        max-width: 600px;
    }
    
    .search-box {
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        border-radius: 24px;
        overflow: hidden;
        background: white;
        display: flex;
    }
    
    #locationSearch {
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        flex-grow: 1;
    }
    
    #locationSearch:focus {
        outline: none;
    }
    
    #saveLocationBtn {
        border: none;
        background: #4285F4;
        color: white;
        padding: 0 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    #saveLocationBtn:hover {
        background: #3367D6;
    }
    
    .pac-container {
        z-index: 1051 !important;
        border-radius: 0 0 8px 8px;
        margin-top: -5px;
    }

	.highlight-marker {
        z-index: 1000;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }


</style>

@extends('app')

@section('title', 'Peta Keseluruhan Prospek')

@section('content')
<div class="container-fluid px-lg-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Enhanced Card Design -->
            <div class="card border-0 shadow-lg" style="border-radius: 12px;">
                <!-- Modern Card Header -->
                <div class="card-header px-4 py-3" 
                     style="background: white; border-top-left-radius: 12px; border-top-right-radius: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                     
                    <div class="card-header-container">
                        <div class="card-header-top">
                            <a href="{{ route('prospek.index') }}" class="btn btn-danger">
                                Kembali
                            </a>
                            <div class="card-header-title">
                                <div class="d-flex align-items-center">
                                    <i data-feather="map" class="feather-lg text-primary mr-3" style="width: 28px; height: 28px;"></i>
                                    <div>
                                        <h5 class="m-0 font-weight-bold text-gray-800">Peta Keseluruhan Prospek</h5>
                                        <p class="m-0 text-muted small" style="font-size: 0.85rem;">Visualisasi geografis seluruh prospek</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-header-actions">
                                <!-- Location Button -->
                                <button id="btnFindMe" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center px-3">
                                    <i data-feather="map-pin" class="feather-sm mr-2" style="width: 16px; height: 16px;"></i>
                                    <span>Lokasi Saya</span>
                                </button>
                                
                                <!-- Radius Selector -->
                                <div class="input-group input-group-sm shadow-sm" style="width: 220px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 text-muted small px-3">
                                            <i data-feather="maximize-2" class="feather-sm mr-1"></i> Radius
                                        </span>
                                    </div>
                                    <select id="radiusSelect" class="form-control border-0 bg-light">
                                        <option value="1">1 km</option>
                                        <option value="5" selected>5 km</option>
                                        <option value="10">10 km</option>
                                        <option value="25">25 km</option>
                                        <option value="50">50 km</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button id="btnFindNearest" class="btn btn-primary border-0" type="button">
                                            <i data-feather="filter" class="feather-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Body with Map Container -->
                <div class="card-body p-0 position-relative">
                    <div class="search-box-container">
                        <div class="search-box">
                            <input type="text" id="locationSearch" class="form-control" placeholder="Cari lokasi...">
                            <button id="saveLocationBtn" type="button">
                                <i data-feather="save"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Map Loading Indicator -->
                    <div id="mapLoading" class="map-loading-overlay">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="sr-only">Memuat peta...</span>
                        </div>
                    </div>
                    
                    <!-- Map Controls Container -->
                    <div id="mapControls" class="map-control-container">
                        <!-- Controls will be added here by JavaScript -->
                    </div>
                    
                    <!-- Map Container -->
                    <div id="allProspekMap"></div>
                </div>

                
                <!-- Modern Card Footer -->
                <div class="card-footer px-4 py-3 bg-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <i data-feather="info" class="feather-sm text-muted mr-2"></i>
                            <span class="text-muted small">
                                Menampilkan <span id="prospekCount" class="font-weight-bold text-gray-800">0</span> prospek
                                dalam radius <span id="currentRadius" class="font-weight-bold text-gray-800">5</span> km
                            </span>
                        </div>
                        
                        <div class="legend d-flex flex-wrap justify-content-center">
                            <div class="legend-item prospek mr-3 mb-1 mb-md-0">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_prospek.png') }}')"></span>
                                <span class="legend-text small">Prospek</span>
                            </div>
                            <div class="legend-item followup mr-3 mb-1 mb-md-0">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_followup.png') }}')"></span>
                                <span class="legend-text small">Follow Up</span>
                            </div>
                            <div class="legend-item negosiasi mr-3 mb-1 mb-md-0">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_negosiasi.png') }}')"></span>
                                <span class="legend-text small">Negosiasi</span>
                            </div>
                            <div class="legend-item deposit mr-3 mb-1 mb-md-0">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_deposit.png') }}')"></span>
                                <span class="legend-text small">Deposit</span>
                            </div>
                            <div class="legend-item closing mr-3 mb-1 mb-md-0">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_closing.png') }}')"></span>
                                <span class="legend-text small">Closing</span>
                            </div>
                            <div class="legend-item gagal">
                                <span class="legend-icon" style="background-image: url('{{ asset('img/map_gagal.png') }}')"></span>
                                <span class="legend-text small">Gagal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pastikan Feather Icons diinisialisasi setelah konten dimuat
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    // Atau untuk info window yang dimuat secara dinamis:
    function initInfoWindow() {
        feather.replace({
            selector: '.feather-icon'
        });
    }
    // Variabel global
    var map;
    var markers = [];
    var infoWindows = [];
    var userMarker = null;
    var userCircle = null;
    var activeMarker = null;
    var activeInfoWindow = null;
	var mapUmumMarkers = [];
	var searchBox = null;



    // Fungsi utama untuk inisialisasi peta
    function initMap() {
        $('#mapLoading').show();
        $('[data-toggle="tooltip"]').tooltip();

        setTimeout(function() {
            var prospeks = @json($prospeks);
        
            // Cek apakah ada prospek dengan koordinat valid
            var hasValidProspeks = prospeks.some(prospek => 
                prospek.latitude && prospek.longitude && 
                !isNaN(prospek.latitude) && !isNaN(prospek.longitude)
            );

            // Default center (gunakan lokasi pertama jika ada, atau Jakarta jika tidak)
            var defaultCenter = hasValidProspeks ? 
                { 
                    lat: parseFloat(prospeks[0].latitude), 
                    lng: parseFloat(prospeks[0].longitude) 
                } : 
                { lat: -6.2088, lng: 106.8456 }; // Jak

            // Inisialisasi peta
            map = new google.maps.Map(document.getElementById('allProspekMap'), {
                center: defaultCenter,
                zoom: 10,
                mapTypeId: 'roadmap',
                styles: [
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#444444"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#f2f2f2"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "all",
                        "stylers": [
                            {
                                "saturation": -100
                            },
                            {
                                "lightness": 45
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "simplified"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#d4e6f7"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    }
                ]
            });
            
            // Sembunyikan loading indicator
            $('#mapLoading').hide();
            addMapControls();

            // Buat marker untuk setiap prospek
            createProspekMarkers(prospeks);
		initSearchBox();
		$('#saveLocationBtn').click(saveSearchedLocation);
		loadMapUmumMarkers();

            
             // Jika ada marker, zoom ke bounds semua marker
            if (markers.length > 0) {
                zoomToMarkers();
            }
            
            // Update prospek count
            updateProspekCount();  
        }, 500);
        
    }

    // Fungsi baru untuk zoom ke semua marker
    function zoomToMarkers() {
        if (markers.length === 0) return;
        
        // Jika hanya ada 1 marker, zoom ke level 14
        if (markers.length === 1) {
            map.setCenter(markers[0].getPosition());
            map.setZoom(14);
            return;
        }
        
        // Untuk multiple marker, hitung bounds
        var bounds = new google.maps.LatLngBounds();
        markers.forEach(function(marker) {
            bounds.extend(marker.getPosition());
        });
        
        // Berikan sedikit padding
        map.fitBounds(bounds, {
            top: 50,    // padding atas
            right: 50,  // padding kanan
            bottom: 50, // padding bawah
            left: 50    // padding kiri
        });
        
        // Batasi zoom level maksimal untuk area yang sangat kecil
        var minZoomLevel = 10;
        google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
            if (map.getZoom() > minZoomLevel) {
                map.setZoom(minZoomLevel);
            }
        });
    }

    // Fungsi untuk menambahkan kontrol di dalam peta
    function addMapControls() {
        const controlsContainer = document.getElementById('mapControls');
        
        // Buat kontrol reset
        const resetControl = createControlButton("Reset Peta", "refresh-cw", () => {
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            } else {
                map.setCenter({lat: -6.2088, lng: 106.8456});
                map.setZoom(10);
            }
        });
        
        // Buat kontrol zoom in
        const zoomInControl = createControlButton("Zoom In", "plus", () => {
            map.setZoom(map.getZoom() + 1);
        });
        
        // Buat kontrol zoom out
        const zoomOutControl = createControlButton("Zoom Out", "minus", () => {
            map.setZoom(map.getZoom() - 1);
        });
        
        // Tambahkan kontrol ke container
        controlsContainer.appendChild(resetControl);
        controlsContainer.appendChild(zoomInControl);
        controlsContainer.appendChild(zoomOutControl);
        
        // Inisialisasi ulang feather icons untuk elemen baru
        feather.replace();
    }

    // Fungsi pembantu untuk membuat tombol kontrol
    function createControlButton(title, iconName, clickHandler) {
        const controlButton = document.createElement("button");
        controlButton.title = title;
        controlButton.className = "map-control-btn";
        
        const icon = document.createElement("i");
        icon.setAttribute("data-feather", iconName);
        icon.className = "feather-sm";
        icon.style.width = "16px";
        icon.style.height = "16px";
        
        controlButton.appendChild(icon);
        controlButton.addEventListener("click", clickHandler);
        
        return controlButton;
    }   

    // Fungsi untuk mengupdate jumlah prospek
    function updateProspekCount() {
        var count = markers.length;
        var radius = $('#radiusSelect').val();
        $('#prospekCount').text(count);
        $('#currentRadius').text(radius);
    }

    function createProspekMarkers(prospeks) {
        if (!prospeks || !Array.isArray(prospeks)) {
            console.error('Data prospek tidak valid');
            return;
        }

        prospeks.forEach(function(prospek) {
            if (!prospek.latitude || !prospek.longitude || 
                isNaN(prospek.latitude) || isNaN(prospek.longitude)) {
                console.warn('Koordinat tidak valid untuk prospek:', prospek.nama);
                return;
            }

            var position = new google.maps.LatLng(
                parseFloat(prospek.latitude),
                parseFloat(prospek.longitude)
            );

            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: generateKode(prospek) + ' - ' + prospek.nama,
                icon: getStatusMarkerIcon(prospek.current_status)
            });

            // Simpan data prospek di marker
            marker.prospekData = prospek;

            if (markers.length > 0) {
                zoomToMarkers();
            }

            // Buat info window
            var infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(prospek)
            });

            // Event click untuk marker
            marker.addListener('click', function() {
                // Jika marker yang sama diklik, toggle infoWindow
                if (activeMarker === marker) {
                    if (activeInfoWindow) {
                        activeInfoWindow.close();
                        activeInfoWindow = null;
                        activeMarker = null;
                    } else {
                        infoWindow.open(map, marker);
                        activeInfoWindow = infoWindow;
                        activeMarker = marker;
                    }
                } else {
                    // Tutup infoWindow yang sedang aktif jika ada
                    if (activeInfoWindow) {
                        activeInfoWindow.close();
                    }
                    
                    // Buka infoWindow untuk marker ini
                    infoWindow.open(map, marker);
                    activeInfoWindow = infoWindow;
                    activeMarker = marker;
                }
                
                // Inisialisasi feather icons di dalam info window
                initInfoWindow();
            });

            markers.push(marker);
            infoWindows.push(infoWindow);
        });
    }


    function generateKode(prospek) {
        // Generate kode similar to what we did in the index view
        var jenisParts = (prospek.jenis || '').split(' ');
        var jenisCode = '';
        var urutCode = (prospek.id_prospek || 0).toString().padStart(4, '0');
        jenisParts.forEach(function(part) {
            if (part) {
                jenisCode += part[0].toUpperCase();
            }
        });
        
        var statusCode = (prospek.current_status || '')[0].toUpperCase();
        
        // Note: Since we don't have the counter here, we'll just use 001
        // If you need the exact same counter as index view, you'll need to pass it from controller
        return jenisCode + '-' + statusCode + '-00' + urutCode;
    }

    // Fungsi untuk mendapatkan icon marker berdasarkan status
    function getStatusMarkerIcon(status) {
        const baseUrl = window.baseUrl;
        const iconBaseUrl = baseUrl + '/img/'; // Path ke folder public/img
        
        const icons = {
            'prospek': 'map_prospek.png',
            'followup': 'map_followup.png',
            'negosiasi': 'map_negosiasi.png',
            'deposit': 'map_deposit.png',
            'closing': 'map_closing.png',
            'gagal': 'map_gagal.png'
        };
        
        return {
            url: iconBaseUrl + (icons[status] || 'map_prospek.png'),
            scaledSize: new google.maps.Size(24, 24),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(12, 24)
        };
    }

    // Fungsi untuk menemukan lokasi user dengan custom icon
    function findMyLocation() {
        const baseUrl = window.baseUrl;
        const iconBaseUrl = baseUrl + '/img/'; // Path ke folder public/img
        if (!navigator.geolocation) {
            alert("Browser Anda tidak mendukung geolocation");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Hapus marker dan circle lama
                if (userMarker) userMarker.setMap(null);
                if (userCircle) userCircle.setMap(null);

                // Buat marker user dengan custom icon
                userMarker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: 'Lokasi Anda',
                    icon: {
                        url: iconBaseUrl + 'map_my.png',
                        scaledSize: new google.maps.Size(24, 24),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(12, 24)
                    }
                });

                // Buat radius circle
                var radius = parseInt($('#radiusSelect').val()) * 1000;
                userCircle = new google.maps.Circle({
                    strokeColor: '#4285F4',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#4285F4',
                    fillOpacity: 0.2,
                    map: map,
                    center: pos,
                    radius: radius
                });

                // Pusatkan peta
                map.setCenter(pos);
                map.setZoom(14);

                // Highlight prospek terdekat
                highlightProspeksInRadius(pos, radius);
            },
            function(error) {
                alert('Error: ' + error.message);
            }
        );
    }

    // Fungsi untuk mencari prospek terdekat
    function findNearestProspeks() {
        if (!userMarker) {
            alert('Silakan klik "Posisi Saya" terlebih dahulu');
            return;
        }

        var pos = userMarker.getPosition();
        var radius = parseInt($('#radiusSelect').val()) * 1000;

        if (userCircle) {
            userCircle.setRadius(radius);
        }

        highlightProspeksInRadius(pos, radius);
    }

    // Fungsi untuk highlight prospek dalam radius
    function highlightProspeksInRadius(center, radius) {
        markers.forEach(function(marker) {
            var distance = google.maps.geometry.spherical.computeDistanceBetween(
                center, 
                marker.getPosition()
            );

            if (distance <= radius) {
                marker.setIcon(getHighlightedMarkerIcon(marker.customStatus));
                marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => marker.setAnimation(null), 1500);
            } else {
                marker.setIcon(getMarkerIcon(marker.customStatus));
            }
        });
    }

    function createInfoWindowContent(prospek) {
        const baseUrl = window.baseUrl;
        const defaultPhoto = baseUrl + '/public/img/logo-dahana.png';
        const photoUrl = prospek.photo ? baseUrl + '/' + prospek.photo : baseUrl + '/img/logo-dahana.png';

        // Format tanggal Indonesia
        function formatTanggal(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        return `
            <div class="info-window-content">
                <!-- Header dengan padding atas -->
                <div class="info-window-header" style="padding-top: 15px;">
                    <div class="info-window-title">
                        <i data-feather="user" class="feather-icon"></i>
                        <span>${generateKode(prospek) + ' - ' + (prospek.nama ? prospek.nama : 'Nama tidak tersedia')}</span>
                    </div>

                <div class="info-window-status ${getStatusClass(prospek.current_status)}">
                    ${getStatusText(prospek.current_status)}
                </div>
            </div>
            
            <!-- Foto Prospek -->
            <div class="info-window-section">
                <div class="section-title">
                    <i data-feather="image" class="feather-icon"></i>
                    <span>Foto Prospek</span>
                </div>
                <div class="section-content text-center">
                    <img src="${photoUrl}" alt="Foto Prospek" style="max-width: 100%; max-height: 150px; border-radius: 4px;">
                </div>
            </div>
            
            <!-- Informasi Dasar -->
            <div class="info-window-section">
                <div class="section-title">
                    <i data-feather="info" class="feather-icon"></i>
                    <span>Informasi Dasar</span>
                </div>
                <div class="section-content">
                    <div class="info-row">
                        <span class="info-label"><i data-feather="calendar" class="feather-icon"></i> Tanggal Prospek:</span>
                        <span class="info-value">${formatTanggal(prospek.tanggal)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="home" class="feather-icon"></i> Perusahaan:</span>
                        <span class="info-value">${prospek.nama_perusahaan || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="tag" class="feather-icon"></i> Jenis Usaha:</span>
                        <span class="info-value">${prospek.jenis || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="phone" class="feather-icon"></i> Telepon:</span>
                        <span class="info-value">${prospek.telepon || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="mail" class="feather-icon"></i> Email:</span>
                        <span class="info-value">${prospek.email || '-'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Alamat -->
            <div class="info-window-section">
                <div class="section-title">
                    <i data-feather="map-pin" class="feather-icon"></i>
                    <span>Alamat</span>
                </div>
                <div class="section-content">
                    <div class="info-row">
                        <span class="info-label"><i data-feather="map" class="feather-icon"></i> Jalan:</span>
                        <span class="info-value">${prospek.alamat || '-'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Perusahaan -->
            <div class="info-window-section">
                <div class="section-title">
                    <i data-feather="briefcase" class="feather-icon"></i>
                    <span>Detail Perusahaan</span>
                </div>
                <div class="section-content">
                    <div class="info-row">
                        <span class="info-label"><i data-feather="user-check" class="feather-icon"></i> Pemilik/Manager:</span>
                        <span class="info-value">${prospek.pemilik_manager || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="zap" class="feather-icon"></i> Boiler:</span>
                        <span class="info-value">
                            <span class="badge ${prospek.menggunakan_boiler ? 'badge-success' : 'badge-danger'}">
                                ${prospek.menggunakan_boiler ? 'Ya' : 'Tidak'}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="bar-chart-2" class="feather-icon"></i> Kapasitas Produksi:</span>
                        <span class="info-value">${prospek.kapasitas_produksi || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="settings" class="feather-icon"></i> Sistem Produksi:</span>
                        <span class="info-value">${prospek.sistem_produksi || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="zap" class="feather-icon"></i> Bahan Bakar:</span>
                        <span class="info-value">${prospek.bahan_bakar || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="zap" class="feather-icon"></i> Informasi:</span>
                        <span class="info-value">${prospek.informasi_perusahaan || '-'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="info-window-section">
                <div class="section-title">
                    <i data-feather="more-horizontal" class="feather-icon"></i>
                    <span>Informasi Tambahan</span>
                </div>
                <div class="section-content">
                    <div class="info-row">
                        <span class="info-label"><i data-feather="users" class="feather-icon"></i> Petugas:</span>
                        <span class="info-value">${prospek.recruitment?.name || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i data-feather="calendar" class="feather-icon"></i> Dibuat Pada:</span>
                        <span class="info-value">${new Date(prospek.created_at).toLocaleDateString('id-ID') || '-'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="info-window-actions">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${prospek.latitude},${prospek.longitude}&travelmode=driving" 
                target="_blank" class="btn btn-sm btn-primary action-btn">
                    <i data-feather="navigation" class="feather-icon"></i> Navigasi
                </a>
                <a href="${baseUrl}/crm/prospek/${prospek.id_prospek}/edit" class="btn btn-sm btn-warning action-btn">
                    <i data-feather="edit" class="feather-icon"></i> Edit
                </a>
                <button onclick="closeInfoWindow()" class="btn btn-sm btn-secondary action-btn">
                    <i data-feather="x" class="feather-icon"></i> Tutup
                </button>
            </div>
        </div>
    `;
}

    // // Fungsi helper untuk marker icon
    // function getMarkerIcon(status) {
    //     const colors = {
    //         'prospek': 'yellow',
    //         'followup': 'blue',
    //         'negosiasi': 'orange',
    //         'closing': 'green',
    //         'deposit': 'purple',
    //         'gagal': 'red'
    //     };
    //     return `http://maps.google.com/mapfiles/ms/icons/${colors[status] || 'yellow'}-dot.png`;
    // }

    function getCustomMarkerIcon(status, menggunakanBoiler) {
        // Warna berdasarkan status
        const colors = {
            'prospek': '#FFC107', // Kuning
            'followup': '#17A2B8', // Biru
            'negosiasi': '#FD7E14', // Orange
            'closing': '#28A745', // Hijau
            'deposit': '#6F42C1', // Ungu
            'gagal': '#DC3545' // Merah
        };
        
        const color = colors[status] || '#FFC107';
        
        // Bentuk SVG untuk marker
        let svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="40" viewBox="0 0 24 40">
                <path fill="${color}" d="M12 0C5.373 0 0 5.373 0 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.342-3.369-1.342-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C21.138 20.163 24 16.418 24 12c0-6.627-5.373-12-12-12z"/>
                ${menggunakanBoiler ? 
                    '<circle cx="12" cy="12" r="6" fill="#FFFFFF" stroke="#000000" stroke-width="1"/>' : 
                    '<path fill="#FFFFFF" d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>'
                }
            </svg>
        `;
        
        // Konversi SVG ke URL data
        const svgUrl = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
        
        return {
            url: svgUrl,
            scaledSize: new google.maps.Size(30, 40),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(12, 40)
        };
    }

    function getHighlightedMarkerIcon(status) {
        const colors = {
            'prospek': 'gray',
            'followup': 'blue',
            'negosiasi': 'orange',
            'closing': 'green',
            'deposit': 'purple',
            'gagal': 'red'
        };
        return `http://maps.google.com/mapfiles/ms/icons/${colors[status] || 'gray'}.png`;
    }

    function getStatusClass(status) {
        const classes = {
            'prospek': 'bg-secondary',
            'followup': 'bg-info',
            'negosiasi': 'bg-warning',
            'closing': 'bg-success',
            'deposit': 'bg-primary',
            'gagal': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    function getStatusText(status) {
        const statuses = {
            'prospek': 'Prospek',
            'followup': 'Follow Up',
            'negosiasi': 'Negosiasi',
            'closing': 'Closing',
            'deposit': 'Deposit',
            'gagal': 'Gagal'
        };
        return statuses[status] || status;
    }

    function closeInfoWindow() {
        if (activeInfoWindow) {
            activeInfoWindow.close();
            activeInfoWindow = null;
            activeMarker = null;
        }
    }

    // Load Google Maps API secara asynchronous
    function loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (window.google?.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&libraries=places,geometry&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        });
    }

    // Inisialisasi aplikasi
    $(document).ready(function() {
        feather.replace();
        loadGoogleMapsAPI();
        $('#radiusSelect').change(updateProspekCount);
        
        // Event listeners
        $('#btnFindMe').click(findMyLocation);
        $('#btnFindNearest').click(findNearestProspeks);
    });

	function initSearchBox() {
        var input = document.getElementById('locationSearch');
        searchBox = new google.maps.places.SearchBox(input);
        
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });
        
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();
            
            if (places.length == 0) return;
            
            // Hanya hapus temporary search marker sebelumnya (jika ada)
            if (window.searchMarker) {
                window.searchMarker.setMap(null);
            }
            
            // Buat marker untuk lokasi yang dicari
            var place = places[0];
            if (!place.geometry) return;
            
            window.searchMarker = new google.maps.Marker({
                map: map,
                title: place.name,
                position: place.geometry.location,
                icon: {
                    url: window.baseUrl + '/img/map_search_temp.png',
                    scaledSize: new google.maps.Size(32, 32)
                },
                animation: google.maps.Animation.DROP
            });
            
            // Simpan data tempat untuk disimpan
            window.searchMarker.tempPlaceData = {
                name: place.name,
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng()
            };
            
            // Zoom ke lokasi yang dicari (tanpa mengubah marker lain)
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
        });
    }

    function saveSearchedLocation() {
        if (!window.searchMarker || !window.searchMarker.tempPlaceData) {
            alert('Silakan cari lokasi terlebih dahulu');
            return;
        }
        
        var placeData = window.searchMarker.tempPlaceData;
        
        Swal.fire({
            title: 'Simpan Lokasi?',
            html: `Anda akan menyimpan lokasi:<br><strong>${placeData.name}</strong>`,
            input: 'text',
            inputLabel: 'Nama Lokasi (opsional)',
            inputPlaceholder: placeData.name,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            preConfirm: (namaLokasi) => {
                return $.ajax({
                    url: '{{ route("prospek.searchAndSave") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        query: document.getElementById('locationSearch').value,
                        latitude: placeData.lat,
                        longitude: placeData.lng,
                        nama_lokasi: namaLokasi || placeData.name
                    }
                }).then(response => {
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(
                        `Gagal menyimpan: ${error.responseJSON.message}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Berhasil!',
                    'Lokasi telah disimpan.',
                    'success'
                );
                // Hapus temporary search marker
                if (window.searchMarker) {
                    window.searchMarker.setMap(null);
                }
                // Tambahkan marker baru ke mapUmumMarkers
                loadMapUmumMarkers();
            }
        });
    }

	function createMapUmumInfoContent(location) {
        const baseUrl = window.baseUrl;
        const defaultPhoto = baseUrl + '/public/img/logo-dahana.png';

        return `
            <div class="info-window-content">
                <!-- Header dengan padding atas -->
                <div class="info-window-header" style="padding-top: 15px;">
                    <div class="info-window-title">
                        <i data-feather="map-pin" class="feather-icon"></i>
                        <span>${location.nama_perusahaan || 'Lokasi Umum'}</span>
                    </div>
                    <div class="info-window-status bg-secondary">
                        ${location.tipe || 'Umum'}
                    </div>
                </div>
                
                <!-- Informasi Dasar -->
                <div class="info-window-section">
                    <div class="section-title">
                        <i data-feather="info" class="feather-icon"></i>
                        <span>Informasi Lokasi</span>
                    </div>
                    <div class="section-content">
                        <div class="info-row">
                            <span class="info-label"><i data-feather="map-pin" class="feather-icon"></i> Tipe:</span>
                            <span class="info-value">${location.tipe || '-'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i data-feather="calendar" class="feather-icon"></i> Dibuat:</span>
                            <span class="info-value">${new Date(location.created_at) || '-'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i data-feather="map" class="feather-icon"></i> Koordinat:</span>
                            <span class="info-value">${location.latitude}, ${location.longitude}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Aksi -->
                <div class="info-window-actions">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}&travelmode=driving" 
                    target="_blank" class="btn btn-sm btn-primary action-btn">
                        <i data-feather="navigation" class="feather-icon"></i> Navigasi
                    </a>
                    <button onclick="deleteMapUmumLocation(${location.id})" class="btn btn-sm btn-danger action-btn">
                        <i data-feather="trash-2" class="feather-icon"></i> Hapus
                    </a>
                    <button onclick="closeInfoWindow()" class="btn btn-sm btn-secondary action-btn">
                        <i data-feather="x" class="feather-icon"></i> Tutup
                    </button>
                </div>
            </div>
        `;
    }

    function deleteMapUmumLocation(id) {
        Swal.fire({
            title: 'Hapus Lokasi?',
            text: "Anda tidak dapat mengembalikan data yang telah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/map-umum/' + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Terhapus!',
                            'Lokasi telah dihapus.',
                            'success'
                        );
                        // Muat ulang marker map_umum
                        loadMapUmumMarkers();
                    },
                    error: function(error) {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus.',
                            'error'
                        );
                    }
                });
            }
        });
    }

	function clearTempSearchMarkers() {
        tempSearchMarkers.forEach(function(marker) {
            marker.setMap(null);
        });
        tempSearchMarkers = [];
    }

    function loadMapUmumMarkers() {
        $.get('{{ route("prospek.getLocations") }}', function(data) {
            // Hapus marker map_umum yang lama
            mapUmumMarkers.forEach(function(marker) {
                marker.setMap(null);
            });
            
            // Buat marker baru dari data terbaru
            mapUmumMarkers = [];
            data.map_umum.forEach(function(location) {
                if (!location.latitude || !location.longitude) return;
                
                var position = new google.maps.LatLng(
                    parseFloat(location.latitude),
                    parseFloat(location.longitude)
                );

                var marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: location.nama_perusahaan || 'Lokasi Umum',
                    icon: {
                        url: window.baseUrl + '/img/map_search.png',
                        scaledSize: new google.maps.Size(24, 24)
                    }
                });

                marker.locationData = location;
                
                // Buat info window untuk map_umum
                var infoWindow = new google.maps.InfoWindow({
                    content: createMapUmumInfoContent(location)
                });

                marker.addListener('click', function() {
                    // Jika marker yang sama diklik, toggle infoWindow
                    if (activeMarker === marker) {
                        if (activeInfoWindow) {
                            activeInfoWindow.close();
                            activeInfoWindow = null;
                            activeMarker = null;
                        } else {
                            infoWindow.open(map, marker);
                            activeInfoWindow = infoWindow;
                            activeMarker = marker;
                        }
                    } else {
                        // Tutup infoWindow yang sedang aktif jika ada
                        if (activeInfoWindow) {
                            activeInfoWindow.close();
                        }
                        
                        // Buka infoWindow untuk marker ini
                        infoWindow.open(map, marker);
                        activeInfoWindow = infoWindow;
                        activeMarker = marker;
                    }
                    
                    // Inisialisasi feather icons di dalam info window
                    initInfoWindow();
                });

                mapUmumMarkers.push(marker);
            });
        });
    }
	
	function showExpandedInfo(marker) {
        var content;
        
        if (marker.isProspek) {
            content = createProspekInfoContent(marker.prospekData);
        } else {
            content = createMapUmumInfoContent(marker.locationData);
        }
        
        $('#expandedInfoTitle').text(marker.getTitle());
        $('#expandedInfoContent').html(content);
        
        // Tampilkan expanded info
        $('#expandedInfoContainer').addClass('visible');
        expandedInfoVisible = true;
        
        // Inisialisasi feather icons
        feather.replace();
    }

    function hideExpandedInfo() {
        $('#expandedInfoContainer').removeClass('visible');
        expandedInfoVisible = false;
        
        // Hapus highlight marker
        if (activeMarker) {
            if (activeMarker.isProspek) {
                activeMarker.setIcon(getStatusMarkerIcon(activeMarker.prospekData.current_status));
            } else {
                activeMarker.setIcon({
                    url: window.baseUrl + '/img/map_search.png',
                    scaledSize: new google.maps.Size(24, 24)
                });
            }
            activeMarker = null;
        }
    }

	function highlightMarker(marker) {
        // Hapus highlight dari marker sebelumnya
        if (activeMarker) {
            if (activeMarker.isProspek) {
                activeMarker.setIcon(getStatusMarkerIcon(activeMarker.prospekData.current_status));
            } else {
                activeMarker.setIcon({
                    url: window.baseUrl + '/img/map_search.png',
                    scaledSize: new google.maps.Size(24, 24)
                });
            }
        }
        
        // Highlight marker baru
        marker.setIcon({
            url: window.baseUrl + '/img/map_highlight.png',
            scaledSize: new google.maps.Size(32, 32)
        });
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 1500);
        
        activeMarker = marker;
    }



</script>
@endpush
