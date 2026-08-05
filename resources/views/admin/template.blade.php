<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Halaman admin">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title }}</title>
    <!-- Icon -->
    <link rel="shortcut icon" href="{{ asset('img/icon.png') }}">
    <!-- Custom CSS -->
    <link href="{{ asset('admin-assets/css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/select2.min.css') }}" rel="stylesheet">

    <!-- Fontawesome -->
    <link href="{{ asset('admin-assets/plugins/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Date Range Picker -->
    <link href="{{ asset('admin-assets/css/daterangepicker.css') }}" rel="stylesheet">

    {{-- CKEditor --}}
    <script src="{{ asset('admin-assets/plugins/ckeditor/ckeditor.js') }}"></script>
</head>

<style type="text/css">
    .table-responsive::-webkit-scrollbar {
        display: none;
    }
</style>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="{{ route('admin') }}">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!-- Dark Logo icon -->
                            <img src="{{ asset('img/' . $website->logo) }}" height="50px" alt="homepage" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                            <h2 class="text-dark mt-4 bold" title="GIS TPS">
                                G I S</h2>
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none mt-2"
                        href="javascript:void(0)"><i class="fas fa-bars fs-6"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">

                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav ms-auto d-flex align-items-center">

                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li>
                            <a class="profile-pic" type="button">
                                <img class="img-circle" src="{{ asset('img/user.png') }}" alt="user" width="40px"
                                    height="40px">
                                <span class="text-white font-medium">{{ $user->name }}</span></a>
                        </li>

                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>

        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li class="sidebar-item pt-2">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin') }}"
                                aria-expanded="false">
                                <i class="fas fa-home me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('admin.tps.index') }}" aria-expanded="false">
                                <i class="fas fa-map-marker-alt me-3" aria-hidden="true"></i>
                                <span class="hide-menu">TPS</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('admin.angkutan.index') }}" aria-expanded="false">
                                <i class="fas fa-truck-moving me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Angkutan</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('admin.sopir.index') }}" aria-expanded="false">
                                <i class="fas fa-user-friends me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Sopir</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('admin.jadwal.index') }}" aria-expanded="false">
                                <i class="fas fa-calendar-alt me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Jadwal</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('admin.website.index') }}" aria-expanded="false">
                                <i class="fas fa-globe me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Website</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('profile.edit') }}" aria-expanded="false">
                                <i class="fas fa-user-cog me-3" aria-hidden="true"></i>
                                <span class="hide-menu">Akun</span>
                            </a>
                        </li>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}"
                            style="display: none;">
                            @csrf
                        </form>
                        <li class="sidebar-item bg-danger">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link text-white" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-lock-open me-3 text-white"></i>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>

                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="d-lg-none"><br></div>

            <div class="page-breadcrumb bg-white">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title text-primary">{{ $title }}</h4>
                    </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <div class="d-md-flex">
                            <ol class="breadcrumb ms-auto">
                                <li><a href="#" class="fw-normal">{{ $active }}</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">


                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <!-- Konten -->
                @yield('content')



            </div>


            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">{{ $website->nama }}</footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('admin-assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('admin-assets/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/fontawesome/js/all.min.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('admin-assets/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('admin-assets/js/custom.js') }}"></script>
    <script src="{{ asset('admin-assets/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/daterangepicker.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables').DataTable();
            $('#dataTabless').DataTable();

            $('#select1').select2();
            $('#select2').select2();
        });
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
