<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $title }}</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('favicon.ico') }}" rel="icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('home-assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home-assets/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('home-assets/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('home-assets/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('home-assets/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('home-assets/assets/css/main.css') }}" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top" style="z-index: 9999">
        <div class="container-fluid container-xl d-flex align-items-center">

            <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('img/' . $website->logo) }}" alt="">
                <h1 class="sitename">{{ $website->nama }}</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ url('/') }}#hero" class="active">Home</a></li>
                    <li><a href="{{ url('/') }}#peta-tps">Peta TPS</a></li>
                    <li><a href="{{ url('/login') }}">Login</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </header>



    @yield('content')




    <footer id="footer" class="footer light-background py-5 mt-5">
        <div class="container">
            <div class="row gy-4">
                <!-- Info Column -->
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="{{ url('/') }}" class="logo d-flex align-items-center mb-3">
                        <img src="{{ asset('img/' . $website->logo) }}" alt="Logo"
                            style="max-height: 40px; margin-right: 8px;">
                        <span class="sitename fw-bold text-dark">{{ $website->nama }}</span>
                    </a>
                    <p>{{ $website->deskripsi }}</p>
                </div>

                <!-- Contact Column -->
                <div class="col-lg-4 col-md-6 footer-contact">
                    <h5 class="fw-bold mb-3 text-dark">Hubungi Kami</h5>
                    <p class="mb-2">
                        <i class="bi bi-geo-alt me-2 text-success"></i>
                        {{ $website->alamat }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-telephone me-2 text-success"></i>
                        {{ $website->telepon }}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-envelope me-2 text-success"></i>
                        {{ $website->email }}
                    </p>
                </div>

                <!-- Map Column -->
                <div class="col-lg-4 col-12">
                    <h5 class="fw-bold mb-3 text-dark">Lokasi Kantor</h5>
                    <div id="footer-map" style="height: 180px; border-radius: 8px; border: 1px solid #ddd; z-index: 1;">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="copyright text-center">
                <p>© <span>Copyright</span> <strong class="px-1 sitename">{{ $website->nama }}</strong><span>, Pradilla
                        Dea Ananda. All Rights Reserved</span></p>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('home-assets/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('home-assets/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('home-assets/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('home-assets/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('home-assets/assets/js/main.js') }}"></script>

    <!-- Footer Map Init -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $coords = json_decode($website->koordinat_kantor, true);
                $lat = $coords[0] ?? 3.593147;
                $lng = $coords[1] ?? 98.4879093;
            @endphp

            const footerMap = L.map('footer-map', {
                zoomControl: false,
                attributionControl: false
            }).setView([{{ $lat }}, {{ $lng }}], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(footerMap);
            L.marker([{{ $lat }}, {{ $lng }}]).addTo(footerMap)
                .bindPopup("Kantor Dinas Lingkungan Hidup")
                .openPopup();
        });
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
