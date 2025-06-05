@extends('layouts.admin')
@section('container')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Users</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Daftar Users</h5>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUsersModal">Tambah User</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Foto Profile</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Bidang</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($user->foto_profile)
                                                        <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="{{ $user->nama }}" width="100">
                                                    @else
                                                        <span>Tidak ada foto profile</span>
                                                    @endif
                                                </td>

                                                <td>{{ $user->nama }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{$user->role}}</td>
                                                <td>{{ $user->bidang ? $user->bidang->nama_bidang : 'Belum Ada Bidang' }}</td>
                                                <td>
                                                    <a href="#" class="btn btn-warning btn-sm " data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}"><i class="bi bi-pen"></i></a>
                                                    <form action="#" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-url="{{route('admin.hapusUsers', $user->id)}}">
                                                            <i class="bi bi-trash"></i>
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
            </div>
        </section>

    </main>

    @foreach($users as $user)
        <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.updateUsers', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="kadis" {{ $user->role == 'kadis' ? 'selected' : '' }}>Kadis</option>
                                    <option value="kabid" {{ $user->role == 'kabid' ? 'selected' : '' }}>Kabid</option>
                                    <option value="sekretaris" {{ $user->role == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="id_bidang" class="form-label">Bidang</label>
                                <select name="id_bidang" class="form-select">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach($bidangs as $bidang)
                                        <option value="{{ $bidang->id }}" {{ $user->id_bidang == $bidang->id ? 'selected' : '' }}>
                                            {{ $bidang->nama_bidang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_bidang" class="form-label">Update Bidang Baru (Opsional)</label>
                                <input type="text" name="nama_bidang" class="form-control" id="nama_bidang">
                            </div>
                            <div class="mb-3">
                                <label for="foto_profile" class="form-label">Foto Profile</label>
                                <input type="file" name="foto_profile" class="form-control" id="foto_profile">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah ftp_get_option.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUsersModal" tabindex="-1" aria-labelledby="addUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.storeUsers') }}" method="POST" enctype="multipart/form-data">>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUsersModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="pegawai">Pegawai</option>
                                <option value="kadis">Kadis</option>
                                <option value="kabid">Kabid</option>
                                <option value="sekretaris">Sekretaris</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_bidang" class="form-label">Bidang</label>
                            <select name="id_bidang" class="form-select">
                                <option value="">-- Pilih Bidang --</option>
                                @foreach($bidangs as $bidang)
                                    <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nama_bidang" class="form-label">Tambah Bidang Baru (Opsional)</label>
                            <input type="text" name="nama_bidang" class="form-control" id="nama_bidang">
                        </div>
                        <div class="mb-3">
                            <label for="foto_profile" class="form-label">Foto Profile</label>
                            <input type="file" name="foto_profile" class="form-control" id="foto_profile">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection