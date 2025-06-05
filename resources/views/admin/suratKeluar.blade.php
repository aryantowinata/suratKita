@extends('layouts.admin')
@section('container')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Surat Keluar</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Surat Keluar</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Daftar Surat Keluar</h5>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addSuratKeluarModal">Tambah Surat Keluar</a>
                            </div>

                            <!-- Table with stripped rows -->
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Surat</th>
                                            <th>Pengirim </th>
                                            <th>Tujuan </th>
                                            <th>Perihal </th>
                                            <th>Tanggal </th>
                                            <th>Jenis </th>
                                            <th>File </th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($surats as $surat)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $surat->nomor_surat}}</td>
                                                <td>
                                                    {{$surat->pengirim}}
                                                </td>
                                                <td>{{ $surat->tujuan}}</td>
                                                <td>{{ $surat->perihal}}</td>
                                                <td>{{ $surat->tanggal_surat}}</td>
                                                <td>{{ $surat->jenis_surat}}</td>
                                                <td>
                                                    @if($surat->file_surat)
                                                        <a href="{{ route('admin.surat-keluar.download', $surat->id) }}"
                                                            class="btn btn-success btn-sm">
                                                            <i class="bi bi-download"></i> Unduh
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Tidak ada file</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.updateStatusSuratKeluar', $surat->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="status" class="form-select form-select-sm"
                                                            onchange="this.form.submit()">
                                                            <option value="menunggu"
                                                                {{ $surat->status == 'menunggu' ? 'selected' : '' }}>Menunggu
                                                            </option>
                                                            <option value="ditolak"
                                                                {{ $surat->status == 'ditolak' ? 'selected' : '' }}>Ditolak
                                                            </option>
                                                            <option value="disetujui"
                                                                {{ $surat->status == 'disetujui' ? 'selected' : '' }}>Disetujui
                                                            </option>

                                                        </select>
                                                    </form>
                                                </td>

                                                <td>
                                                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editSuratKeluarModal-{{ $surat->id }}"><i
                                                            class="bi bi-pen"></i></a>
                                                    <form action="{{ route('admin.hapusSuratKeluar', $surat->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-url="{{ route('admin.hapusSuratKeluar', $surat->id) }}">
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
    <div class="modal fade" id="addSuratKeluarModal" tabindex="-1" aria-labelledby="addSuratKeluarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.storeSuratKeluar')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSuratMasukModalLabel">Tambah Surat Keluar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Input fields -->
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                            <input type="text" name="nomor_surat" class="form-control" id="nomor_surat" required>
                        </div>

                        <div class="mb-3">
                            <label for="pengirim" class="form-label">Pengirim</label>
                            <input type="text" name="pengirim" class="form-control" id="pengirim" required>
                        </div>
                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" id="tujuan" required>
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
                            <input type="text" name="status" class="form-control" id="status" readonly value="menunggu">
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
                        <button type="submit" class="btn btn-primary">Simpan Surat Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Surat -->
    @foreach($surats as $surat)
        <div class="modal fade" id="editSuratKeluarModal-{{ $surat->id }}" tabindex="-1"
            aria-labelledby="editSuratKeluarModalLabel-{{ $surat->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.updateSuratKeluar', $surat->id) }}" method="POST"
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
                                <label class="form-label">Pengirim</label>
                                <input type="text" name="pengirim" class="form-control" value="{{ $surat->pengirim }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tujuan</label>
                                <input type="text" name="tujuan" class="form-control" value="{{ $surat->tujuan }}" required>
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
                                    <option value="menunggu" {{ $surat->status == 'menunggu' ? 'selected' : '' }}>Menunggu
                                    </option>
                                    <option value="disetujui" {{ $surat->status == 'disetujui' ? 'selected' : '' }}>Disetujui
                                    </option>
                                    <option value="ditolak" {{ $surat->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_surat" class="form-label">Jenis Surat</label>
                                <input type="text" name="jenis_surat" class="form-control" id="jenis_surat"
                                    value="{{$surat->jenis_surat}}">
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