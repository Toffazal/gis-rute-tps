@extends('sopir.template')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="white-box">
                <h3 class="box-title">Riwayat Pengangkutan</h3>

                @if ($riwayat->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada riwayat pengangkutan.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Tanggal</th>
                                    <th>Angkutan</th>
                                    <th>Jumlah TPS</th>
                                    <th>Total Jarak</th>
                                    <th>Durasi</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $idx => $rute)
                                    <tr>
                                        <td>{{ $riwayat->firstItem() + $idx }}</td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-primary"></i>
                                            {{ $rute->tanggal->translatedFormat('l, d M Y') }}
                                        </td>
                                        <td>
                                            <i class="fas fa-truck me-1 text-success"></i>
                                            {{ $rute->angkutan->nama ?? '-' }}
                                            <small class="text-muted">({{ $rute->angkutan->nomor ?? '-' }})</small>
                                        </td>
                                        <td class="text-center">
                                            {{ $rute->ruteTujuan->where('tipe', 'tps')->count() }}
                                        </td>
                                        <td>
                                            {{ number_format(($rute->total_jarak_meter ?? 0) / 1000, 1) }} km
                                        </td>
                                        <td>
                                            @if ($rute->total_durasi_detik)
                                                @php
                                                    $durasi = abs($rute->total_durasi_detik);
                                                    $jam = floor($durasi / 3600);
                                                    $menit = floor(($durasi % 3600) / 60);
                                                @endphp
                                                {{ $jam > 0 ? $jam . ' jam ' : '' }}{{ $menit }} menit
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('sopir.riwayat.show', $rute->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
