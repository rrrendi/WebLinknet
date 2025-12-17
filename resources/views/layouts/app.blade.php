<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Linknet') - Management Produksi</title>
    <link rel="icon" href="{{ asset('icon.ico') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #004483ff;
            --secondary-color: #001c36ff;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;

            /* Light Theme Colors */
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --sidebar-bg: linear-gradient(180deg, #001f3aff 0%, #3b85c7ff 100%);
            --navbar-bg: #ffffff;
            --table-hover: rgba(0, 68, 131, 0.05);
            --page-title-color: #000000ff;
        }

        /* Dark Theme Colors */
        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
            --text-color: #e0e0e0;
            --text-muted: #a0a0a0;
            --border-color: #3d3d3d;
            --sidebar-bg: linear-gradient(180deg, #0a0a0a 0%, var(--primary-color) 100%);
            --navbar-bg: #2d2d2d;
            --table-hover: rgba(255, 255, 255, 0.05);
            --page-title-color: #ffffff;
        }

        .page-title {
            color: var(--page-title-color);
            font-weight: 600;
        }


        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* SIDEBAR */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .brand {
            padding: 15px 20px;
            color: white;
            font-size: 18px;
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
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: var(--info-color);
            color: white;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            font-size: 18px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar-custom {
            background-color: var(--navbar-bg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
        }

        [data-theme="dark"] .navbar-custom {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* CARD */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            background-color: var(--card-bg);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        [data-theme="dark"] .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        /* BADGES */
        .badge-ok {
            background-color: var(--success-color);
        }

        .badge-nok {
            background-color: var(--danger-color);
        }

        /* STAT CARD */
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* TABLE */
        .table {
            color: var(--text-color);
        }

        .table thead th {
            color: var(--text-color);
            border-color: var(--border-color);
        }

        .table tbody td {
            color: var(--text-color);
            border-color: var(--border-color);
        }

        .table-hover tbody tr:hover {
            background-color: var(--table-hover);
        }

        [data-theme="dark"] .table {
            --bs-table-bg: var(--card-bg);
            --bs-table-color: var(--text-color);
            --bs-table-striped-bg: rgba(255, 255, 255, 0.02);
            --bs-table-active-bg: rgba(255, 255, 255, 0.05);
            --bs-table-border-color: var(--border-color);
        }

        [data-theme="dark"] .table> :not(caption)>*>* {
            color: var(--text-color);
            background-color: var(--card-bg);
            border-bottom-color: var(--border-color);
        }

        .table-dark {
            --bs-table-bg: #dfdfdfff;
            --bs-table-color: #ffffffff;
        }

        /* BUTTONS */
        .btn {
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        /* FORM */
        .form-control,
        .form-select {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--border-color);
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--info-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        [data-theme="dark"] .form-control::placeholder {
            color: var(--text-muted);
        }

        [data-theme="dark"] .form-select option {
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        /* ALERT */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* TEXT COLORS */
        .text-muted {
            color: var(--text-muted) !important;
        }

        /* THEME TOGGLE BUTTON */
        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            background-color: var(--border-color);
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
            padding: 0;
        }

        .theme-toggle::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        [data-theme="dark"] .theme-toggle {
            background-color: var(--primary-color);
        }

        [data-theme="dark"] .theme-toggle::before {
            transform: translateX(30px);
        }

        .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            transition: opacity 0.3s ease;
        }

        .theme-icon-sun {
            left: 8px;
            color: #f39c12;
        }

        .theme-icon-moon {
            right: 8px;
            color: #f1c40f;
        }

        [data-theme="light"] .theme-icon-moon {
            opacity: 0.3;
        }

        [data-theme="dark"] .theme-icon-sun {
            opacity: 0.3;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        .mobile-toggle {
            display: none;
        }

        /* SCROLLBAR */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        /* Dark theme scrollbar */
        [data-theme="dark"] ::-webkit-scrollbar {
            background-color: var(--card-bg);
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
        }

        /* LOADING SPINNER */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* PAGINATION */
        [data-theme="dark"] .pagination .page-link {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        [data-theme="dark"] .pagination .page-link:hover {
            background-color: var(--border-color);
            color: var(--text-color);
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        [data-theme="dark"] .pagination .page-item.disabled .page-link {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        /* ====================================================== */
        /* ACTIVITY HISTORY - DARK MODE SUPPORT */
        /* ====================================================== */

        /* List Group Items */
        .list-group-item.activity-item {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--border-color);
            transition: all 0.3s ease;
        }

        .list-group-item.activity-item:hover {
            background-color: var(--table-hover);
        }

        /* Activity Icons */
        .activity-icon {
            color: var(--info-color);
        }

        [data-theme="dark"] .activity-icon {
            color: #5dade2;
        }

        /* List Group in Dark Mode */
        [data-theme="dark"] .list-group-item {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        [data-theme="dark"] .list-group-item-action:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .list-group-item-action:focus {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Badge colors remain vibrant in dark mode */
        [data-theme="dark"] .badge.bg-success {
            background-color: #27ae60 !important;
        }

        [data-theme="dark"] .badge.bg-danger {
            background-color: #e74c3c !important;
        }

        [data-theme="dark"] .badge.bg-primary {
            background-color: #3498db !important;
        }

        /* Alert in Dark Mode */
        [data-theme="dark"] .alert-warning {
            background-color: rgba(243, 156, 18, 0.2);
            border-color: rgba(243, 156, 18, 0.3);
            color: #f39c12;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-box-seam"></i> Management Produksi Linknet Koperasi
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            {{-- Menu ini HANYA untuk Admin & User --}}
            @if(auth()->user()->canAccessIgi())

            <a class="nav-link {{ request()->is('igi*') ? 'active' : '' }}" href="{{ route('igi.index') }}">
                <i class="bi bi-inbox"></i> IGI
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

            @endif

            {{-- Download Data - SEMUA role bisa akses --}}
            <a class="nav-link {{ request()->is('download*') ? 'active' : '' }}" href="{{ route('download.index') }}">
                <i class="bi bi-download"></i> Download Data
            </a>

            {{-- User Management - HANYA Admin --}}
            @if(auth()->user()->isAdmin())
            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="bi bi-people"></i> User Management
            </a>
            @endif

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
                <button class="btn btn-outline-primary mobile-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h5">
                    <span class="page-title">@yield('page-title', 'Dashboard')</span>
                </span>
                <div class="d-flex align-items-center gap-3">
                    <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                        <i class="bi bi-sun-fill theme-icon theme-icon-sun"></i>
                        <i class="bi bi-moon-stars-fill theme-icon theme-icon-moon"></i>
                    </button>

                    <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : (auth()->user()->isTamu() ? 'warning' : 'primary') }} me-2">
                        {{ strtoupper(auth()->user()->role) }}
                    </span>
                    <span class="me-3">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
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

    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============================================
        // THEME TOGGLE FUNCTIONALITY
        // ============================================
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;

        // Load saved theme from localStorage
        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', currentTheme);

        // Toggle theme
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Optional: Play sound on theme change
            playThemeChangeSound();
        });

        function playThemeChangeSound() {
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Toggle sidebar for mobile
        function toggleSidebar() {
            $('#sidebar').toggleClass('show');
        }

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('.sidebar, .mobile-toggle').length) {
                    $('#sidebar').removeClass('show');
                }
            }
        });

        // ============================================
        // SOUND NOTIFICATION FUNCTIONS
        // ============================================

        // Suara berhasil - BEEP 1 kali
        function playScanSuccessSound() {
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(2000, audioContext.currentTime);

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.15);
        }

        // Suara gagal - BEEP 2 kali
        function playScanErrorSound() {
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();

            // First beep
            const oscillator1 = audioContext.createOscillator();
            const gainNode1 = audioContext.createGain();

            oscillator1.connect(gainNode1);
            gainNode1.connect(audioContext.destination);

            oscillator1.type = 'square';
            oscillator1.frequency.setValueAtTime(300, audioContext.currentTime);

            gainNode1.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode1.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

            oscillator1.start(audioContext.currentTime);
            oscillator1.stop(audioContext.currentTime + 0.15);

            // Second beep - langsung setelah beep pertama
            const oscillator2 = audioContext.createOscillator();
            const gainNode2 = audioContext.createGain();

            oscillator2.connect(gainNode2);
            gainNode2.connect(audioContext.destination);

            oscillator2.type = 'square';
            oscillator2.frequency.setValueAtTime(300, audioContext.currentTime + 0.15);

            gainNode2.gain.setValueAtTime(0.3, audioContext.currentTime + 0.15);
            gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator2.start(audioContext.currentTime + 0.15);
            oscillator2.stop(audioContext.currentTime + 0.3);
        }
    </script>

    @stack('scripts')
</body>

</html>