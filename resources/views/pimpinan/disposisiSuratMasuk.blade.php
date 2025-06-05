@extends('layouts.pimpinan')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Surat Masuk</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pimpinan.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Surat Masuk</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">

                        <!-- Header Title & Tombol + -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Daftar Surat Masuk</h5>

                            <!-- Tombol Tambah Instruksi dengan Teks -->
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addInstruksiModal">
                                Tambah Instruksi Baru
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor</th>
                                        <th>Pengirim</th>
                                        <th>Perihal</th>
                                        <th>Jenis</th>
                                        <th>Instruksi</th>
                                        <th>Status Surat</th>
                                        <th>File</th>
                                        <th>Bidang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disposisis as $index => $disposisi)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $disposisi->suratMasuk->nomor_surat ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratMasuk->pengirim ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratMasuk->perihal ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratMasuk->jenis_surat ?? 'Tidak Ada' }}</td>
                                        <td>
                                            @if($allInstruksis->isEmpty())
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
                                                    <label class="form-check-label">
                                                        {{ $instruksi->nama_instruksi }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route('pimpinan.updateStatusSuratMasuk', $disposisi->suratMasuk->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="baru"
                                                        {{ $disposisi->suratMasuk->status == 'baru' ? 'selected' : '' }}>
                                                        Baru</option>
                                                    <option value="diterima"
                                                        {{ $disposisi->suratMasuk->status == 'diterima' ? 'selected' : '' }}>
                                                        Diterima</option>
                                                    <option value="ditindaklanjuti"
                                                        {{ $disposisi->suratMasuk->status == 'ditindaklanjuti' ? 'selected' : '' }}>
                                                        Ditindaklanjuti</option>
                                                    <option value="selesai"
                                                        {{ $disposisi->suratMasuk->status == 'selesai' ? 'selected' : '' }}>
                                                        Selesai</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if($disposisi->suratMasuk->file_surat)
                                            <a href="{{ route('pimpinan.surat-masuk.download', $disposisi->suratMasuk->id) }}"
                                                class="btn btn-success btn-sm" title="Unduh Surat">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('pimpinan.updateBidang', $disposisi->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')

                                                @foreach($bidangs as $bidang)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="bidang_ids[]"
                                                        value="{{ $bidang->id }}"
                                                        {{ $disposisi->bidangs->contains($bidang->id) ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <label class="form-check-label">
                                                        {{ $bidang->nama_bidang }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </form>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- End Table -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->

<!-- Modal Tambah Instruksi -->
<div class="modal fade" id="addInstruksiModal" tabindex="-1" aria-labelledby="addInstruksiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('pimpinan.storeInstruksi') }}" method="POST" id="formAddInstruksi">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInstruksiModalLabel">Tambah Instruksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_instruksi" class="form-label">Nama Instruksi</label>
                        <input type="text" name="nama_instruksi" id="nama_instruksi" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection