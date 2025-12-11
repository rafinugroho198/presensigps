@extends('layouts.presensi')

@section('header')
    <div class="appHeader bg-dark text-light border-bottom border-info">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack text-info">
                <ion-icon name="arrow-back-circle-outline" style="font-size: 28px;"></ion-icon>
            </a>
        </div>
        <div class="pageTitle text-info">SISTEM AUTENTIKASI AKTIF</div>
        <div class="right"></div>
    </div>
    <style>
        body {
            background-color: #1a1a2e;
        }
        .appHeader {
            box-shadow: 0 4px 15px rgba(0, 255, 255, 0.2);
        }
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 10px;
            border: 2px solid #00ffff;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.6);
            transform: scaleX(-1);
        }
        #map {
            height: 200px;
            border-radius: 10px;
            border: 1px solid #ff007f;
            filter: grayscale(80%) invert(0.1);
        }
        .btn-futuristic-in {
            background: linear-gradient(45deg, #00ffff, #00aaff);
            color: #1a1a2e;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4);
            transition: all 0.3s ease;
        }
        .btn-futuristic-out {
            background: linear-gradient(45deg, #ff007f, #ff66a3);
            color: #1a1a2e;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(255, 0, 127, 0.4);
            transition: all 0.3s ease;
        }
        .btn-futuristic-in:hover, .btn-futuristic-out:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture"></div>
            <p class="text-center mt-2 text-info small">**Sistem Autentikasi Wajah Aktif**</p>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col">
            @if ($cek > 0)
                <button id="takeabsen" class="btn btn-futuristic-out btn-block p-3">
                    <ion-icon name="exit-outline" style="font-size: 20px; vertical-align: middle;"></ion-icon>
                    TERMINASI SESI (Absen Pulang)
                </button>
            @else
                <button id="takeabsen" class="btn btn-futuristic-in btn-block p-3">
                    <ion-icon name="camera-outline" style="font-size: 20px; vertical-align: middle;"></ion-icon>
                    AKTIVASI SESI (Absen Masuk)
                </button>
            @endif
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col">
            <div id="map"></div>
            <p class="text-center mt-2 text-info small">**Pelacakan Lokasi GEO-FENCE (GPS Aktif)**</p>
        </div>
    </div>
@endsection

@push('myscript')
    {{-- Pastikan jQuery, WebcamJS, dan SweetAlert2 terload --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // === KONFIGURASI KAMERA ===
        Webcam.set({
            height: 480,
            width: 640,
            image_format: 'png',
            jpeg_quality: 80
        });
        Webcam.attach('.webcam-capture');

        // === DETEKSI LOKASI GPS ===
        const lokasiInput = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        }

        function successCallback(position) {
            lokasiInput.value = position.coords.latitude + "," + position.coords.longitude;

            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var userIcon = L.divIcon({
                className: 'user-location-icon',
                html: '<div style="background-color: #00ff7f; width: 15px; height: 15px; border-radius: 50%; border: 3px solid #0d1e2d; box-shadow: 0 0 10px #00ff7f;"></div>',
                iconSize: [25, 25],
                iconAnchor: [12, 12]
            });
            L.marker([position.coords.latitude, position.coords.longitude], {icon: userIcon}).addTo(map)
                .bindPopup('Lokasi Anda Terdeteksi').openPopup();

            L.circle([-6.523987146602277, 106.71530996185996], {
                color: '#ff007f',
                fillColor: '#ff007f',
                fillOpacity: 0.3,
                radius: 20
            }).addTo(map).bindPopup('Zona Kantor (20m)');
        }

        function errorCallback(error) {
            alert("Sinyal Lokasi Tidak Ditemukan! Aktifkan GPS dan Beri Izin Lokasi.");
        }

        // === PROSES ABSEN ===
        $("#takeabsen").click(function(e) {
            e.preventDefault();

            let lokasi = $("#lokasi").val();
            if (lokasi === "") {
                Swal.fire("Lokasi Belum Terdeteksi!", "Aktifkan GPS dan beri izin lokasi.", "warning");
                return;
            }

            // Ambil foto dari kamera
            Webcam.snap(function(uri) {
                $.ajax({
                    type: 'POST',
                    url: '/presensi/store',
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: uri,
                        lokasi: lokasi
                    },
                    cache: false,
                    success: function(respond) {
                        let status = respond.split("|");
                        if (status[0] === "success") {
                            Swal.fire({
                                title: 'Sesi Berhasil!',
                                text: status[1],
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            setTimeout(() => location.href = '/dashboard', 3000);
                        } else {
                            Swal.fire({
                                title: 'Akses Ditolak!',
                                text: status[1],
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi'
                            });
                        }
                    },
                    error: function(err) {
                        Swal.fire("Error", "Gagal mengirim data ke server!", "error");
                    }
                });
            });
        });
    </script>
@endpush
