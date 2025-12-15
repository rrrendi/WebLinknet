@extends('layouts.app')
@section('title', 'Dashboard Stok')
@section('page-title', 'Dashboard Management Produksi')
@section('content')
<div class="container-fluid">
    <!-- Stats Cards Row 1 -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total BAPB</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($stats['total_bapb']) }}</h2>
                            <small class="text-muted">Dokumen Header</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Barang</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($stats['total_barang']) }}</h2>
                            <small class="text-muted">Semua Item</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-box-seam fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Uji Fungsi OK</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($stats['total_uji_ok']) }}</h2>
                            <small class="text-muted">Lolos Test</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Uji Fungsi NOK</h6>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($stats['total_uji_nok']) }}</h2>
                            <small class="text-muted">Perlu Repair</small>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Repair OK</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($stats['total_repair_ok']) }}</h2>
                            <small class="text-muted">Berhasil Diperbaiki</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-tools fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Repair NOK</h6>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($stats['total_repair_nok']) }}</h2>
                            <small class="text-muted">Masih Rusak</small>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-tools fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Rekondisi</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($stats['total_rekondisi']) }}</h2>
                            <small class="text-muted">Sedang Rekondisi</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-arrow-clockwise fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Service Handling</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($stats['total_service']) }}</h2>
                            <small class="text-muted">Dalam Penanganan</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-wrench fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Packing Card -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 text-white-50">Total Packing (Siap Kirim)</h6>
                            <h1 class="mb-0 fw-bold display-4">{{ number_format($stats['total_packing']) }}</h1>
                            <p class="mb-0">Barang sudah selesai proses dan siap dikirim</p>
                        </div>
                        <div>
                            <i class="bi bi-box-seam" style="font-size: 5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Status Proses Barang</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">{{ number_format($statusBreakdown['IGI']) }}</h3>
                                <p class="text-muted mb-0 small">IGI</p>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <h3 class="text-info mb-1">{{ number_format($statusBreakdown['UJI_FUNGSI']) }}</h3>
                                <p class="text-muted mb-0 small">Uji Fungsi</p>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1">{{ number_format($statusBreakdown['REPAIR']) }}</h3>
                                <p class="text-muted mb-0 small">Repair</p>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1">{{ number_format($statusBreakdown['REKONDISI']) }}</h3>
                                <p class="text-muted mb-0 small">Rekondisi</p>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h3 class="text-danger mb-1">{{ number_format($statusBreakdown['SERVICE_HANDLING']) }}</h3>
                                <p class="text-muted mb-0 small">Service</p>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1">{{ number_format($statusBreakdown['PACKING']) }}</h3>
                                <p class="text-muted mb-0 small">Packing</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart per Jenis -->
    <div class="row mt-4">
        @foreach(['STB', 'ONT', 'ROUTER'] as $jenis)
            @php
                $totalJenis = $chartData[$jenis]['total'];
                $packingJenis = $chartData[$jenis]['packing'];
                $percentageJenis = $totalJenis > 0 ? round(($packingJenis / $totalJenis) * 100) : 0;
                $iconClass = $jenis === 'STB' ? 'tv' : ($jenis === 'ONT' ? 'router' : 'wifi');
                $colorClass = $jenis === 'STB' ? 'info' : ($jenis === 'ONT' ? 'success' : 'warning');
            @endphp
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header bg-{{ $colorClass }} text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-{{ $iconClass }}"></i>
                            {{ $jenis }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Masuk:</span>
                            <strong class="text-primary">{{ number_format($totalJenis) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Sudah Packing:</span>
                            <strong class="text-success">{{ number_format($packingJenis) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Dalam Proses:</span>
                            <strong class="text-warning">{{ number_format($totalJenis - $packingJenis) }}</strong>
                        </div>
                        <!-- Progress Bar -->
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $percentageJenis }}%;"
                                 aria-valuenow="{{ $percentageJenis }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $percentageJenis }}%
                            </div>
                        </div>
                        <small class="text-muted">Tingkat Penyelesaian</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('igi.create') }}" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-plus-circle d-block fs-3 mb-2"></i>
                                Tambah BAPB
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('uji-fungsi.index') }}" class="btn btn-success w-100 py-3">
                                <i class="bi bi-check-circle d-block fs-3 mb-2"></i>
                                Uji Fungsi
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('repair.index') }}" class="btn btn-warning w-100 py-3">
                                <i class="bi bi-tools d-block fs-3 mb-2"></i>
                                Repair
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('download.index') }}" class="btn btn-info w-100 py-3">
                                <i class="bi bi-download d-block fs-3 mb-2"></i>
                                Download Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection