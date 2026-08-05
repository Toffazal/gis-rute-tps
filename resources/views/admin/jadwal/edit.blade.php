@extends('admin.template')
@section('content')
    <!-- Select2 CSS -->
    <link href="{{ asset('admin-assets/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin-assets/css/select2.custom.css') }}" rel="stylesheet" />

    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary btn-sm text-white mb-2"><i
            class="fas fa-angles-left"></i>Kembali</a>

    <div class="white-box">
        <h3 class="box-title mb-4">Edit Jadwal Pengangkutan</h3>

        <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="hari" class="form-label fw-bold">Hari</label>
                    <select name="hari" id="hari" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($hariList as $h)
                            <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>
                                {{ $h }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-6 col-12 mb-3">
                    <label for="user_id" class="form-label fw-bold">Sopir</label>
                    <select name="user_id" id="user_id" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($sopir as $s)
                            <option value="{{ $s->id }}" {{ $jadwal->user_id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->username }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="angkutan_id" class="form-label fw-bold">Angkutan</label>
                    <select name="angkutan_id" id="angkutan_id" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($angkutan as $a)
                            <option value="{{ $a->id }}" {{ $jadwal->angkutan_id == $a->id ? 'selected' : '' }}>
                                {{ $a->nama }} ({{ $a->nomor }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="tps_id" class="form-label fw-bold">TPS</label>
                    <select name="tps_id" id="tps_id" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($tps as $t)
                            <option value="{{ $t->id }}" {{ $jadwal->tps_id == $t->id ? 'selected' : '' }}>
                                {{ $t->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="border-top pt-3 mt-3 text-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <!-- Select2 JS -->
    <script src="{{ asset('admin-assets/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Opsi...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
