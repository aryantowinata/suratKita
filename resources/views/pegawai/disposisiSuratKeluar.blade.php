@extends('layouts.pegawai')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1> Surat Keluar</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('pegawai.dashboard')}}">Dashboard</a></li>
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
                                        <th>Instruksi</th>
                                        <th>Status </th>
                                        <th>File </th>
                                        <th>Bidang</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disposisis as $index => $disposisi)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $disposisi->suratKeluar->nomor_surat ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratKeluar->pengirim ?? 'Tidak Ada' }}</td>
                                        <td>{{ $disposisi->suratKeluar->perihal ?? 'Tidak Ada' }}</td>
                                        <td>
                                            @if ($allInstruksis->isEmpty())
                                            <span class="text-muted">Tidak ada instruksi</span>
                                            @else
                                            <form action="{{ route('pegawai.updateInstruksi', $disposisi->id) }}"
                                                method="POST" class="instruksi-form">
                                                @csrf
                                                @method('PUT')
                                                @foreach ($allInstruksis as $instruksi)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="instruksi_ids[]" value="{{ $instruksi->id }}"
                                                        {{ $disposisi->instruksis->contains($instruksi->id) ? 'checked' : '' }}
                                                        onchange="this.form.submit()" disabled>
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
                                                action="{{ route('pegawai.updateStatusSuratKeluar', $disposisi->suratKeluar->id) }}"
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
                                                        DIsetujui</option>

                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if($disposisi->suratKeluar->file_surat)
                                            <a href="{{ route('pegawai.surat-keluar.download', $disposisi->suratKeluar->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                            @else
                                            <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $disposisi->bidang->nama_bidang }}
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




@endsection