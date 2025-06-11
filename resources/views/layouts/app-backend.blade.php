<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>{{ config('app.name') }} - {{ $title }}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Font family -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Layout JS -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Custom Styles -->
    @stack('styles')

</head>

<body>
    <div class="main-wrapper">
        <div class="header header-one">
            <a href=""
                class="d-inline-flex d-sm-inline-flex align-items-center d-md-inline-flex d-lg-none align-items-center device-logo">
                <img src="{{ asset('assets/img/logopt.png') }}" class="img-fluid logo2" alt="Logo">
            </a>
            <div class="main-logo d-inline float-start d-lg-flex align-items-center d-none d-sm-none d-md-none">
                <div class="logo-white">
                    <a href="">
                        <img src="{{ asset('assets/img/logo-full-white.png') }}" class="img-fluid logo-blue" alt="Logo">
                    </a>
                    <a href="">
                        <img src="{{ asset('assets/img/logo-small-white.png') }}" class="img-fluid logo-small"
                            alt="Logo">
                    </a>
                </div>
                <div class="logo-color">
                    <a href="">
                        <img src="{{ asset('assets/img/logopt.png') }}" class="img-fluid logo-blue" alt="Logo">
                    </a>
                    <a href="">
                        <img src="{{ asset('assets/img/logo-small.png') }}" class="img-fluid logo-small" alt="Logo">
                    </a>
                </div>
            </div>

            <a href="javascript:void(0);" id="toggle_btn">
                <span class="toggle-bars">
                    <span class="bar-icons"></span>
                    <span class="bar-icons"></span>
                    <span class="bar-icons"></span>
                    <span class="bar-icons"></span>
                </span>
            </a>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav nav-tabs user-menu">
                {{-- <li class="nav-item dropdown  flag-nav dropdown-heads">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button">
                        <i class="fe fe-bell"></i> <span class="badge rounded-pill"></span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <div class="notification-title">Notifications <a href="notifications.html">View all</a>
                            </div>
                            <a href="javascript:void(0)" class="clear-noti d-flex align-items-center">Mark all as read
                                <i class="fe fe-check-circle"></i></a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <a href="">
                                        <div class="d-flex">
                                            <span class="avatar avatar-md">
                                                <img class="avatar-img rounded-circle" alt="avatar-img"
                                                    src="{{ Auth::user()->photo_url }}">
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details"><span class="noti-title">John Hammond</span>
                                                    created <span class="noti-title">Isla Nublar SOC2 compliance
                                                        report</span></p>
                                                <p class="noti-time"><span class="notification-time">Last Wednesday at
                                                        11:15 AM</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="#">Clear All</a>
                        </div>
                    </div>
                </li> --}}
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)" class="user-link  nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            <img src="{{ Auth::user()->photo_url }}" alt="img" class="profilesidebar">
                            <span class="animate-circle"></span>
                        </span>
                        <span class="user-content">
                            <span class="user-details">{{ Auth::user()->name }}</span>
                            <span class="user-name">{{ Auth::user()->role }}</span>
                        </span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilemenu">
                            <div class="subscription-menu">
                                <ul>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                                    </li>
                                    {{-- <li>
                                        <a class="dropdown-item" href="">Settings</a>
                                    </li> --}}
                                </ul>
                            </div>
                            <div class="subscription-logout">
                                <ul>
                                    <li class="pb-0">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Log Out</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul class="sidebar-vertical">
                        <div class="profile-wrapper text-center py-4">
                                <div class="profile-box mx-auto">
                                    <img src="{{ Auth::user()->photo_url }}" alt="User Photo" class="profile-img">
                                </div>
                            </div>
                        <li>
                            <a class="{{ request()->routeIs(['dashboard.*']) ? 'active' : '' }}"
                                href="{{ route('dashboard.index') }}">
                                <i class="fe fe-home"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        @if (Auth::user()->role === 'superadmin')
                        <li class="submenu">
                            <a href="#">
                                <i class="fe fe-database"></i> <span> Data Master</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="display: none;">
                                <li>
                                    <a class="{{ request()->routeIs('pangkat.*') ? 'active' : '' }}"
                                        href="{{ route('pangkat.index') }}">Pangkat</a>
                                </li>
                                <li>
                                    <a class="{{ request()->routeIs('jabatan.*') ? 'active' : '' }}"
                                        href="{{ route('jabatan.index') }}">Jabatan</a>
                                </li>
                                <li>
                                    <a class="{{ request()->routeIs('keterangan.*') ? 'active' : '' }}"
                                        href="{{ route('keterangan.index') }}">Keterangan</a>
                                </li>
                                <li>
                                    <a class="{{ request()->routeIs('subdis.*') ? 'active' : '' }}"
                                        href="{{ route('subdis.index') }}">Subdis</a>
                                </li>
                                <li>
                                    <a class="{{ request()->routeIs('jam-apel.*') ? 'active' : '' }}"
                                        href="{{ route('jam-apel.index') }}">Jam Apel</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('user.*') ? 'active' : '' }}"
                                href="{{ route('user.index') }}">
                                <i class="fe fe-users"></i> <span>Data Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['riwayat.piket.index']) ? 'active' : '' }}"
                                href="{{ route('riwayat.piket.index') }}">
                                <i class="fe fe-clock"></i>
                                <span>Riwayat Sesi</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.index', 'rekap-apel.anggota']) ? 'active' : '' }}"
                                href="{{ route('rekap-apel.index', ['type' => 'pagi', 'date' => now()->format('Y-m-d')]) }}">
                                <i class="fe fe-file"></i>
                                <span>Detail Rekap</span>
                            </a>
                        </li>
                        <li>
                            <a href=""><i class="fe fe-clipboard"></i> <span>Detail Apel Anggota</span></a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['grafik.kehadiran.index']) ? 'active' : '' }}"
                                href="{{ route('grafik.kehadiran.index') }}">
                                <i class="fe fe-bar-chart-2"></i>
                                <span>Grafik Kehadiran</span>
                            </a>
                        </li>
                        <li>
                            <a href=""><i class="fe fe-file-text"></i> <span>Laporan Keterangan</span></a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.subdis']) ? 'active' : '' }}" href="#">
                                <i class="fe fe-file-text"></i>
                                <span>Laporan Subdis</span>
                            </a>
                        </li>
                        @elseif (Auth::user()->role === 'pokmin')
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.index']) ? 'active' : '' }}"
                                href="{{ route('rekap-apel.index', ['type' => 'pagi', 'date' => now()->format('Y-m-d')]) }}">
                                <i class="fe fe-file"></i>
                                <span>Detail Rekap</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.anggota']) ? 'active' : '' }}" href="#">
                                <i class="fe fe-clipboard"></i>
                                <span>Detail Apel Anggota</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.subdis']) ? 'active' : '' }}" href="#">
                                <i class="fe fe-file-text"></i>
                                <span>Laporan Subdis</span>
                            </a>
                        </li>
                        @elseif (Auth::user()->role === 'piket')
                        <li>
                            <a class="{{ request()->routeIs(['riwayat.piket.index']) ? 'active' : '' }}"
                                href="{{ route('riwayat.piket.index') }}">
                                <i class="fe fe-clock"></i>
                                <span>Riwayat Sesi</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.index', 'rekap-apel.anggota']) ? 'active' : '' }}"
                                href="{{ route('rekap-apel.index', ['type' => 'pagi', 'date' => now()->format('Y-m-d')]) }}">
                                <i class="fe fe-file"></i>
                                <span>Detail Rekap</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['rekap-apel.subdis']) ? 'active' : '' }}" href="#">
                                <i class="fe fe-file-text"></i>
                                <span>Laporan Subdis</span>
                            </a>
                        </li>
                        @elseif (Auth::user()->role === 'pimpinan')
                        <li>
                            <a class="{{ request()->routeIs(['grafik.kehadiran.index']) ? 'active' : '' }}"
                                href="{{ route('grafik.kehadiran.index') }}">
                                <i class="fe fe-bar-chart-2"></i>
                                <span>Grafik Kehadiran</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs(['laporan.personel.*']) ? 'active' : '' }}"
                                href="{{ route('laporan.personel.keterangan') }}">
                                <i class="fe fe-file-text"></i>
                                <span>Laporan Kehadiran</span>
                            </a>
                        </li>
                        @elseif (Auth::user()->role === 'personil')

                        @endif
                    </ul>
                </div>
            </div>
        </div>

        @yield('content')

    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Datatable JS -->
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <!-- select CSS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- Datepicker Core JS -->
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/locale/id.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <!-- multiselect JS -->
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <!-- Feather Icon JS -->
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('#alert').fadeOut();
            }, 3000);

            $("#page-wrapper").css("min-height", "");
        });
    </script>

    @stack('scripts')

</body>

</html>