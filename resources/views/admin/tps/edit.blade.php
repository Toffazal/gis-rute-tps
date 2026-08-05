@extends('admin.template')
@section('content')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <a href="{{ route('admin.tps.index') }}" class="btn btn-secondary btn-sm text-white mb-2"><i
            class="fas fa-angles-left"></i>Kembali</a>

    <div class="white-box">
        <h3 class="box-title mb-4">Edit TPS</h3>

        <form action="{{ route('admin.tps.update', $tps->id) }}" method="post" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="nama" class="form-label">Nama TPS</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ $tps->nama }}"
                            required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="5" required>{{ $tps->alamat }}</textarea>
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-lg-8">
                    <p class="text-muted">Klik peta untuk menentukan lokasi marker.</p>
                    <div id="map-editor" style="height: 350px;"></div>
                    <button type="button" class="btn btn-info my-3" id="set-marker-manual">Set lokasi dari Koordinat Anda
                        saat ini</button>
                </div>
                <div class="col-lg-4">
                    <!-- Input Manual Koordinat -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="manual_latitude" class="form-label">Latitude</label>
                                <input type="number" id="manual_latitude" class="form-control" step="any"
                                    value="{{ $tps->latitude ?? '' }}" placeholder="-5.4326"
                                    title="Masukkan nilai latitude (contoh: -5.4326)">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="manual_longitude" class="form-label">Longitude</label>
                                <input type="number" id="manual_longitude" class="form-control" step="any"
                                    value="{{ $tps->longitude ?? '' }}" placeholder="105.2610"
                                    title="Masukkan nilai longitude (contoh: 105.2610)">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Data Koordinat Marker (JSON)</label>
                        <textarea id="marker-data-display" class="form-control" rows="6" readonly></textarea>
                    </div>

                    <input type="hidden" name="koordinat_marker" id="koordinat_marker">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan TPS</button>
            </div>
        </form>

    </div>
@endsection


@section('scripts')
    <script>
        let mapEditor;
        let currentMarker = null;

        document.addEventListener('DOMContentLoaded', function() {
            const defaultLat = {{ $tps->latitude ?? 3.593147 }};
            const defaultLng = {{ $tps->longitude ?? 98.4879093 }};

            mapEditor = L.map('map-editor').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapEditor);

            // Function untuk update marker dan data koordinat
            function updateMarkerAndData(lat, lng, popupText = "Lokasi marker") {
                // Validasi input
                if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                    return false;
                }

                // Hapus marker lama kalau ada
                if (currentMarker) {
                    mapEditor.removeLayer(currentMarker);
                }

                // Pasang marker baru
                currentMarker = L.marker([lat, lng]).addTo(mapEditor)
                    .bindPopup(popupText).openPopup();

                // Update peta view
                mapEditor.setView([lat, lng], 16);

                // Simpan koordinat ke input dan textarea
                const coords = [parseFloat(lat), parseFloat(lng)];
                const coordsJson = JSON.stringify(coords, null, 2);

                document.getElementById('koordinat_marker').value = JSON.stringify(coords);
                document.getElementById('marker-data-display').value = coordsJson;

                // Update input manual jika dipanggil dari klik peta atau geolocation
                if (popupText !== "Koordinat Manual") {
                    document.getElementById('manual_latitude').value = lat;
                    document.getElementById('manual_longitude').value = lng;
                }

                return true;
            }

            // Pasang marker awal jika sudah ada koordinat
            if (defaultLat && defaultLng) {
                currentMarker = L.marker([defaultLat, defaultLng]).addTo(mapEditor).bindPopup(
                        "Lokasi TPS")
                    .openPopup();
                document.getElementById('marker-data-display').value = JSON.stringify([defaultLat, defaultLng],
                    null, 2);
                document.getElementById('koordinat_marker').value = JSON.stringify([defaultLat, defaultLng]);
            }

            // Klik peta untuk pasang / pindah marker
            mapEditor.on('click', function(e) {
                const latlng = e.latlng;
                updateMarkerAndData(latlng.lat, latlng.lng, "Lokasi marker dari klik peta");
            });

            // Tombol set lokasi dari geolocation
            document.getElementById('set-marker-manual').addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert("Geolocation tidak didukung oleh browser ini.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        updateMarkerAndData(lat, lng, "Lokasi Anda Saat Ini");
                    },
                    function(error) {
                        alert("Gagal mengambil lokasi: " + error.message);
                    }
                );
            });

            // Real-time update saat mengetik koordinat
            let debounceTimer;

            function handleManualInput() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    const lat = document.getElementById('manual_latitude').value;
                    const lng = document.getElementById('manual_longitude').value;

                    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                        if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                            updateMarkerAndData(lat, lng, "Koordinat Manual");
                        }
                    }
                }, 1000);
            }

            document.getElementById('manual_latitude').addEventListener('input', handleManualInput);
            document.getElementById('manual_longitude').addEventListener('input', handleManualInput);
        });
    </script>
@endsection
