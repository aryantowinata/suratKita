@extends('layouts.admin')

@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Laporan Surat</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Surat</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filter Laporan Surat</h5>

                        <!-- Form Filter & Cetak PDF -->
                        <form method="GET" action="{{ route('admin.laporan') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date">Tanggal Mulai:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">Tanggal Akhir:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2 btn-sm">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.laporan.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                        target="_blank" class="btn btn-danger btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                                    </a>

                                </div>
                            </div>
                        </form>

                        <hr>

                        <h5 class="card-title">Daftar Laporan Surat</h5>

                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Surat</th>
                                        <th>Nomor Surat</th>
                                        <th>Pengirim/Penerima</th>
                                        <th>Perihal</th>
                                        <th>Tanggal Surat</th>
                                        <th>File</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suratMasuk as $index => $surat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>Surat Masuk</td>
                                        <td>{{ $surat->nomor_surat }}</td>
                                        <td>{{ $surat->pengirim }}</td>
                                        <td>{{ $surat->perihal }}</td>
                                        <td>{{ $surat->tanggal_surat }}</td>
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
                                        <td>{{ $surat->status }}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($suratKeluar as $index => $surat)
                                    <tr>
                                        <td>{{ $index + count($suratMasuk) + 1 }}</td>
                                        <td>Surat Keluar</td>
                                        <td>{{ $surat->nomor_surat }}</td>
                                        <td>{{ $surat->pengirim }}</td>
                                        <td>{{ $surat->perihal }}</td>
                                        <td>{{ $surat->tanggal_surat }}</td>
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
                                        <td>{{ $surat->status }}</td>
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
@endsection