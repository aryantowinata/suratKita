@extends('layouts.admin')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Surat Masuk</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Surat Masuk</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Daftar Surat Masuk</h5>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addSuratMasukModal">Tambah Surat Masuk</a>
                        </div>

                        <!-- Table with stripped rows -->
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal </th>
                                        <th>No Surat</th>
                                        <th>No Agenda</th>
                                        <th>Perihal </th>
                                        <th>Pengirim </th>
                                        <th>Jenis </th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($surats as $surat)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $surat->tanggal_surat}}</td>
                                        <td>{{ $surat->nomor_surat}}</td>
                                        <td>{{ $surat->no_agenda}}</td>
                                        <td>{{ $surat->perihal}}</td>
                                        <td>{{ $surat->pengirim}}</td>
                                        <td>{{ $surat->jenis_surat}}</td>
                                        <td>
                                            @if($surat->file_surat)
                                            <a href="{{ route('admin.surat-masuk.download', $surat->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.updateStatusSuratMasuk', $surat->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="baru"
                                                        {{ $surat->status == 'baru' ? 'selected' : '' }}>Baru</option>
                                                    <option value="diterima"
                                                        {{ $surat->status == 'diterima' ? 'selected' : '' }}>Diterima
                                                    </option>
                                                    <option value="ditindaklanjuti"
                                                        {{ $surat->status == 'ditindaklanjuti' ? 'selected' : '' }}>
                                                        Ditindaklanjuti</option>
                                                    <option value="selesai"
                                                        {{ $surat->status == 'selesai' ? 'selected' : '' }}>Selesai
                                                    </option>

                                                </select>
                                            </form>
                                        </td>

                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editSuratMasukModal-{{ $surat->id }}"><i
                                                    class="bi bi-pen"></i></a>
                                            <form action="{{ route('admin.hapusSuratMasuk', $surat->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-url="{{ route('admin.hapusSuratMasuk', $surat->id) }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->


<!-- Modal Tambah Surat -->
<div class="modal fade" id="addSuratMasukModal" tabindex="-1" aria-labelledby="addSuratMasukModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.storeSuratMasuk')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSuratMasukModalLabel">Tambah Surat Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Input fields -->
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control" id="nomor_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_agenda" class="form-label">Nomor Agenda</label>
                        <input type="text" name="no_agenda" class="form-control" id="no_agenda" required>
                    </div>

                    <div class="mb-3">
                        <label for="pengirim" class="form-label">Pengirim</label>
                        <input type="text" name="pengirim" class="form-control" id="pengirim" required>
                    </div>
                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" name="perihal" class="form-control" id="perihal" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" id="tanggal_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" name="status" class="form-control" id="status" readonly value="baru">
                    </div>
                    <div class="mb-3">
                        <label for="jenis_surat" class="form-label">Jenis Surat</label>
                        <input type="text" name="jenis_surat" class="form-control" id="jenis_surat" required>
                    </div>
                    <div class="mb-3">
                        <label for="file_surat" class="form-label">File Surat</label>
                        <input type="file" name="file_surat" class="form-control" id="file_surat">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Surat Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Surat -->
@foreach($surats as $surat)
<div class="modal fade" id="editSuratMasukModal-{{ $surat->id }}" tabindex="-1"
    aria-labelledby="editSuratMasukModalLabel-{{ $surat->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.updateSuratMasuk', $surat->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Surat Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control" value="{{ $surat->nomor_surat }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Agenda</label>
                        <input type="text" name="no_agenda" class="form-control" value="{{ $surat->no_agenda }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pengirim</label>
                        <input type="text" name="pengirim" class="form-control" value="{{ $surat->pengirim }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Perihal</label>
                        <input type="text" name="perihal" class="form-control" value="{{ $surat->perihal }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" value="{{ $surat->tanggal_surat }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Surat</label>
                        <select name="status" class="form-select">
                            <option value="baru" {{ $surat->status == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="diterima" {{ $surat->status == 'diterima' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="ditindaklanjuti" {{ $surat->status == 'ditindaklanjuti' ? 'selected' : '' }}>
                                Ditindaklanjuti
                            </option>
                            <option value="selesai" {{ $surat->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="diarsipkan" {{ $surat->status == 'diarsipkan' ? 'selected' : '' }}>Diarsipkan
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_surat" class="form-label">Jenis Surat</label>
                        <input type="text" name="jenis_surat" class="form-control" id="jenis_surat"
                            value="{{$surat->jenis_surat}}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tujuan Surat (Role & Bidang)</label>
                        @foreach($rolesWithBidang as $user)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="id_role[]" value="{{ $user->id }}"
                                id="role-{{ $user->id }}-{{ $surat->id ?? 'new' }}"
                                {{ isset($surat) && $surat->roles->contains($user->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role-{{ $user->id }}-{{ $surat->id ?? 'new' }}">
                                {{ ucfirst($user->role) }} - {{ $user->bidang->nama_bidang ?? 'Tidak Ada Bidang' }}
                            </label>
                        </div>
                        @endforeach
                    </div>





                    <div class="mb-3">
                        <label class="form-label">File Surat (Opsional)</label>
                        <input type="file" name="file_surat" class="form-control">
                        @if($surat->file_surat)
                        <small class="text-muted">
                            File saat ini:
                            <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank">
                                Lihat / Unduh
                            </a>
                        </small>
                        @endif
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


@endsection