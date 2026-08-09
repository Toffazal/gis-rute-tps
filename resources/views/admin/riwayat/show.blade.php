@extends('admin.template')
@section('head')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <style>
        #riwayat-map {
            height: 450px;
            border: 1px solid #dee2e6;
            z-index: 1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding: 12px 18px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0d6efd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 17px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #0d6efd;
        }

        .timeline-item.aksi-selesai {
            border-left-color: #198754;
        }

        .timeline-item.aksi-selesai::before {
            background: #198754;
            box-shadow: 0 0 0 2px #198754;
        }

        .timeline-item.aksi-skip,
        .timeline-item.aksi-tidak_dikunjungi {
            border-left-color: #dc3545;
        }

        .timeline-item.aksi-skip::before,
        .timeline-item.aksi-tidak_dikunjungi::before {
            background: #dc3545;
            box-shadow: 0 0 0 2px #dc3545;
        }

        .timeline-item.aksi-tiba {
            border-left-color: #0dcaf0;
        }

        .timeline-item.aksi-tiba::before {
            background: #0dcaf0;
            box-shadow: 0 0 0 2px #0dcaf0;
        }

        .card-meta {
            background: #fff;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
        }
    </style>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.riwayat.index') }}" class="btn btn-secondary btn-sm text-white">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        @if ($ruteHarian->status === 'berlangsung')
            <span class="badge bg-danger animate-pulse px-3 py-2 text-white">
                <i class="fas fa-satellite-dish fa-spin me-2"></i> PANTUAN REALTIME / SEDANG BERJALAN
            </span>
        @endif
    </div>

    <!-- Info Header Card -->
    <div class="card-meta">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6 col-12 border-end">
                <small class="text-muted d-block text-uppercase fw-semibold mb-1">Nama Sopir</small>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle text-primary me-2 fs-4"></i>
                    <div>
                        <p class="fw-bold mb-0 text-dark">{{ $ruteHarian->user->name ?? 'Sopir Terhapus' }}</p>
                        <small class="text-muted">Username: {{ $ruteHarian->user->username ?? '-' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 border-end">
                <small class="text-muted d-block text-uppercase fw-semibold mb-1">Kendaraan / Angkutan</small>
                <div class="d-flex align-items-center">
                    <i class="fas fa-truck text-success me-2 fs-4"></i>
                    <div>
                        <p class="fw-bold mb-0 text-dark">{{ $ruteHarian->angkutan->nama ?? '-' }}</p>
                        <small class="text-muted">No Polisi: {{ $ruteHarian->angkutan->nomor ?? '-' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 border-end">
                <small class="text-muted d-block text-uppercase fw-semibold mb-1">Tanggal Tugas</small>
                <p class="fw-bold mb-0 text-dark mt-1">
                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                    {{ $ruteHarian->tanggal->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="col-md-2 col-sm-4 col-6 border-end">
                <small class="text-muted d-block text-uppercase fw-semibold mb-1">Total Jarak & Durasi</small>
                <p class="fw-bold mb-0 text-primary mt-1">
                    {{ number_format(($ruteHarian->total_jarak_meter ?? 0) / 1000, 1) }} km
                </p>
                <small class="text-muted">
                    @if ($ruteHarian->status === 'belum_mulai')
                        Belum Jalan
                    @elseif ($ruteHarian->status === 'berlangsung')
                        Sedang Jalan
                    @elseif ($ruteHarian->total_durasi_detik)
                        @php
                            $durasi = abs($ruteHarian->total_durasi_detik);
                            $jam = floor($durasi / 3600);
                            $menit = floor(($durasi % 3600) / 60);
                        @endphp
                        <i class="far fa-clock me-1"></i> {{ $jam > 0 ? $jam . 'j ' : '' }}{{ $menit }}m
                    @else
                        -
                    @endif
                </small>
            </div>
            <div class="col-md-2 col-sm-4 col-12">
                <small class="text-muted d-block text-uppercase fw-semibold mb-1">Status Rute</small>
                @php
                    $statusBadge = match ($ruteHarian->status) {
                        'selesai' => 'bg-success text-white',
                        'berlangsung' => 'bg-primary text-white',
                        'belum_mulai' => 'bg-light text-dark border',
                        default => 'bg-secondary text-white',
                    };
                    $statusLabel = match ($ruteHarian->status) {
                        'selesai' => 'Selesai',
                        'berlangsung' => 'Berlangsung',
                        'belum_mulai' => 'Belum Mulai',
                        default => ucfirst($ruteHarian->status),
                    };
                @endphp
                <span class="badge {{ $statusBadge }} mt-2 px-3 py-2" style="font-size: 12px; border-radius: 30px;">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="card-meta">
        <h6 class="fw-bold mb-3"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Peta Jalur Rute & Monitoring Posisi
        </h6>
        <div id="riwayat-map"></div>
    </div>

    <div class="row">
        <!-- Destination List -->
        <div class="col-lg-7 col-12">
            <div class="card-meta">
                <h6 class="fw-bold mb-4"><i class="fas fa-list-ol me-2 text-primary"></i>Urutan Kunjungan TPS / TPA</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">Urutan</th>
                                <th>Lokasi / TPS</th>
                                <th>Status</th>
                                <th class="text-end">Jarak</th>
                                <th>Waktu Tiba</th>
                                <th>Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ruteHarian->ruteTujuan as $tujuan)
                                @php
                                    $statusClass = match ($tujuan->status) {
                                        'selesai' => 'bg-success text-white',
                                        'aktif' => 'bg-primary text-white',
                                        'dilewati' => 'bg-danger text-white',
                                        'tidak_dikunjungi' => 'bg-secondary text-white',
                                        default => 'bg-light text-dark border',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center font-bold">{{ $tujuan->urutan }}</td>
                                    <td>
                                        @if ($tujuan->tipe === 'tpa')
                                            <span class="text-danger"><i class="fas fa-industry me-1"></i> <strong>TPA
                                                    (Titik Akhir)
                                                </strong></span>
                                        @else
                                            <span class="text-dark"><i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                                {{ $tujuan->tps->nama ?? 'TPS Terhapus' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($tujuan->status) }}</span>
                                        @if ($tujuan->alasan)
                                            <div class="small text-danger mt-1">Alasan: {{ $tujuan->alasan }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end text-primary font-bold">
                                        {{ number_format($tujuan->jarak_dari_sebelumnya_meter / 1000, 2) }} km
                                    </td>
                                    <td>{{ $tujuan->waktu_tiba ? $tujuan->waktu_tiba->format('H:i:s') : '-' }}</td>
                                    <td>{{ $tujuan->waktu_selesai ? $tujuan->waktu_selesai->format('H:i:s') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Log Timeline -->
        <div class="col-lg-5 col-12">
            <div class="card-meta">
                <h6 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>Timeline Aktivitas Sopir</h6>

                @if ($ruteHarian->ruteLog->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="far fa-clock fa-2x mb-2 text-light"></i>
                        <p class="mb-0">Belum ada catatan aktivitas.</p>
                    </div>
                @else
                    <div class="timeline">
                        @foreach ($ruteHarian->ruteLog as $log)
                            @php
                                $logClass = match ($log->aksi) {
                                    'selesai' => 'aksi-selesai',
                                    'skip', 'tidak_dikunjungi' => 'aksi-skip',
                                    'tiba' => 'aksi-tiba',
                                    default => '',
                                };
                                $logLabel = match ($log->aksi) {
                                    'mulai_rute'
                                        => '<span class="text-success"><i class="fas fa-play me-1"></i> Mulai Rute</span>',
                                    'tiba'
                                        => '<span class="text-info"><i class="fas fa-map-pin me-1"></i> Tiba di Lokasi</span>',
                                    'selesai'
                                        => '<span class="text-success"><i class="fas fa-check-circle me-1"></i> Selesai Pengangkutan</span>',
                                    'skip'
                                        => '<span class="text-danger"><i class="fas fa-forward me-1"></i> Dilewati (Skip)</span>',
                                    'tidak_dikunjungi'
                                        => '<span class="text-secondary"><i class="fas fa-times-circle me-1"></i> Tidak Dikunjungi</span>',
                                    default => ucfirst(str_replace('_', ' ', $log->aksi)),
                                };
                            @endphp
                            <div class="timeline-item {{ $logClass }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{!! $logLabel !!}</strong>
                                    <small class="text-muted fw-bold">{{ $log->created_at->format('H:i:s') }}</small>
                                </div>
                                @if ($log->ruteTujuan)
                                    <small class="text-muted d-block mt-1">
                                        @if ($log->ruteTujuan->tipe === 'tpa')
                                            Lokasi: TPA
                                        @else
                                            Lokasi: {{ $log->ruteTujuan->tps->nama ?? 'TPS' }}
                                        @endif
                                    </small>
                                @endif
                                @if ($log->alasan)
                                    <div class="small text-danger mt-1"><strong>Alasan:</strong> {{ $log->alasan }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kantorCoord = [{{ $kantorCoord[0] }}, {{ $kantorCoord[1] }}];
            const tpaCoord = [{{ $tpaCoord[0] }}, {{ $tpaCoord[1] }}];

            // Initialize Map
            const map = L.map('riwayat-map').setView(kantorCoord, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Custom Icons
            const kantorIcon = L.divIcon({
                html: '<i class="fas fa-building" style="color: #198754; font-size: 24px; text-shadow: 0 1px 3px rgba(0,0,0,0.4);"></i>',
                className: 'custom-div-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            });

            const tpaIcon = L.divIcon({
                html: '<i class="fas fa-industry" style="color: #dc3545; font-size: 24px; text-shadow: 0 1px 3px rgba(0,0,0,0.4);"></i>',
                className: 'custom-div-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            });

            // Markers for Office & TPA
            L.marker(kantorCoord, {
                    icon: kantorIcon
                }).addTo(map)
                .bindPopup('<strong><i class="fas fa-building text-success me-1"></i> Kantor (Start)</strong>');

            L.marker(tpaCoord, {
                    icon: tpaIcon
                }).addTo(map)
                .bindPopup(
                    '<strong><i class="fas fa-industry text-danger me-1"></i> TPA (Selesai/Tujuan Akhir)</strong>');

            const routePoints = [kantorCoord];

            // Loop markers of each route destination
            @foreach ($ruteHarian->ruteTujuan as $tujuan)
                @if ($tujuan->tipe === 'tps' && $tujuan->tps)
                    (function() {
                        const coord = [{{ $tujuan->tps->latitude }}, {{ $tujuan->tps->longitude }}];
                        const status = '{{ $tujuan->status }}';

                        // Select color code based on destination status
                        let color = '#6c757d'; // Menunggu
                        if (status === 'selesai') color = '#198754';
                        else if (status === 'aktif') color = '#0d6efd';
                        else if (status === 'dilewati' || status === 'tidak_dikunjungi') color = '#dc3545';

                        const icon = L.divIcon({
                            html: '<div style="background:' + color +
                                ';color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;box-shadow:0 1px-4px rgba(0,0,0,0.3); border: 2px solid #fff;">{{ $tujuan->urutan }}</div>',
                            className: 'custom-div-icon',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12],
                            popupAnchor: [0, -12]
                        });

                        L.marker(coord, {
                            icon: icon
                        }).addTo(map).bindPopup(
                            '<strong>#{{ $tujuan->urutan }} - {{ $tujuan->tps->nama }}</strong><br>' +
                            'Status: <span class="badge" style="background:' + color +
                            '">{{ ucfirst($tujuan->status) }}</span>' +
                            (
                                @if ($tujuan->alasan)
                                    '<br><span class="text-danger">Alasan: {{ $tujuan->alasan }}</span>'
                                @else
                                    ''
                                @endif )
                        );

                        routePoints.push(coord);
                    })();
                @endif
            @endforeach

            routePoints.push(tpaCoord);

            // Draw line route path
            L.polyline(routePoints, {
                color: '#4b7bec',
                weight: 4,
                opacity: 0.8,
                dashArray: '8, 8'
            }).addTo(map);

            // Adjust bounds to fit all nodes
            const bounds = L.latLngBounds(routePoints);
            map.fitBounds(bounds, {
                padding: [40, 40]
            });
        });
    </script>
@endpush
