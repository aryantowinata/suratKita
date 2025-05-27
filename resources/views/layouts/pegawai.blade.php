<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard Pegawai</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}" type="image/png">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/bootstrap/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/bootstrap-icons/bootstrap-icons.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/boxicons/css/boxicons.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/quill/quill.snow.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/quill/quill.bubble.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/remixicon/remixicon.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/simple-datatables/style.css')}}" />

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css')}}" />

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="index.html" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">Hello {{$adminData->nama}}</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->



        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="{{ $adminData->foto_profile ? asset('storage/' . $adminData->foto_profile) : 'default.jpg' }}"
                            alt="Profile" class="rounded-circle" style="width: 35px; ">
                        <span class=" d-none d-md-block dropdown-toggle ps-2">Pegawai</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>Pegawai</h6>

                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{route('pegawai.profile')}}">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <li>
                            <form id="logout-form" action="{{route('pegawai.logout')}}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>

                            <a class="dropdown-item d-flex align-items-center" href=""
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>

                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}"
                    href="{{route('pegawai.dashboard')}}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->



            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed{{ request()->routeIs('pegawai.disposisiSuratMasuk') ? 'active' : '' }}"
                    href="{{route('pegawai.disposisiSuratMasuk')}}">
                    <i class="bi bi-envelope"></i>
                    <span> Surat Masuk</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed{{ request()->routeIs('pegawai.disposisiSuratKeluar') ? 'active' : '' }}"
                    href="{{route('pegawai.disposisiSuratKeluar')}}">
                    <i class="bi bi-envelope"></i>
                    <span> Surat Keluar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed{{ request()->routeIs('pegawai.laporan') ? 'active' : '' }}"
                    href="{{route('pegawai.laporan')}}">
                    <i class="bi bi-bookmark-fill"></i>
                    <span>Laporan</span>
                </a>
            </li>


        </ul>

    </aside><!-- End Sidebar-->

    <!-- content awal -->
    @yield('container')

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Project</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="#">Project</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/admin/vendor/apexcharts/apexcharts.min.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/chart.js/chart.umd.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/echarts/echarts.min.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/quill/quill.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/tinymce/tinymce.min.js')}}"></script>
    <script src="{{ asset('assets/admin/vendor/php-email-form/validate.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Template Main JS File -->
    <script src="{{ asset('assets/admin/js/main.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert2 Toast untuk konfirmasi penghapusan
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const deleteUrl = this.getAttribute('data-url');

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Yakin ingin menghapus?',
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    timerProgressBar: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(csrfInput);
                        form.appendChild(methodInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // SweetAlert2 Toast untuk pesan sukses
        @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        @endif
    });
    </script>



</body>

</html>