@extends('admin.template')
@section('content')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @php
        $tpaCoords = json_decode($website->koordinat_tpa, true);
        $tpaLat = $tpaCoords[0] ?? 3.593147;
        $tpaLng = $tpaCoords[1] ?? 98.4879093;

        $kantorCoords = json_decode($website->koordinat_kantor, true);
        $kantorLat = $kantorCoords[0] ?? 3.593147;
        $kantorLng = $kantorCoords[1] ?? 98.4879093;
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="white-box">
                <h3 class="box-title mb-4">Pengaturan Website</h3>

                <form action="{{ route('admin.website.update', $website->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- 1. Informasi Umum -->
                    <div class="border p-4 rounded mb-4 bg-light shadow-sm">
                        <h4 class="fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>Informasi Umum</h4>
                        
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label fw-bold">Nama Website / Aplikasi</label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $website->nama) }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="telepon" class="form-label fw-bold">Nomor Telepon / Kontak</label>
                                    <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $website->telepon) }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label fw-bold">Email Kantor</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $website->email) }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="logo" class="form-label fw-bold">Logo Website</label>
                                    <input type="file" name="logo" id="logo" class="form-control mb-2" accept="image/*">
                                    @if ($website->logo)
                                        <div class="mt-2 p-2 border rounded bg-white d-inline-block">
                                            <img id="logo-preview" src="{{ asset('img/' . $website->logo) }}" alt="Logo Preview" style="max-height: 80px;" class="img-fluid">
                                            <small class="d-block text-muted text-center mt-1">Logo Saat Ini</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group mb-3">
                                    <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required>{{ old('deskripsi', $website->deskripsi) }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label fw-bold">Alamat Kantor</label>
                                    <textarea name="alamat" id="alamat" class="form-control" rows="4" required>{{ old('alamat', $website->alamat) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Lokasi TPA Map (1 Kolom / Full Width) -->
                    <div class="border p-4 rounded mb-4 bg-light shadow-sm">
                        <h4 class="fw-bold mb-2 text-dark d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span><i class="fas fa-trash-alt me-2 text-danger"></i>Lokasi TPA</span>
                            <button type="button" class="btn btn-info btn-sm text-white" id="set-tpa-current"><i class="fas fa-location-crosshairs me-1"></i> Tentukan dari Lokasi Saya</button>
                        </h4>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Klik pada peta untuk menaruh / memindahkan koordinat lokasi TPA.</p>
                        
                        <div id="map-tpa" style="height: 380px; border-radius: 4px; border: 1px solid #ddd;" class="mb-3"></div>
                        
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="tpa_lat" class="form-label fw-bold">Latitude TPA</label>
                                <input type="number" id="tpa_lat" class="form-control" step="any" value="{{ $tpaLat }}">
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="tpa_lng" class="form-label fw-bold">Longitude TPA</label>
                                <input type="number" id="tpa_lng" class="form-control" step="any" value="{{ $tpaLng }}">
                            </div>
                        </div>
                        <input type="hidden" name="koordinat_tpa" id="koordinat_tpa" value="{{ $website->koordinat_tpa }}">
                    </div>

                    <!-- 3. Lokasi Kantor DLH Map (1 Kolom / Full Width) -->
                    <div class="border p-4 rounded mb-4 bg-light shadow-sm">
                        <h4 class="fw-bold mb-2 text-dark d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span><i class="fas fa-building me-2 text-primary"></i>Lokasi Kantor DLH</span>
                            <button type="button" class="btn btn-info btn-sm text-white" id="set-kantor-current"><i class="fas fa-location-crosshairs me-1"></i> Tentukan dari Lokasi Saya</button>
                        </h4>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Klik pada peta untuk menaruh / memindahkan koordinat lokasi Kantor DLH.</p>
                        
                        <div id="map-kantor" style="height: 380px; border-radius: 4px; border: 1px solid #ddd;" class="mb-3"></div>
                        
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="kantor_lat" class="form-label fw-bold">Latitude Kantor</label>
                                <input type="number" id="kantor_lat" class="form-control" step="any" value="{{ $kantorLat }}">
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="kantor_lng" class="form-label fw-bold">Longitude Kantor</label>
                                <input type="number" id="kantor_lng" class="form-control" step="any" value="{{ $kantorLng }}">
                            </div>
                        </div>
                        <input type="hidden" name="koordinat_kantor" id="koordinat_kantor" value="{{ $website->koordinat_kantor }}">
                    </div>

                    <div class="border-top pt-3 mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGO PREVIEW ---
            const logoInput = document.getElementById('logo');
            if (logoInput) {
                logoInput.addEventListener('change', function(event) {
                    const preview = document.getElementById('logo-preview');
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (preview) {
                                preview.src = e.target.result;
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // --- LEAFLET MAPS ---
            const tpaLatInput = document.getElementById('tpa_lat');
            const tpaLngInput = document.getElementById('tpa_lng');
            const tpaHiddenInput = document.getElementById('koordinat_tpa');

            const kantorLatInput = document.getElementById('kantor_lat');
            const kantorLngInput = document.getElementById('kantor_lng');
            const kantorHiddenInput = document.getElementById('koordinat_kantor');

            const initialTpaLat = parseFloat(tpaLatInput.value) || 3.593147;
            const initialTpaLng = parseFloat(tpaLngInput.value) || 98.4879093;

            const initialKantorLat = parseFloat(kantorLatInput.value) || 3.593147;
            const initialKantorLng = parseFloat(kantorLngInput.value) || 98.4879093;

            // 1. TPA Map
            const mapTpa = L.map('map-tpa').setView([initialTpaLat, initialTpaLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapTpa);
            let markerTpa = L.marker([initialTpaLat, initialTpaLng]).addTo(mapTpa).bindPopup("Lokasi TPA").openPopup();

            // 2. Kantor DLH Map
            const mapKantor = L.map('map-kantor').setView([initialKantorLat, initialKantorLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapKantor);
            let markerKantor = L.marker([initialKantorLat, initialKantorLng]).addTo(mapKantor).bindPopup("Lokasi Kantor DLH").openPopup();

            // Update Functions
            function updateTpaData(lat, lng) {
                tpaLatInput.value = lat;
                tpaLngInput.value = lng;
                tpaHiddenInput.value = `[${lat}, ${lng}]`;

                if (markerTpa) {
                    mapTpa.removeLayer(markerTpa);
                }
                markerTpa = L.marker([lat, lng]).addTo(mapTpa).bindPopup("Lokasi TPA").openPopup();
                mapTpa.setView([lat, lng], 15);
            }

            function updateKantorData(lat, lng) {
                kantorLatInput.value = lat;
                kantorLngInput.value = lng;
                kantorHiddenInput.value = `[${lat}, ${lng}]`;

                if (markerKantor) {
                    mapKantor.removeLayer(markerKantor);
                }
                markerKantor = L.marker([lat, lng]).addTo(mapKantor).bindPopup("Lokasi Kantor DLH").openPopup();
                mapKantor.setView([lat, lng], 15);
            }

            // Map Click Events
            mapTpa.on('click', function(e) {
                updateTpaData(e.latlng.lat.toFixed(7), e.latlng.lng.toFixed(7));
            });

            mapKantor.on('click', function(e) {
                updateKantorData(e.latlng.lat.toFixed(7), e.latlng.lng.toFixed(7));
            });

            // Geolocation Buttons
            document.getElementById('set-tpa-current').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        updateTpaData(position.coords.latitude.toFixed(7), position.coords.longitude.toFixed(7));
                    }, function() {
                        alert("Gagal mengambil koordinat lokasi saat ini.");
                    });
                } else {
                    alert("Browser Anda tidak mendukung Geolocation.");
                }
            });

            document.getElementById('set-kantor-current').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        updateKantorData(position.coords.latitude.toFixed(7), position.coords.longitude.toFixed(7));
                    }, function() {
                        alert("Gagal mengambil koordinat lokasi saat ini.");
                    });
                } else {
                    alert("Browser Anda tidak mendukung Geolocation.");
                }
            });

            // Form Manual Input Updates with Debounce
            let debounceTpa;
            function handleManualTpaInput() {
                clearTimeout(debounceTpa);
                debounceTpa = setTimeout(function() {
                    const lat = parseFloat(tpaLatInput.value);
                    const lng = parseFloat(tpaLngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        updateTpaData(lat, lng);
                    }
                }, 800);
            }
            tpaLatInput.addEventListener('input', handleManualTpaInput);
            tpaLngInput.addEventListener('input', handleManualTpaInput);

            let debounceKantor;
            function handleManualKantorInput() {
                clearTimeout(debounceKantor);
                debounceKantor = setTimeout(function() {
                    const lat = parseFloat(kantorLatInput.value);
                    const lng = parseFloat(kantorLngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        updateKantorData(lat, lng);
                    }
                }, 800);
            }
            kantorLatInput.addEventListener('input', handleManualKantorInput);
            kantorLngInput.addEventListener('input', handleManualKantorInput);

            // Fix map rendering inside hidden components or just call invalidateSize
            setTimeout(function() {
                mapTpa.invalidateSize();
                mapKantor.invalidateSize();
            }, 500);
        });
    </script>
@endsection
