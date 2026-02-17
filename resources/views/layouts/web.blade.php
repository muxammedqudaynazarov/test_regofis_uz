<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME', 'RegOFIS') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('dist/img/logo.ico') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <style>
        body {
            font-family: 'Google Sans', sans-serif;
        }

        .nav-sidebar .nav-link p {
            font-size: 10pt;
        }

        .nav-icon {
            font-size: 10pt !important;
        }
    </style>

    @yield('style')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    {{ json_decode(Auth::user()->name)->short_name ?? Auth::user()->name ?? 'Foydalanuvchi' }}
                    <i class="fas fa-angle-down ml-1 text-xs"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    @php
                        $userRoleNames = json_decode(Auth::user()->hemis_roles, true) ?? [];
                        $roles = \Spatie\Permission\Models\Role::whereIn('name', $userRoleNames)->get();
                    @endphp
                    @if($roles->count() > 1)
                        <span class="dropdown-header small font-weight-bold">Foydalanuvchi roli</span>
                        <div class="dropdown-divider"></div>
                        @foreach($roles as $role)
                            <a href="{{ route('switch.role', $role->name) }}"
                               class="dropdown-item small {{ (Auth::user()->current_role ?? '') == $role->name ? 'active' : '' }}">
                                {{ $role->desc ?? $role->name }}
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                    @endif

                    <a href="{{ url('/logout') }}" class="dropdown-item dropdown-footer text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Tizimdan chiqish
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('home') }}" class="brand-link border-bottom-0">
            <img src="{{ asset('dist/img/logo.ico') }}" alt="Logo" class="brand-image img-circle elevation-3"
                 style="opacity: .8">
            <span class="brand-text font-weight-light">RegOFIS.uz</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Asosiy sahifa</p>
                        </a>
                    </li>

                    @can('department.faculties.view')
                        <li class="nav-item">
                            <a href="{{ route('departments.show', 'faculties') }}"
                               class="nav-link {{ Request::is('home/departments/faculties*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university"></i>
                                <p>
                                    Fakultetlar ro‘yxati
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('department.view')
                        <li class="nav-item">
                            <a href="{{ route('departments.show', 'show') }}"
                               class="nav-link {{ Request::is('home/departments/show*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    Kafedralar ro‘yxati
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('curriculum.view')
                        <li class="nav-item">
                            <a href="{{ route('curriculum.index') }}"
                               class="nav-link {{ Request::is('home/curriculum*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-graduation-cap"></i>
                                <p>
                                    O‘quv rejalar
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('languages.view')
                        <li class="nav-item">
                            <a href="{{ route('languages.index') }}"
                               class="nav-link {{ Request::is('home/languages*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-language"></i>
                                <p>
                                    Tizim tillari
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('applications.view')
                        <li class="nav-item">
                            <a href="#"
                               class="nav-link {{ Request::is('home/applications*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>
                                    Arizalar ro‘yxati
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('lessons.view')
                        <li class="nav-item">
                            <a href="{{ route('subjects-register.index') }}"
                               class="nav-link {{ Request::is('home/subjects-register*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Fanlar ro‘yxati
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('subjects.view')
                        @php
                            $subjects = \App\Models\SubjectTeacher::where('user_id', auth('web')->id())->count() ?? 0;
                        @endphp
                        <li class="nav-item">
                            <a href="{{ route('lessons.index') }}"
                               class="nav-link {{ Request::is('home/lessons*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>
                                    Biriktirilgan fanlar
                                    <span class="right badge badge-danger">{{ $subjects }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('exam.view')
                        <li class="nav-item">
                            <a href="#"
                               class="nav-link">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Yakuniy nazoratlar
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('statistics.view')
                        <li class="nav-item">
                            <a href="#"
                               class="nav-link">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    Statistika
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('system.view')
                        <li class="nav-item">
                            <a href="{{ route('options.index') }}"
                               class="nav-link {{ Request::is('home/options*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Tizim sozlamalari
                                </p>
                            </a>
                        </li>
                    @endcan
                </ul>
            </nav>
        </div>
    </aside>

    @yield('content')

    <footer class="main-footer text-sm">
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">RegOFIS.uz</a>.</strong>
        <div class="float-right d-none d-sm-inline-block">
            <b>Versiya</b> 1.1.0
        </div>
    </footer>
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(function () {
        var Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });

        // Muvaffaqiyatli xabar (Success)
        @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}'
        });
        @endif

        // Xatolik xabari (Error)
        @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}'
        });
        @endif

        // Validatsiya xatolari (Validation Errors)
        @if($errors->any())
        @foreach($errors->all() as $error)
        Toast.fire({
            icon: 'warning',
            title: '{{ $error }}'
        });
        @endforeach
        @endif
    });
</script>
@yield('scripts')
</body>
</html>
