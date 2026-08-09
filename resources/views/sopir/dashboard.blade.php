@extends('sopir.template')
@section('content')
    <style>
        #dashboard-map {
            height: 450px;
            border-radius: 10px;
            border: 2px solid #dee2e6;
            z-index: 1;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .destination-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.2s;
        }

        .destination-card.active {
            border-left-color: #198754;
            background-color: #f0fff4;
        }

        .destination-card.selesai {
            border-left-color: #6c757d;
            opacity: 0.7;
        }

        .destination-card.dilewati {
            border-left-color: #dc3545;
            opacity: 0.7;
        }

        .tujuan-list-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tujuan-list-item:last-child {
            border-bottom: none;
        }

        .urutan-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            color: #fff;
            flex-shrink: 0;
        }
    </style>

    {{-- Header --}}
    <div class="row">
        <div class="col-12">
            <div class="white-box p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 fw-bold">Selamat datang, {{ $user->name }}!</h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                            &mdash; Hari: <strong>{{ $hariIni }}</strong>
                        </p>
                    </div>
                    <div>
                        @if ($ruteHarian)
                            @if ($ruteHarian->status === 'belum_mulai')
                                <span class="badge bg-warning text-dark status-badge me-2">
                                    <i class="fas fa-clock me-1"></i> Belum Dimulai
                                </span>
                                <form action="{{ route('sopir.rute.mulai', $ruteHarian->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm"
                                        onclick="return confirm('Mulai rute pengangkutan hari ini?')">
                                        <i class="fas fa-play me-1"></i> Mulai Rute
                                    </button>
                                </form>
                            @elseif ($ruteHarian->status === 'berlangsung')
                                <span class="badge bg-primary status-badge me-2">
                                    <i class="fas fa-truck me-1"></i> Sedang Berlangsung
                                </span>
                                <form action="{{ route('sopir.rute.selesaiRute', $ruteHarian->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Yakin ingin mengakhiri rute hari ini? Semua tujuan yang belum dikunjungi akan ditandai sebagai tidak dikunjungi.')">
                                        <i class="fas fa-flag-checkered me-1"></i> Selesaikan Rute
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success status-badge">
                                    <i class="fas fa-check-circle me-1"></i> Rute Selesai
                                </span>
                            @endif
                        @else
                            <span class="badge bg-secondary status-badge">
                                <i class="fas fa-info-circle me-1"></i> Tidak ada jadwal hari ini
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($ruteHarian && $ruteHarian->ruteTujuan->count() > 0)
        {{-- Active Destination Card --}}
        @if ($tujuanAktif && $ruteHarian->status === 'berlangsung')
            <div class="row">
                <div class="col-12">
                    <div class="white-box p-3 destination-card active">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-success mb-1">
                                    <i class="fas fa-map-pin me-1"></i>
                                    Tujuan Berikutnya — #{{ $tujuanAktif->urutan }}
                                </h6>
                                <h5 class="fw-bold mb-1">
                                    @if ($tujuanAktif->tipe === 'tpa')
                                        <i class="fas fa-industry me-1 text-danger"></i> TPA
                                    @else
                                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                        {{ $tujuanAktif->tps->nama ?? 'TPS' }}
                                    @endif
                                </h5>
                                @if ($tujuanAktif->tps)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-road me-1"></i>
                                        {{ $tujuanAktif->tps->alamat ?? '-' }}
                                    </p>
                                @endif
                                <small class="text-muted">
                                    <i class="fas fa-ruler me-1"></i>
                                    Jarak: {{ number_format($tujuanAktif->jarak_dari_sebelumnya_meter / 1000, 1) }} km
                                </small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                {{-- Navigasi Google Maps --}}
                                @php
                                    if ($tujuanAktif->tipe === 'tpa') {
                                        $navLat = $tpaCoord[0];
                                        $navLng = $tpaCoord[1];
                                    } else {
                                        $navLat = $tujuanAktif->tps->latitude ?? 0;
                                        $navLng = $tujuanAktif->tps->longitude ?? 0;
                                    }
                                @endphp
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $navLat }},{{ $navLng }}&travelmode=driving"
                                    target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-directions me-1"></i> Navigasi
                                </a>

                                {{-- Tombol Tiba --}}
                                @if (!$tujuanAktif->waktu_tiba)
                                    <form action="{{ route('sopir.rute.tiba', $tujuanAktif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm text-white"
                                            onclick="return confirm('Tandai sudah tiba di lokasi ini?')">
                                            <i class="fas fa-map-pin me-1"></i> Tiba
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Selesai --}}
                                @if ($tujuanAktif->waktu_tiba)
                                    <form action="{{ route('sopir.rute.selesai', $tujuanAktif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"
                                            onclick="return confirm('Tandai selesai pengangkutan di lokasi ini?')">
                                            <i class="fas fa-check me-1"></i> Selesai
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Skip --}}
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#skipModal">
                                    <i class="fas fa-forward me-1"></i> Skip
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Map --}}
        <div class="row">
            <div class="col-12">
                <div class="white-box p-3">
                    <h6 class="fw-bold mb-3"><i class="fas fa-map me-2 text-primary"></i>Peta Rute Pengangkutan</h6>
                    <div id="dashboard-map"></div>
                </div>
            </div>
        </div>

        {{-- Destination List --}}
        <div class="row">
            <div class="col-12">
                <div class="white-box p-0">
                    <div class="p-3 border-bottom">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list-ol me-2 text-primary"></i>Urutan Tujuan Kunjungan
                        </h6>
                    </div>
                    @foreach ($ruteHarian->ruteTujuan as $tujuan)
                        <div class="tujuan-list-item">
                            @php
                                $bgColor = match ($tujuan->status) {
                                    'selesai' => '#198754',
                                    'aktif' => '#0d6efd',
                                    'dilewati' => '#dc3545',
                                    'tidak_dikunjungi' => '#6c757d',
                                    default => '#adb5bd',
                                };
                            @endphp
                            <div class="urutan-badge" style="background-color: {{ $bgColor }}">
                                {{ $tujuan->urutan }}
                            </div>
                            <div class="flex-grow-1">
                                <strong>
                                    @if ($tujuan->tipe === 'tpa')
                                        <i class="fas fa-industry text-danger me-1"></i> TPA
                                    @else
                                        {{ $tujuan->tps->nama ?? 'TPS' }}
                                    @endif
                                </strong>
                                @if ($tujuan->tps)
                                    <br><small class="text-muted">{{ $tujuan->tps->alamat ?? '' }}</small>
                                @endif
                            </div>
                            <div class="text-end d-flex align-items-center gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">
                                        {{ number_format($tujuan->jarak_dari_sebelumnya_meter / 1000, 1) }} km
                                    </small>
                                    @php
                                        $statusLabel = match ($tujuan->status) {
                                            'selesai' => '<span class="badge bg-success">Selesai</span>',
                                            'aktif' => '<span class="badge bg-primary">Aktif</span>',
                                            'dilewati' => '<span class="badge bg-danger">Dilewati</span>',
                                            'tidak_dikunjungi'
                                                => '<span class="badge bg-secondary">Tidak Dikunjungi</span>',
                                            default => '<span class="badge bg-light text-dark">Menunggu</span>',
                                        };
                                    @endphp
                                    {!! $statusLabel !!}
                                </div>
                                @if ($tujuan->status === 'menunggu' && $tujuan->tipe === 'tps' && $ruteHarian->status === 'berlangsung')
                                    <form action="{{ route('sopir.rute.mulaiManual', $tujuan->id) }}" method="POST"
                                        class="ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2 text-white"
                                            style="font-size: 0.75rem;"
                                            onclick="return confirm('Kunjungi TPS ini sekarang? Urutan rute sisa lainnya akan dihitung ulang secara otomatis.')">
                                            <i class="fas fa-play me-1"></i> Mulai
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="p-3 border-top bg-light">
                        <div class="d-flex justify-content-between">
                            <strong>Total Estimasi Jarak:</strong>
                            <strong class="text-primary">
                                {{ number_format(($ruteHarian->total_jarak_meter ?? 0) / 1000, 1) }} km</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skip Modal --}}
        @if ($tujuanAktif && $ruteHarian->status === 'berlangsung')
            <div class="modal fade" id="skipModal" tabindex="-1" aria-labelledby="skipModalLabel" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('sopir.rute.skip', $tujuanAktif->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="skipModalLabel">Lewati Tujuan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda akan melewati:
                                    <strong>{{ $tujuanAktif->tps->nama ?? 'TPA' }}</strong>
                                </p>
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Opsi Pengabaian <span
                                            class="text-danger">*</span></label>
                                    <div class="form-check p-0">
                                        <div class="mb-2">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="status_skip"
                                                id="status_skip_dilewati" value="dilewati" checked required>
                                            <label class="form-check-label fw-bold" for="status_skip_dilewati">
                                                Dilewati (Kunjungi Nanti)
                                            </label>
                                            <span class="d-block text-muted ms-4" style="font-size: 0.8rem;">
                                                TPS dilewati sementara, akan dikunjungi di akhir urutan sebelum TPA dengan
                                                rute yang dioptimalkan kembali.
                                            </span>
                                        </div>
                                        <div>
                                            <input class="form-check-input ms-0 me-2" type="radio" name="status_skip"
                                                id="status_skip_tidak_dikunjungi" value="tidak_dikunjungi" required>
                                            <label class="form-check-label fw-bold" for="status_skip_tidak_dikunjungi">
                                                Tidak Dikunjungi (Batal)
                                            </label>
                                            <span class="d-block text-muted ms-4" style="font-size: 0.8rem;">
                                                TPS dibatalkan sepenuhnya untuk pengangkutan hari ini.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="alasan" class="form-label fw-bold">Alasan <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alasan" id="alasan" class="form-control" rows="3" required
                                        placeholder="Jelaskan alasan melewati tujuan ini..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary text-white"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row">
            <div class="col-12">
                <div class="white-box text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada jadwal pengangkutan untuk hari ini</h5>
                    <p class="text-muted">Silakan cek halaman <a href="{{ route('sopir.jadwal.index') }}">Jadwal</a>
                        untuk melihat jadwal lengkap Anda.</p>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($ruteHarian && $ruteHarian->ruteTujuan->count() > 0)
                const kantorCoord = [{{ $kantorCoord[0] }}, {{ $kantorCoord[1] }}];
                const tpaCoord = [{{ $tpaCoord[0] }}, {{ $tpaCoord[1] }}];

                const map = L.map('dashboard-map').setView(kantorCoord, 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Custom icons
                const kantorIcon = L.divIcon({
                    html: '<i class="fas fa-building" style="color: #198754; font-size: 22px; text-shadow: 0 1px 3px rgba(0,0,0,0.4);"></i>',
                    className: 'custom-div-icon',
                    iconSize: [22, 22],
                    iconAnchor: [11, 22],
                    popupAnchor: [0, -22]
                });

                const tpaIcon = L.divIcon({
                    html: '<i class="fas fa-industry" style="color: #dc3545; font-size: 22px; text-shadow: 0 1px 3px rgba(0,0,0,0.4);"></i>',
                    className: 'custom-div-icon',
                    iconSize: [22, 22],
                    iconAnchor: [11, 22],
                    popupAnchor: [0, -22]
                });

                // Kantor marker
                L.marker(kantorCoord, {
                    icon: kantorIcon
                }).addTo(map).bindPopup(
                    '<strong><i class="fas fa-building text-success me-1"></i> Kantor (Start)</strong>');

                // TPA marker
                L.marker(tpaCoord, {
                    icon: tpaIcon
                }).addTo(map).bindPopup(
                    '<strong><i class="fas fa-industry text-danger me-1"></i> TPA (Finish)</strong>');

                // Route points for polyline
                const routePoints = [kantorCoord];

                // TPS markers
                @foreach ($ruteHarian->ruteTujuan as $tujuan)
                    @if ($tujuan->tipe === 'tps' && $tujuan->tps)
                        (function() {
                            const coord = [{{ $tujuan->tps->latitude }}, {{ $tujuan->tps->longitude }}];
                            const status = '{{ $tujuan->status }}';

                            let color = '#6c757d';
                            if (status === 'aktif') color = '#0d6efd';
                            else if (status === 'selesai') color = '#198754';
                            else if (status === 'dilewati') color = '#dc3545';
                            else color = '#fd7e14'; // menunggu

                            const tpsIcon = L.divIcon({
                                html: '<div style="background:' + color +
                                    ';color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,0.4);">{{ $tujuan->urutan }}</div>',
                                className: 'custom-div-icon',
                                iconSize: [24, 24],
                                iconAnchor: [12, 24],
                                popupAnchor: [0, -24]
                            });

                            L.marker(coord, {
                                    icon: tpsIcon
                                }).addTo(map)
                                .bindPopup(
                                    '<strong>#{{ $tujuan->urutan }} - {{ $tujuan->tps->nama }}</strong><br>' +
                                    '<small>{{ $tujuan->tps->alamat ?? '' }}</small><br>' +
                                    '<span class="badge" style="background:' + color +
                                    '">{{ ucfirst($tujuan->status) }}</span>'
                                );

                            routePoints.push(coord);
                        })();
                    @endif
                @endforeach

                // Add TPA as last point
                routePoints.push(tpaCoord);

                // Draw polyline
                L.polyline(routePoints, {
                    color: '#0d6efd',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '10, 6'
                }).addTo(map);

                // Fit bounds
                const bounds = L.latLngBounds(routePoints);
                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            @endif
        });
    </script>
@endpush
