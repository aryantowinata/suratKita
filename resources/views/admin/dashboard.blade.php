@extends('layouts.admin')
@section('container')

<main id="main" class="main">
    <div class="pagetitle mb-4">
        <h1 class="fw-bold text-primary">Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row g-4">

            <!-- Card -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow border-0 dashboard-card h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="card-icon bg-primary text-white mb-3">
                            <i class="bi bi-envelope fs-2"></i>
                        </div>
                        <h5 class="card-title text-muted">Surat Masuk</h5>
                        <h3 class="fw-bold">{{$totalSuratMasuk}}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card shadow border-0 dashboard-card h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="card-icon bg-success text-white mb-3">
                            <i class="bi bi-envelope-fill fs-2"></i>
                        </div>
                        <h5 class="card-title text-muted">Surat Keluar</h5>
                        <h3 class="fw-bold">{{$totalSuratKeluar}}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card shadow border-0 dashboard-card h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="card-icon bg-info text-white mb-3">
                            <i class="bi bi-people-fill fs-2"></i>
                        </div>
                        <h5 class="card-title text-muted">Total Users</h5>
                        <h3 class="fw-bold">{{$totalUsers}}</h3>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<script>
history.pushState(null, null, location.href);
window.onpopstate = function() {
    history.go(1);
};
</script>

@endsection