@extends('admin.template')
@section('head')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <style>
        .kpi-card {
            border: none;
            border-radius: 12px;
            color: #fff;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .kpi-card .kpi-icon {
            position: absolute;
            right: 15px;
            bottom: 10px;
            font-size: 70px;
            opacity: 0.2;
            transition: transform 0.3s ease;
        }

        .kpi-card:hover .kpi-icon {
            transform: scale(1.1) rotate(-10deg);
        }

        .kpi-blue {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .kpi-orange {
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        }

        .kpi-teal {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .kpi-purple {
            background: linear-gradient(135deg, #7F00FF 0%, #E100FF 100%);
        }

        .kpi-card h3 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 5px;
            color: #fff;
        }

        .kpi-card p {
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0;
            opacity: 0.9;
        }

        .chart-box {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .chart-title i {
            margin-right: 10px;
        }

        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background-color: #f1f3f5;
            overflow: hidden;
        }

        .progress-bar-custom .progress-fill {
            height: 100%;
            border-radius: 5px;
            transition: width 0.6s ease;
        }

        .badge-active {
            background-color: #0d6efd;
            color: #fff;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
        }

        .table-custom th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            color: #6c757d;
            background-color: #f8f9fa;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 13px;
        }
    </style>

    <div class="row">
        <!-- KPI 1: TPS -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="kpi-card kpi-blue">
                <h3>{{ $tpsCount }}</h3>
                <p>Total TPS</p>
                <div class="kpi-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>

        <!-- KPI 2: Sopir -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="kpi-card kpi-orange">
                <h3>{{ $sopirCount }}</h3>
                <p>Total Sopir</p>
                <div class="kpi-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>

        <!-- KPI 3: Angkutan -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="kpi-card kpi-teal">
                <h3>{{ $angkutanCount }}</h3>
                <p>Total Angkutan</p>
                <div class="kpi-icon">
                    <i class="fas fa-truck-moving"></i>
                </div>
            </div>
        </div>

        <!-- KPI 4: Rute Hari Ini -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="kpi-card kpi-purple">
                <h3>{{ $ruteAktifHariIni + $ruteSelesaiHariIni }}</h3>
                <p>Rute Hari Ini</p>
                <div class="kpi-icon">
                    <i class="fas fa-route"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Chart 1: Riwayat 7 Hari Terakhir -->
        <div class="col-lg-8 col-12">
            <div class="chart-box">
                <div class="chart-title">
                    <i class="fas fa-chart-line text-primary"></i> Kinerja Pengangkutan (7 Hari Terakhir)
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Status TPS Hari Ini -->
        <div class="col-lg-4 col-12">
            <div class="chart-box">
                <div class="chart-title">
                    <i class="fas fa-chart-pie text-warning"></i> Status TPS Hari Ini
                </div>
                <div
                    style="position: relative; height: 320px; display: flex; align-items: center; justify-content: center;">
                    @if ($tpsSelesai == 0 && $tpsSkip == 0 && $tpsMenunggu == 0)
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Belum ada rute/tujuan hari ini.</p>
                        </div>
                    @else
                        <canvas id="todayTpsChart"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Active Driver Rute Monitoring -->
    <div class="row">
        <div class="col-12">
            <div class="chart-box">
                <div class="chart-title mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-circle-notch fa-spin text-danger me-2"></i>
                        <strong>Pemantauan Rute Aktif Hari Ini</strong>
                    </div>
                    <a href="{{ route('admin.riwayat.index', ['status' => 'berlangsung']) }}"
                        class="btn btn-sm btn-outline-primary">
                        Semua Rute Aktif
                    </a>
                </div>

                @if ($activeRoutes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-route fa-3x mb-3 text-light"></i>
                        <p class="mb-0">Tidak ada sopir yang sedang menjalankan rute saat ini.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Sopir</th>
                                    <th>Angkutan</th>
                                    <th>Waktu Mulai</th>
                                    <th>Progress TPS</th>
                                    <th width="35%">Bar Progress</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeRoutes as $route)
                                    @php
                                        $tpsList = $route->ruteTujuan->where('tipe', 'tps');
                                        $totalTps = $tpsList->count();
                                        $selesaiTps = $tpsList->where('status', 'selesai')->count();
                                        $skipTps = $tpsList
                                            ->whereIn('status', ['dilewati', 'tidak_dikunjungi'])
                                            ->count();
                                        $progressPercent =
                                            $totalTps > 0 ? round((($selesaiTps + $skipTps) / $totalTps) * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/user.png') }}" class="rounded-circle me-2"
                                                    width="30" height="30">
                                                <div>
                                                    <span class="fw-bold">{{ $route->user->name ?? '-' }}</span><br>
                                                    <small class="text-muted">Username:
                                                        {{ $route->user->username ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $route->angkutan->nama ?? '-' }}</span><br>
                                            <small class="text-muted">{{ $route->angkutan->nomor ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <i class="far fa-clock me-1 text-primary"></i>
                                            {{ $route->waktu_mulai ? $route->waktu_mulai->format('H:i:s') : '-' }}
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">{{ $selesaiTps }} selesai</span>,
                                            <span class="text-danger">{{ $skipTps }} skip</span>
                                            <span class="text-muted">/ {{ $totalTps }} TPS</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2 text-muted fw-bold">{{ $progressPercent }}%</span>
                                                <div class="progress-bar-custom flex-grow-1">
                                                    <div class="progress-fill bg-success"
                                                        style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.riwayat.show', $route->id) }}"
                                                class="btn btn-sm btn-primary text-white">
                                                <i class="fas fa-desktop me-1"></i> Pantau Rute
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Load Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Riwayat Kinerja Pengangkutan
            const historyCtx = document.getElementById('historyChart').getContext('2d');
            new Chart(historyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartHistoryLabels) !!},
                    datasets: [{
                        label: 'Total Rute Selesai',
                        data: {!! json_encode($chartHistoryCount) !!},
                        backgroundColor: 'rgba(30, 60, 114, 0.85)',
                        borderColor: 'rgb(30, 60, 114)',
                        borderWidth: 1,
                        yAxisID: 'yCount',
                        borderRadius: 4
                    }, {
                        label: 'Jarak Tempuh (km)',
                        data: {!! json_encode($chartHistoryJarak) !!},
                        type: 'line',
                        borderColor: '#38ef7d',
                        backgroundColor: 'rgba(56, 239, 125, 0.1)',
                        pointBackgroundColor: '#11998e',
                        borderWidth: 3,
                        tension: 0.3,
                        yAxisID: 'yJarak'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yCount: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Rute Selesai',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                drawOnChartArea: true
                            }
                        },
                        yJarak: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Jarak (km)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });

            // Chart 2: Status TPS Hari Ini
            @if (!($tpsSelesai == 0 && $tpsSkip == 0 && $tpsMenunggu == 0))
                const todayTpsCtx = document.getElementById('todayTpsChart').getContext('2d');
                new Chart(todayTpsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Dilewati/Skip', 'Pending/Aktif'],
                        datasets: [{
                            data: [{{ $tpsSelesai }}, {{ $tpsSkip }},
                                {{ $tpsMenunggu }}],
                            backgroundColor: [
                                '#198754', // Selesai -> Green
                                '#dc3545', // Dilewati -> Red
                                '#6c757d' // Pending -> Gray
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        cutout: '65%'
                    }
                });
            @endif
        });
    </script>
@endpush
