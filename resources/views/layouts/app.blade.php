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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* SIDEBAR */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #000000ff 0%, var(--primary-color) 100%);
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
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
            padding: 15px 20px;
        }

        /* CARD */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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
        .table-hover tbody tr:hover {
            background-color: rgba(0, 68, 131, 0.05);
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
        .form-control:focus,
        .form-select:focus {
            border-color: var(--info-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* ALERT */
        .alert {
            border-radius: 8px;
            border: none;
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

        /* LOADING SPINNER */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
    </style>

    @stack('styles')
</head>

<!-- Tambahkan script ini SEBELUM tag </body> di layouts/app.blade.php -->
<!-- Letakkan sebelum @stack('scripts') -->

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
    // SOUND NOTIFICATION FUNCTION
    // ============================================
    function playScanSuccessSound() {
        // Create AudioContext
        const audioContext = new(window.AudioContext || window.webkitAudioContext)();

        // Create oscillator (sound generator)
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        // Connect nodes
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Configure sound (beep sound)
        oscillator.type = 'sine'; // Type: sine, square, sawtooth, triangle
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // Frequency: 800 Hz (high pitch beep)

        // Configure volume (0.0 to 1.0)
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime); // Volume: 30%
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2); // Fade out

        // Play sound
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2); // Duration: 0.2 seconds
    }

    function playScanErrorSound() {
        // Create AudioContext for error sound
        const audioContext = new(window.AudioContext || window.webkitAudioContext)();

        // Create oscillator
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        // Connect nodes
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Configure sound (lower pitch for error)
        oscillator.type = 'square'; // Square wave for harsh sound
        oscillator.frequency.setValueAtTime(200, audioContext.currentTime); // Lower frequency for error

        // Configure volume
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

        // Play sound
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3); // Slightly longer for error
    }

    // Alternative: Menggunakan HTML5 Audio dengan URL online (jika ingin suara lebih natural)
    function playScanSuccessSoundAlt() {
        const audio = new Audio('https://www.soundjay.com/button/beep-07.wav');
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Audio play failed:', e));
    }
</script>

@stack('scripts')

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


            <a class="nav-link {{ request()->is('download*') ? 'active' : '' }}" href="{{ route('download.index') }}">
                <i class="bi bi-download"></i> Download Data
            </a>

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
                    @yield('page-title', 'Dashboard')
                </span>

                <div class="d-flex align-items-center">
                    <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : 'primary' }} me-2">
                        {{ auth()->user()->isAdmin() ? 'ADMIN' : 'USER' }}
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
    </script>

    @stack('scripts')
</body>

</html>