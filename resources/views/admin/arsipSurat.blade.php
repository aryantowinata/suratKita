@extends('layouts.admin')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Arsip Surat</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Arsip Surat</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Arsip Surat</h5>

                        <form method="GET" action="{{ route('admin.arsipSurat') }}"
                            class="mb-3 d-flex align-items-center gap-2">
                            <label for="jenis_surat" class="form-label mb-0 fw-semibold">Filter Jenis Surat:</label>
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-filter"></i>
                                </span>
                                <select name="jenis_surat" id="jenis_surat" class="form-select"
                                    onchange="this.form.submit()">
                                    <option value="">-- Semua --</option>
                                    <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>
                                        Surat Masuk</option>
                                    <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>
                                        Surat Keluar</option>
                                </select>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table datatable ">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis </th>
                                        <th>No Surat</th>
                                        <th>No Agenda</th>
                                        <th>Pengirim</th>
                                        <th>Perihal</th>
                                        <th>Tanggal </th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($arsipSurats as $arsip)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($arsip->jenis_surat) }}</td>
                                        <td>{{ $arsip->nomor_surat }}</td>
                                        <td>{{ $arsip->no_agenda }}</td>
                                        <td>{{ $arsip->pengirim ?? $arsip->penerima ?? '-' }}</td>
                                        <td>{{ $arsip->perihal }}</td>
                                        <td>{{ \Carbon\Carbon::parse($arsip->tanggal_surat)->format('d M Y') }}</td>
                                        <td>
                                            @if($arsip->file_surat)
                                            <a href="{{ route('admin.downloadArsipSurat', $arsip->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.updateStatusArsip', $arsip->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="baru"
                                                        {{ $arsip->status == 'baru' ? 'selected' : '' }}>Baru</option>
                                                    <option value="diterima"
                                                        {{ $arsip->status == 'diterima' ? 'selected' : '' }}>Diterima
                                                    </option>
                                                    <option value="selesai"
                                                        {{ $arsip->status == 'selesai' ? 'selected' : '' }}>Selesai
                                                    </option>
                                                    <option value="ditindaklanjuti"
                                                        {{ $arsip->status == 'ditindaklanjuti' ? 'selected' : '' }}>
                                                        Ditindaklanjuti</option>
                                                    <option value="diarsipkan"
                                                        {{ $arsip->status == 'diarsipkan' ? 'selected' : '' }}>
                                                        Diarsipkan</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm " data-bs-toggle="modal"
                                                data-bs-target="#editArsipSuratModal-{{ $arsip->id }}"><i
                                                    class="bi bi-pen"></i></a>
                                            <form action="{{ route('admin.hapusArsipSurat', $arsip->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-url="{{ route('admin.hapusArsipSurat', $arsip->id) }}">
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



<!-- Modal Edit Arsip Surat -->
@foreach($arsipSurats as $arsip)
<div class="modal fade" id="editArsipSuratModal-{{ $arsip->id }}" tabindex="-1"
    aria-labelledby="editArsipSuratModalLabel-{{ $arsip->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.updateArsipSurat', $arsip->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Arsip Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control" value="{{ $arsip->nomor_surat }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="no_agenda" class="form-label">Nomor Agenda</label>
                        <input type="text" name="no_agenda" class="form-control" value="{{ $arsip->no_agenda }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="pengirim" class="form-label">Pengirim/Penerima</label>
                        <input type="text" name="pengirim" class="form-control" value="{{ $arsip->pengirim }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" name="perihal" class="form-control" value="{{ $arsip->perihal }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" value="{{ $arsip->tanggal_surat }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="baru" {{ $arsip->status == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="selesai" {{ $arsip->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="disposisi" {{ $arsip->status == 'disposisi' ? 'selected' : '' }}>Disposisi
                            </option>
                            <option value="diarsipkan" {{ $arsip->status == 'diarsipkan' ? 'selected' : '' }}>Diarsipkan
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="file_surat" class="form-label">File Surat</label>
                        <input type="file" name="file_surat" class="form-control">
                        @if($arsip->file_surat)
                        <small class="form-text">
                            <a href="{{ route('admin.downloadArsipSurat', $arsip->id) }}" target="_blank">
                                <i class="bi bi-file-earmark-arrow-down"></i> Unduh File
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