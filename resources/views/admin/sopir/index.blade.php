@extends('admin.template')
@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#createModal">+
                Sopir</button>
            <div class="white-box">
                <h3 class="box-title">Data Sopir</h3>
                <div class="table-responsive">
                    <table id="dataTables" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sopir as $w)
                                <tr class="align-middle">
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $w->name }}</td>
                                    <td>{{ $w->username }}</td>
                                    <td>{{ $w->email }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit" 
                                            data-id="{{ $w->id }}" 
                                            data-name="{{ $w->name }}" 
                                            data-username="{{ $w->username }}"
                                            data-email="{{ $w->email }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('admin.sopir.destroy', $w->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus data sopir ini?');">
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

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sopir.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Sopir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required placeholder="Masukkan username">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="Masukkan alamat email">
                        </div>
                        <div class="alert alert-info py-2 mb-0 mt-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Info:</strong> Password akun baru akan disamakan secara default dengan <strong>Username</strong> yang Anda inputkan di atas.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Sopir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="edit_name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required placeholder="Masukkan username">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required placeholder="Masukkan alamat email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const username = button.getAttribute('data-username');
                    const email = button.getAttribute('data-email');

                    const form = editModal.querySelector('form');
                    form.setAttribute('action', `{{ url('/admin/sopir') }}/${id}`);

                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_username').value = username;
                    document.getElementById('edit_email').value = email;
                });
            }
        });
    </script>
@endsection
