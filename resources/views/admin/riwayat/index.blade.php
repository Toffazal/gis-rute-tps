@extends('admin.template')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="white-box">
                <h3 class="box-title mb-4">Riwayat & Monitoring Rute Sopir</h3>

                <!-- Filter Panel -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.riwayat.index') }}" class="row g-3">
                            <!-- Filter Sopir -->
                            <div class="col-md-3 col-sm-6 col-12">
                                <label for="user_id" class="form-label fw-bold text-muted small">Filter
                                    Sopir</label>
                                <select class="form-select" id="select1" style="width:100%;" name="user_id">
                                    <option value="">-- Semua Sopir --</option>
                                    @foreach ($sopirList as $sopir)
                                        <option value="{{ $sopir->id }}"
                                            {{ request('user_id') == $sopir->id ? 'selected' : '' }}>
                                            {{ $sopir->name }} ({{ $sopir->username }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="col-md-3 col-sm-6 col-12">
                                <label for="status" class="form-label fw-bold text-muted small">Filter
                                    Status</label>
                                <select id="select2" class="form-select" style="width:100%;" name="status">
                                    <option value="">-- Semua Status --</option>
                                    <option value="belum_mulai" {{ request('status') == 'belum_mulai' ? 'selected' : '' }}>
                                        Belum Mulai</option>
                                    <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>
                                        Berlangsung (Aktif)</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                </select>
                            </div>

                            <!-- Filter Tanggal -->
                            <div class="col-md-4 col-sm-8 col-12">
                                <label for="daterange" class="form-label fw-bold text-muted small">Rentang Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i
                                            class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" class="form-control bg-white" id="daterange" name="daterange"
                                        placeholder="Pilih Rentang Tanggal" value="{{ request('daterange') }}" readonly>
                                </div>
                            </div>

                            <!-- Reset Button (Only shows when filters are active) -->
                            @if (request()->filled('user_id') || request()->filled('status') || request()->filled('daterange'))
                                <div class="col-md-2 col-sm-4 col-12 d-flex align-items-end">
                                    <a href="{{ route('admin.riwayat.index') }}" class="btn btn-secondary text-white w-100">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                @if ($riwayat->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-4x mb-3 text-light"></i>
                        <p class="mb-0">Tidak ada data riwayat rute yang sesuai dengan filter.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Tanggal</th>
                                    <th>Sopir</th>
                                    <th>Angkutan</th>
                                    <th class="text-center">Total TPS</th>
                                    <th>Total Jarak</th>
                                    <th>Durasi Perjalanan</th>
                                    <th class="text-center">Status</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $idx => $rute)
                                    @php
                                        $tpsCount = $rute->ruteTujuan->where('tipe', 'tps')->count();

                                        // Safety check for negative total_durasi_detik
                                        $durasiSeconds = abs($rute->total_durasi_detik);
                                        $jam = floor($durasiSeconds / 3600);
                                        $menit = floor(($durasiSeconds % 3600) / 60);

                                        $statusBadge = match ($rute->status) {
                                            'selesai' => 'bg-success text-white',
                                            'berlangsung' => 'bg-primary text-white',
                                            'belum_mulai' => 'bg-light text-dark border',
                                            default => 'bg-secondary text-white',
                                        };
                                        $statusLabel = match ($rute->status) {
                                            'selesai' => 'Selesai',
                                            'berlangsung' => 'Berlangsung',
                                            'belum_mulai' => 'Belum Mulai',
                                            default => ucfirst($rute->status),
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $riwayat->firstItem() + $idx }}</td>
                                        <td>
                                            <span
                                                class="fw-semibold">{{ $rute->tanggal->translatedFormat('l, d M Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2 fs-5"></i>
                                                <div>
                                                    <strong
                                                        class="text-dark">{{ $rute->user->name ?? 'Sopir Terhapus' }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $rute->angkutan->nama ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $rute->angkutan->nomor ?? '-' }}</small>
                                        </td>
                                        <td class="text-center font-bold text-dark">
                                            {{ $tpsCount }}
                                        </td>
                                        <td>
                                            <span class="text-primary font-bold">
                                                {{ number_format(($rute->total_jarak_meter ?? 0) / 1000, 1) }} km
                                            </span>
                                        </td>
                                        <td>
                                            @if ($rute->status === 'belum_mulai')
                                                <span class="text-muted">-</span>
                                            @elseif ($rute->status === 'berlangsung')
                                                <span class="badge bg-info text-white"><i
                                                        class="fas fa-spinner fa-spin me-1"></i> Sedang Jalan</span>
                                            @elseif ($rute->total_durasi_detik)
                                                {{ $jam > 0 ? $jam . ' jam ' : '' }}{{ $menit }} menit
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusBadge }}"
                                                style="padding: 6px 12px; border-radius: 20px;">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($rute->status === 'belum_mulai')
                                                <button class="btn btn-sm btn-outline-secondary" disabled
                                                    title="Belum dimulai">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('admin.riwayat.show', $rute->id) }}"
                                                    class="btn btn-sm btn-primary text-white"
                                                    title="{{ $rute->status === 'berlangsung' ? 'Pantau Rute' : 'Lihat Detail' }}">
                                                    <i
                                                        class="fas {{ $rute->status === 'berlangsung' ? 'fa-desktop' : 'fa-eye' }}"></i>
                                                    {{ $rute->status === 'berlangsung' ? ' Pantau' : ' Detail' }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Date Range Picker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'MM/DD/YYYY'
                }
            });

            // Submit on Date Apply
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
                $(this).closest('form').submit();
            });

            // Submit on Date Clear
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $(this).closest('form').submit();
            });

            // Submit on Select Dropdowns Change
            $('#select1, #select2').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
