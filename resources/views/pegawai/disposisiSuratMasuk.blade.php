@extends('layouts.pegawai')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Surat Masuk</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pegawai.dashboard') }}">Dashboard</a></li>
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
                            <h5 class="card-title mb-0">Daftar Surat Masuk</h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor</th>
                                        <th>Pengirim</th>
                                        <th>Perihal</th>
                                        <th>Instruksi</th>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Bidang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($disposisis as $index => $disposisi)
                                    @if ($disposisi->suratMasuk)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $disposisi->suratMasuk->nomor_surat }}</td>
                                        <td>{{ $disposisi->suratMasuk->pengirim }}</td>
                                        <td>{{ $disposisi->suratMasuk->perihal }}</td>
                                        <td>
                                            @if ($allInstruksis->isEmpty())
                                            <span class="text-muted">Tidak ada instruksi</span>
                                            @else
                                            <form class="instruksi-form">
                                                @foreach ($allInstruksis as $instruksi)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="instruksi_ids[]" value="{{ $instruksi->id }}"
                                                        {{ $disposisi->instruksis->contains($instruksi->id) ? 'checked' : '' }}
                                                        disabled>
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
                                                action="{{ route('pegawai.updateStatusSuratMasuk', $disposisi->suratMasuk->id) }}"
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
                                                    <option value="diarsipkan"
                                                        {{ $disposisi->suratMasuk->status == 'diarsipkan' ? 'selected' : '' }}>
                                                        Diarsipkan</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if($disposisi->suratMasuk->file_surat)
                                            <a href="{{ route('pegawai.surat-masuk.download', $disposisi->suratMasuk->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $userBidangId = auth()->user()->id_bidang;
                                            $matchingBidang = $disposisi->bidangs->firstWhere('id', $userBidangId);
                                            @endphp

                                            {{ $matchingBidang ? $matchingBidang->nama_bidang : '-' }}
                                        </td>

                                    </tr>
                                    @endif
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-danger">Belum ada surat masuk</td>
                                    </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div><!-- End Table -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->
@endsection