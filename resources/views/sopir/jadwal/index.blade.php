@extends('sopir.template')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="white-box">
                <h3 class="box-title">Jadwal Pengangkutan Saya</h3>

                @foreach ($hariList as $hari)
                    @php
                        $jadwalHari = $jadwalAll->get($hari, collect());
                    @endphp
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary">
                            <i class="fas fa-calendar-day me-2"></i>{{ $hari }}
                            <span class="badge bg-light text-dark ms-2">{{ $jadwalHari->count() }} jadwal</span>
                        </h5>

                        @if ($jadwalHari->isEmpty())
                            <p class="text-muted ms-4"><em>Tidak ada jadwal</em></p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>TPS</th>
                                            <th>Angkutan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwalHari as $idx => $jadwal)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                    {{ $jadwal->tps->nama ?? '-' }}
                                                </td>
                                                <td>
                                                    <i class="fas fa-truck text-success me-1"></i>
                                                    {{ $jadwal->angkutan->nama ?? '-' }}
                                                    <small class="text-muted">({{ $jadwal->angkutan->nomor ?? '-' }})</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
