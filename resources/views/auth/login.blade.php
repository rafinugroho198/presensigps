<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>E-Presensi Geolocation</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="__manifest.json">

    {{-- KODE CSS KUSTOM UNTUK BIRU TUA TELAH DIUBAH --}}
    <style>
        body {
            /* Warna Biru Tua (Midnight Blue) */
            background-color: #112f61ff !important; 
        }
        /* Memastikan teks tetap terlihat dengan latar belakang gelap */
        .section h1, .section h4, .text-gray-600 { 
            color: #ffffffff !important; 
        }
    </style>

</head>

<body> 
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <div id="appCapsule" class="pt-0">

        <div class="login-form mt-1">
            <div class="section">
                <img src="{{ asset('assets/img/login/login.png') }}" alt="image" class="form-image">
            </div>
            <div class="section mt-1">
                <h1>E-Presensi</h1>
                <h4>Silahkan Login</h4>
            </div>
            <div class="section mt-1 mb-5">
                @php
                    $messagewarning = Session::get('warning');
                @endphp
                @if (Session::get('warning'))
                    <div class="alert alert-outline-warning">
                        {{ $messagewarning }}
                    </div>
                @endif
                <form action="/proseslogin" method="POST">
                    @csrf
                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="text" name="nip" class="form-control" id="nip" placeholder="NIP">
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password">
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-links mt-2">
                        <div>
                            <a href="{{ route('register') }}">Register Now</a>
                        </div>
                        <div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="font-semibold text-blue-600 hover:text-blue-800">
                            Login Admin</a></div>
                    </div>
                    
        
                    <div class="form-button-group">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
                    </div>

                </form>
            </div>
        </div>


    </div>
    <script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/popper.min.js') }}"></script>
    <script src="assets/js/lib/bootstrap.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/base.js') }}"></script>


</body>

</html>