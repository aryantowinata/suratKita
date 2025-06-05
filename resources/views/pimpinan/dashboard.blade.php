@extends('layouts.pimpinan')
@section('container')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('pimpinan.dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- Sales Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Surat Masuk</h5>

                                <div class="d-flex align-items-center">
                                    <div
                                        class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{$totalSuratMasuk}}</h6>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Sales Card -->



                </div>
            </div><!-- End Left side columns -->
        </div>
    </section>
</main><!-- End #main -->

<script>
history.pushState(null, null, location.href);
window.onpopstate = function() {
    history.go(1);
};
</script>

@endsection