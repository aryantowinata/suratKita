@extends('layouts.pimpinan')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1> Surat Keluar</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('pimpinan.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active"> Surat Keluar</li>
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
                                        <th>Nomor </th>
                                        <th>Pengirim</th>
                                        <th>Perihal</th>
                                        <th>Jenis </th>
                                        <th>Instruksi</th>
                                        <th>Status </th>
                                        <th>File </th>
                                        <th>Tujuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $hasSurat = false;
                                    @endphp

                                    @foreach ($disposisis as $index => $disposisi)
                                    @if ($disposisi->suratKeluar)
                                    @php $hasSurat = true; @endphp
                                    <tr>
                                        <td>{{ $index + 0 }}</td>
                                        <td>{{ $disposisi->suratKeluar->nomor_surat ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratKeluar->pengirim ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratKeluar->perihal ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratKeluar->jenis_surat ?? 'Tidak Ada' }}</td>
                                        <td>
                                            @if ($allInstruksis->isEmpty())
                                            <span class="text-muted">Tidak ada instruksi</span>
                                            @else
                                            <form action="{{ route('pimpinan.updateInstruksi', $disposisi->id) }}"
                                                method="POST" class="instruksi-form">
                                                @csrf
                                                @method('PUT')
                                                @foreach ($allInstruksis as $instruksi)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="instruksi_ids[]" value="{{ $instruksi->id }}"
                                                        {{ $disposisi->instruksis->contains($instruksi->id) ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <label
                                                        class="form-check-label">{{ $instruksi->nama_instruksi }}</label>
                                                </div>
                                                @endforeach
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route('pimpinan.updateStatusSuratKeluar', $disposisi->suratKeluar->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="menunggu"
                                                        {{ $disposisi->suratKeluar->status == 'menunggu' ? 'selected' : '' }}>
                                                        Menunggu</option>
                                                    <option value="ditolak"
                                                        {{ $disposisi->suratKeluar->status == 'ditolak' ? 'selected' : '' }}>
                                                        Ditolak</option>
                                                    <option value="disetujui"
                                                        {{ $disposisi->suratKeluar->status == 'disetujui' ? 'selected' : '' }}>
                                                        Disetujui</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if($disposisi->suratKeluar->file_surat)
                                            <a href="{{ route('pimpinan.surat-keluar.download', $disposisi->suratKeluar->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>{{ $disposisi->suratKeluar->tujuan ?? 'Tidak Ada' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editSuratKeluarModal-{{ $disposisi->id }}">
                                                <i class="bi bi-pen"></i>
                                            </a>
                                            <form
                                                action="{{ route('pimpinan.hapusDisposisiSuratKeluar', $disposisi->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-url="{{ route('pimpinan.hapusDisposisiSuratKeluar', $disposisi->id) }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach

                                    @if (!$hasSurat)
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Tidak Ada Surat</td>
                                    </tr>
                                    @endif
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
            <form action="{{route('pimpinan.storeSuratKeluar')}}" method="POST" enctype="multipart/form-data">
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
                    <button type="submit" class="btn btn-primary">Simpan Surat Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($disposisis as $disposisi)
@if ($disposisi->suratKeluar)
<div class="modal fade" id="editSuratKeluarModal-{{ $disposisi->id }}" tabindex="-1"
    aria-labelledby="editSuratKeluarModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pimpinan.updateDisposisiSuratKeluar', $disposisi->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Disposisi & Surat Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nomor Surat -->
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control"
                            value="{{ $disposisi->suratKeluar->nomor_surat }}" required>
                    </div>

                    <!-- Pengirim -->
                    <div class="mb-3">
                        <label for="pengirim" class="form-label">Pengirim</label>
                        <input type="text" name="pengirim" class="form-control"
                            value="{{ $disposisi->suratKeluar->pengirim }}" required>
                    </div>

                    <!-- Perihal -->
                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" name="perihal" class="form-control"
                            value="{{ $disposisi->suratKeluar->perihal }}" required>
                    </div>

                    <!-- Jenis Surat -->
                    <div class="mb-3">
                        <label for="jenis_surat" class="form-label">Jenis Surat</label>
                        <input type="text" name="jenis_surat" class="form-control"
                            value="{{ $disposisi->suratKeluar->jenis_surat }}" required>
                    </div>

                    <!-- File Surat -->
                    <div class="mb-3">
                        <label for="file_surat" class="form-label">File Surat</label>
                        <input type="file" name="file_surat" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file.</small>
                    </div>

                    <!-- Instruksi -->
                    <div class="mb-3">
                        <label for="instruksi" class="form-label">Instruksi</label>
                        <input type="text" name="instruksi" class="form-control" value="{{ $disposisi->instruksi }}"
                            required>
                    </div>

                    <!-- Status Disposisi -->
                    <div class="mb-3">
                        <label for="status_disposisi" class="form-label">Status Disposisi</label>
                        <select name="status_disposisi" class="form-select">
                            <option value="pending" {{ $disposisi->status == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="selesai" {{ $disposisi->status == 'selesai' ? 'selected' : '' }}>Selesai
                            </option>
                        </select>
                    </div>

                    <!-- Status Surat -->
                    <div class="mb-3">
                        <label for="status_surat" class="form-label">Status Surat</label>
                        <select name="status_surat" class="form-select">
                            <option value="menunggu"
                                {{ $disposisi->suratKeluar->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="ditolak"
                                {{ $disposisi->suratKeluar->status == 'ditolak' ? 'selected' : '' }}>
                                Ditolak</option>
                            <option value="disetujui"
                                {{ $disposisi->suratKeluar->status == 'disetujui' ? 'selected' : '' }}>Disetujui
                            </option>
                        </select>
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
@endif
@endforeach


@endsection