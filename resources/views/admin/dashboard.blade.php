@extends('admin.template')
@section('content')
    <h1>Selamat datang, {{ $user->name }}</h1>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Data TPS</h3>
                <ul class="list-inline two-part d-flex align-items-center mb-0">
                    <li>
                        <i class="fas fa-map-marker-alt text-primary fa-2x"></i>
                    </li>
                    <li class="ms-auto"><span class="counter text-primary">{{ $tps }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Riwayat Kunjungan</h3>
                <ul class="list-inline two-part d-flex align-items-center mb-0">
                    <li>
                        <i class="fas fa-clock-rotate-left text-primary fa-2x"></i>
                    </li>
                    <li class="ms-auto"><span class="counter text-primary">0</span></li>
                </ul>
            </div>
        </div>
    </div>
@endsection
