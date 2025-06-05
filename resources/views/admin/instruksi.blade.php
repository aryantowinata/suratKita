@extends('layouts.admin')
@section('container')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Arsip Surat</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Instruksi Surat</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Daftar Instruksi Surat</h5>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addInstruksiSuratModal">Tambah Instruksi Surat</a>
                        </div>


                        <div class="table-responsive">
                            <table class="table datatable ">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Instruksi </th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instruksi as $instruksis)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $instruksis->nama_instruksi }}</td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm " data-bs-toggle="modal"
                                                data-bs-target="#editInstruksi-{{ $instruksis->id }}"><i
                                                    class="bi bi-pen"></i></a>
                                            <form action="{{ route('admin.hapusInstruksi', $instruksis->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-url="{{ route('admin.hapusInstruksi', $instruksis->id) }}">
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
@foreach($instruksi as $instruksis)
<div class="modal fade" id="editInstruksi-{{ $instruksis->id }}" tabindex="-1"
    aria-labelledby="editInstruksiLabel-{{ $instruksis->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.updateInstruksi', $instruksis->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Instruksi Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_instruksi" class="form-label">Instruksi</label>
                        <input type="text" name="nama_instruksi" class="form-control"
                            value="{{ $instruksis->nama_instruksi }}" required>
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

<!-- Modal Tambah Surat -->
<div class="modal fade" id="addInstruksiSuratModal" tabindex="-1" aria-labelledby="addInstruksiSuratModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.storeInstruksi')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addInstruksiSuratModalLabel">Tambah Instruksi Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="nama_instruksi" class="form-label">Instruksi</label>
                        <input type="text" name="nama_instruksi" class="form-control" id="nama_instruksi" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Instruksi Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach
@endsection