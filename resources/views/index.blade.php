@extends('layouts.template')
@section('content')
    <main class="main">

        <section id="hero" class="hero section">
            <div class="hero-bg">
                <img src="{{ asset('home-assets/assets/img/hero-bg-light.webp') }}" alt="">
            </div>
            <div class="container text-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <h1 data-aos="fade-up">Selamat datang di website <span>{{ $title }}</span></h1>
                    <p data-aos="fade-up" data-aos-delay="100">{{ $website->deskripsi }}<br>
                    </p>
                </div>
            </div>

        </section>


        {{-- Peta Persebaran TPS --}}
        <div id="peta-tps" class="container-fluid">
            <div class="container-xxl">
                <div class="bg-white shadow-sm my-5 p-3 rounded border">
                    <h2 class="poppins fw-bold garis-bawah mb-4">Peta Persebaran TPS</h2>
                    <form id="filter-form" class="align-items-center row">
                        <div class="col-md-3">
                            <input type="search" name="filter-search" id="filter-search" class="form-control"
                                placeholder="Cari TPS...">
                        </div>
                    </form>

                    <br>

                    <div class="row">
                        <div class="col-lg-7">
                            <div id="map" style="height: 400px;"></div>
                        </div>

                        <div class="col-lg-5">
                            <div id="info-panel" class="info-panel bg-light rounded px-3 pt-3"
                                style="max-height: 400px; overflow: auto;">
                                <h4 class="fw-bold poppins mb-3">Lokasi TPS</h4>
                                <div class="info-panel-placeholder">
                                    <p class="mb-0">Hasil filter akan ditampilkan di sini. Gunakan menu filter di atas
                                        untuk
                                        memulai.</p>
                                </div>
                                <div class="results-list" style="cursor: pointer;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- END Peta Persebaran TPS --}}

    </main>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([3.593147, 98.4879093], 13); // Fokus ke Binjai, Sumatera Utara
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const dataTps = @json($data);

            const markerGroup = L.featureGroup().addTo(map);
            const infoPanelPlaceholder = document.querySelector('.info-panel-placeholder');
            const resultsList = document.querySelector('.results-list');

            function stripHtml(html) {
                const div = document.createElement("div");
                div.innerHTML = html;
                return div.textContent || div.innerText || "";
            }

            function applyFilters() {
                markerGroup.clearLayers();
                resultsList.innerHTML = "";

                const searchQuery = document.getElementById('filter-search').value.toLowerCase();

                const filteredData = dataTps.filter(item => {
                    const matchSearch = !searchQuery || item.nama.toLowerCase().includes(searchQuery);
                    return matchSearch;
                });

                infoPanelPlaceholder.style.display = filteredData.length === 0 ? 'flex' : 'none';

                filteredData.forEach(tps => {
                    const lat = parseFloat(tps.latitude);
                    const lng = parseFloat(tps.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const truncatedDesc = stripHtml(tps.alamat).substring(0, 80) + (stripHtml(tps
                            .alamat).length > 80 ? '...' : '');

                        const popupContent = `
                        <div style="max-width:220px;">
                            <strong class="d-block text-dark fw-bold">${tps.nama}</strong><br>
                            <div>${truncatedDesc}</div>
                            <div class="mt-2 d-flex gap-1">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" 
                                    target="_blank" class="btn btn-sm btn-success text-white rounded">Rute</a>
                            </div>
                        </div>
                    `;

                        const marker = L.marker([lat, lng]).bindPopup(popupContent);
                        markerGroup.addLayer(marker);

                        // Panel list item
                        const card = document.createElement('div');
                        card.className = 'bg-white mb-3 p-2 rounded shadow-sm';
                        card.innerHTML = `
                        <strong class="d-block text-dark fw-bold">${tps.nama}</strong>
                        <small class="d-block">${truncatedDesc}</small>
                    `;
                        card.addEventListener('click', () => {
                            map.flyTo([lat, lng], 16);
                            marker.openPopup();
                        });
                        resultsList.appendChild(card);
                    }
                });

                // Zoom to fit all markers
                const bounds = markerGroup.getBounds();
                if (bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.05));
                }
            }

            document.getElementById('filter-search').addEventListener('input', applyFilters);

            applyFilters();
        });
    </script>
@endsection
