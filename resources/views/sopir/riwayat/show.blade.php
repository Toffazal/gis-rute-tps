@extends('sopir.template')
@section('content')
    <style>
        #riwayat-map {
            height: 400px;
            border-radius: 10px;
            border: 2px solid #dee2e6;
            z-index: 1;
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
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #0d6efd;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 15px;
            width: 10px;
            height: 10px;
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

        .timeline-item.aksi-skip {
            border-left-color: #dc3545;
        }

        .timeline-item.aksi-skip::before {
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
    </style>

    <div class="mb-2">
        <a href="{{ route('sopir.riwayat.index') }}" class="btn btn-secondary btn-sm text-white">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Info Header --}}
    <div class="white-box">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <small class="text-muted">Tanggal</small>
                <p class="fw-bold mb-0">{{ $ruteHarian->tanggal->translatedFormat('l, d M Y') }}</p>
            </div>
            <div class="col-md-3 col-6">
                <small class="text-muted">Angkutan</small>
                <p class="fw-bold mb-0">
                    {{ $ruteHarian->angkutan->nama ?? '-' }}
                    <small class="text-muted">({{ $ruteHarian->angkutan->nomor ?? '-' }})</small>
                </p>
            </div>
            <div class="col-md-3 col-6">
                <small class="text-muted">Total Jarak</small>
                <p class="fw-bold mb-0 text-primary">
                    {{ number_format(($ruteHarian->total_jarak_meter ?? 0) / 1000, 1) }} km
                </p>
            </div>
            <div class="col-md-3 col-6">
                <small class="text-muted">Durasi</small>
                <p class="fw-bold mb-0">
                    @if ($ruteHarian->total_durasi_detik)
                        @php
                            $durasi = abs($ruteHarian->total_durasi_detik);
                            $jam = floor($durasi / 3600);
                            $menit = floor(($durasi % 3600) / 60);
                        @endphp
                        {{ $jam > 0 ? $jam . ' jam ' : '' }}{{ $menit }} menit
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="white-box">
        <h6 class="fw-bold mb-3"><i class="fas fa-map me-2 text-primary"></i>Peta Rute</h6>
        <div id="riwayat-map"></div>
    </div>

    <div class="row">
        {{-- Destination List --}}
        <div class="col-md-6 col-12">
            <div class="white-box">
                <h6 class="fw-bold mb-3"><i class="fas fa-list-ol me-2 text-primary"></i>Daftar Tujuan</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Jarak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ruteHarian->ruteTujuan as $tujuan)
                                <tr>
                                    <td>{{ $tujuan->urutan }}</td>
                                    <td>
                                        @if ($tujuan->tipe === 'tpa')
                                            <i class="fas fa-industry text-danger me-1"></i> TPA
                                        @else
                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                            {{ $tujuan->tps->nama ?? 'TPS' }}
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = match ($tujuan->status) {
                                                'selesai' => 'bg-success',
                                                'dilewati' => 'bg-danger',
                                                'tidak_dikunjungi' => 'bg-secondary',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">{{ ucfirst($tujuan->status) }}</span>
                                        @if ($tujuan->alasan)
                                            <br><small class="text-muted">{{ $tujuan->alasan }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($tujuan->jarak_dari_sebelumnya_meter / 1000, 1) }} km</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Timeline Log --}}
        <div class="col-md-6 col-12">
            <div class="white-box">
                <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2 text-primary"></i>Timeline Aktivitas</h6>

                @if ($ruteHarian->ruteLog->isEmpty())
                    <p class="text-muted text-center">Tidak ada log aktivitas.</p>
                @else
                    <div class="timeline">
                        @foreach ($ruteHarian->ruteLog as $log)
                            @php
                                $aksiClass = match ($log->aksi) {
                                    'selesai' => 'aksi-selesai',
                                    'skip' => 'aksi-skip',
                                    'tiba' => 'aksi-tiba',
                                    default => '',
                                };
                                $aksiLabel = match ($log->aksi) {
                                    'mulai_rute' => '<i class="fas fa-play-circle text-success me-1"></i> Mulai Rute',
                                    'tiba' => '<i class="fas fa-map-pin text-info me-1"></i> Tiba',
                                    'selesai' => '<i class="fas fa-check-circle text-success me-1"></i> Selesai',
                                    'skip' => '<i class="fas fa-forward text-danger me-1"></i> Dilewati',
                                    'tidak_dikunjungi' => '<i class="fas fa-times-circle text-secondary me-1"></i> Tidak Dikunjungi',
                                    default => ucfirst(str_replace('_', ' ', $log->aksi)),
                                };
                            @endphp
                            <div class="timeline-item {{ $aksiClass }}">
                                <div class="d-flex justify-content-between">
                                    <strong>{!! $aksiLabel !!}</strong>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </div>
                                @if ($log->ruteTujuan)
                                    <small class="text-muted">
                                        @if ($log->ruteTujuan->tipe === 'tpa')
                                            TPA
                                        @else
                                            {{ $log->ruteTujuan->tps->nama ?? 'TPS' }}
                                        @endif
                                    </small>
                                @endif
                                @if ($log->alasan)
                                    <br><small class="text-danger">Alasan: {{ $log->alasan }}</small>
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

            const map = L.map('riwayat-map').setView(kantorCoord, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

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

            L.marker(kantorCoord, {
                icon: kantorIcon
            }).addTo(map).bindPopup('<strong><i class="fas fa-building text-success me-1"></i> Kantor (Start)</strong>');
            L.marker(tpaCoord, {
                icon: tpaIcon
            }).addTo(map).bindPopup('<strong><i class="fas fa-industry text-danger me-1"></i> TPA (Finish)</strong>');

            const routePoints = [kantorCoord];

            @foreach ($ruteHarian->ruteTujuan as $tujuan)
                @if ($tujuan->tipe === 'tps' && $tujuan->tps)
                    (function() {
                        const coord = [{{ $tujuan->tps->latitude }}, {{ $tujuan->tps->longitude }}];
                        const status = '{{ $tujuan->status }}';
                        let color = status === 'selesai' ? '#198754' : (status === 'dilewati' ? '#dc3545' :
                            '#6c757d');

                        const icon = L.divIcon({
                            html: '<div style="background:' + color +
                                ';color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,0.4);">{{ $tujuan->urutan }}</div>',
                            className: 'custom-div-icon',
                            iconSize: [24, 24],
                            iconAnchor: [12, 24],
                            popupAnchor: [0, -24]
                        });

                        L.marker(coord, {
                            icon: icon
                        }).addTo(map).bindPopup(
                            '<strong>#{{ $tujuan->urutan }} - {{ $tujuan->tps->nama }}</strong><br>' +
                            '<span class="badge" style="background:' + color +
                            '">{{ ucfirst($tujuan->status) }}</span>'
                        );
                        routePoints.push(coord);
                    })();
                @endif
            @endforeach

            routePoints.push(tpaCoord);

            L.polyline(routePoints, {
                color: '#0d6efd',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 6'
            }).addTo(map);

            const bounds = L.latLngBounds(routePoints);
            map.fitBounds(bounds, {
                padding: [30, 30]
            });
        });
    </script>
@endpush
