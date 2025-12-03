<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Management Produksi')</title>
    <link rel="icon" href="{{ asset('icon.ico') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #000000ff 0%, #004483ff 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar .brand {
            padding: 15px 20px;
            color: white;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 700;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(180deg, #001c36ff 0%, #005bb0ff 100%);
            color: white;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }

        .badge-ok {
            background-color: #27ae60;
        }

        .badge-nok {
            background-color: #e74c3c;
        }

        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .main-content {
                margin-left: 0;
            }
        }

        body {
            background-color: #000000ff;
            /* Dark mode modern */
            color: #000000ff;
            /* agar teks tetap terlihat */
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <i class="bi bi-box-seam"></i> Management Produksi Linknet
        </div>

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard Stok
            </a>
            <a class="nav-link {{ request()->is('igi*') ? 'active' : '' }}" href="{{ route('igi.index') }}">
                <i class="bi bi-inbox"></i> I.G.I
            </a>
            <a class="nav-link {{ request()->is('koreksi-barcode*') ? 'active' : '' }}" href="{{ route('koreksi-barcode.index') }}">
                <i class="bi bi-pencil-square"></i> Koreksi Barcode
            </a>
            <a class="nav-link {{ request()->is('uji-fungsi*') ? 'active' : '' }}" href="{{ route('uji-fungsi.index') }}">
                <i class="bi bi-check-circle"></i> Uji Fungsi
            </a>
            <a class="nav-link {{ request()->is('repair*') ? 'active' : '' }}" href="{{ route('repair.index') }}">
                <i class="bi bi-tools"></i> Repair
            </a>
            <a class="nav-link {{ request()->is('rekondisi*') ? 'active' : '' }}" href="{{ route('rekondisi.index') }}">
                <i class="bi bi-arrow-clockwise"></i> Rekondisi
            </a>
            <a class="nav-link {{ request()->is('service-handling*') ? 'active' : '' }}" href="{{ route('service-handling.index') }}">
                <i class="bi bi-wrench"></i> Service Handling
            </a>
            <a class="nav-link {{ request()->is('packing*') ? 'active' : '' }}" href="{{ route('packing.index') }}">
                <i class="bi bi-box"></i> Packing
            </a>
            <a class="nav-link {{ request()->is('download*') ? 'active' : '' }}" href="{{ route('download.index') }}">
                <i class="bi bi-download"></i> Download Data
            </a>

            <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; width: 100%; text-align: left;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
                <div class="d-flex align-items-center">
                    <span class="me-3">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </span>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>