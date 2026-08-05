@extends('sopir.template')
@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('sopir.tps.create') }}" class="btn btn-primary btn-sm float-end">+
                TPS</a>
            <div class="white-box">
                <h3 class="box-title">Data TPS</h3>
                <div class="table-responsive">
                    <table id="dataTables" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>TPS</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tps as $w)
                                <tr class="align-middle">
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $w->nama }}
                                    </td>
                                    <td>{{ $w->alamat }}</td>
                                    <td>
                                        <a href="{{ route('sopir.tps.edit', $w->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('sopir.tps.destroy', $w->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus data?');">
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
@endsection
