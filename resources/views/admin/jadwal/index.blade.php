@extends('admin.template')
@section('content')
    <!-- Filter Hari -->
    <form action="{{ route('admin.jadwal.index') }}" method="GET" class="row align-items-center mb-2">
        <div class="col-lg-3 col-md-4 col-12 d-flex gap-2 align-items-center">
            <select name="hari" id="hari" class="form-select form-control" onchange="this.form.submit()">
                <option value="">Semua Hari (Filter)</option>
                @foreach ($hariList as $h)
                    <option value="{{ $h }}" {{ $filterHari == $h ? 'selected' : '' }}>{{ $h }}
                    </option>
                @endforeach
            </select>
            @if ($filterHari)
                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary btn-sm" title="Reset Filter">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </div>
    </form>

    <div class="row">
        <div class="col-12 mb-3">
            <!-- Dropdown Button for Excel Actions -->
            <div class="dropdown float-end">
                <button class="btn btn-success btn-sm text-white dropdown-toggle" type="button" id="dropdownExcel"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownExcel">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.jadwal.export', ['hari' => $filterHari]) }}">
                            <i class="fas fa-file-export me-2 text-success"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-2 text-primary"></i> Import Excel
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.jadwal.template') }}">
                            <i class="fas fa-download me-2 text-info"></i> Download Template
                        </a>
                    </li>
                </ul>
            </div>

            <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary btn-sm float-end">+ Jadwal</a>

            <div class="white-box">
                <h3 class="box-title">Jadwal Pengangkutan</h3>

                <div class="table-responsive">
                    <table id="dataTables" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sopir</th>
                                <th>TPS</th>
                                <th>Angkutan (No. Polisi)</th>
                                <th>Hari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $w)
                                <tr class="align-middle">
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $w->user->name ?? 'Tidak ada Sopir' }}</td>
                                    <td>{{ $w->tps->nama ?? 'Tidak ada TPS' }}</td>
                                    <td>
                                        @if ($w->angkutan)
                                            {{ $w->angkutan->nama }} ({{ $w->angkutan->nomor }})
                                        @else
                                            Tidak ada Angkutan
                                        @endif
                                    </td>
                                    <td><span class="badge bg-primary px-3 py-1"
                                            style="border-radius: 35px;">{{ $w->hari }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.jadwal.edit', $w->id) }}"
                                            class="btn btn-warning btn-sm text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.jadwal.destroy', $w->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Jadwal dari Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="import_file" class="form-label fw-bold">Pilih Berkas Excel (.xlsx, .xls,
                                .csv)</label>
                            <input type="file" name="file" id="import_file" class="form-control" required
                                accept=".xlsx, .xls, .csv">
                        </div>
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Pastikan data Excel Anda sudah sesuai dengan format template yang disediakan.
                            Silakan unduh template terlebih dahulu jika belum memilikinya.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
