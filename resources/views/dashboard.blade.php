@extends('layouts.app')

@section('title', 'Dashboard Stok')
@section('page-title', 'Dashboard Stok Management Produksi')

@section('content')
<div class="container-fluid">
    <!-- Info Dual Table System -->
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <h5><i class="bi bi-info-circle"></i> Sistem Dual Table</h5>
        <div class="row">
            <div class="col-md-6">
                <strong>📦 IGI Operasional:</strong> {{ number_format($totalIgi) }} data aktif (sedang diproses)
            </div>
            <div class="col-md-6">
                <strong>📚 IGI Master:</strong> {{ number_format($totalIgiMaster) }} data permanen (histori lengkap)
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row">
        <!-- Total IGI Operasional -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">IGI Operasional</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalIgi) }}</h2>
                            <small class="text-muted">Data Aktif</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-inbox fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Uji Fungsi OK -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Uji Fungsi OK</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalUjiFungsiOk) }}</h2>
                            <small class="text-muted">Total: {{ number_format($totalUjiFungsi) }}</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Uji Fungsi NOK -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Uji Fungsi NOK</h6>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($totalUjiFungsiNok) }}</h2>
                            <small class="text-muted">Total: {{ number_format($totalUjiFungsi) }}</small>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Repair OK -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Repair OK</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalRepairOk) }}</h2>
                            <small class="text-muted">Total: {{ number_format($totalRepair) }}</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-tools fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Repair NOK -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Repair NOK</h6>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($totalRepairNok) }}</h2>
                            <small class="text-muted">Total: {{ number_format($totalRepair) }}</small>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-tools fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Rekondisi -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Rekondisi</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($totalRekondisi) }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-arrow-clockwise fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Service Handling -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Service Handling</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalServiceHandling) }}</h2>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-wrench fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Packing -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Packing</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalPacking) }}</h2>
                            <small class="text-muted">Selesai & Dikirim</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-box-seam fs-1"></i>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Status Proses (IGI Operasional) Aktif</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <h3 class="text-primary">{{ number_format($statusBreakdown['IGI']) }}</h3>
                            <p class="text-muted mb-0">IGI</p>
                        </div>
                        <div class="col">
                            <h3 class="text-info">{{ number_format($statusBreakdown['UJI_FUNGSI']) }}</h3>
                            <p class="text-muted mb-0">Uji Fungsi</p>
                        </div>
                        <div class="col">
                            <h3 class="text-warning">{{ number_format($statusBreakdown['REPAIR']) }}</h3>
                            <p class="text-muted mb-0">Repair</p>
                        </div>
                        <div class="col">
                            <h3 class="text-success">{{ number_format($statusBreakdown['REKONDISI']) }}</h3>
                            <p class="text-muted mb-0">Rekondisi</p>
                        </div>
                        <div class="col">
                            <h3 class="text-danger">{{ number_format($statusBreakdown['SERVICE_HANDLING']) }}</h3>
                            <p class="text-muted mb-0">Service</p>
                        </div>
                        <div class="col">
                            <h3 class="text-success">{{ number_format($statusBreakdown['PACKING']) }}</h3>
                            <p class="text-muted mb-0">Packing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Stok Keseluruhan</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h1 class="display-4 fw-bold text-primary">{{ number_format($totalIgiMaster) }}</h1>
                            <p class="text-muted">Total Barang Masuk (Histori)</p>
                        </div>
                        <div class="col-md-4">
                            <h1 class="display-4 fw-bold text-success">{{ number_format($totalPacking) }}</h1>
                            <p class="text-muted">Barang Selesai (Packing)</p>
                        </div>
                        <div class="col-md-4">
                            <h1 class="display-4 fw-bold text-warning">{{ number_format($totalIgi) }}</h1>
                            <p class="text-muted">Barang Dalam Proses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart per Kategori -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-router"></i> ONT</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Masuk:</span>
                        <strong>{{ number_format($chartData['ONT']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sudah Packing:</span>
                        <strong class="text-success">{{ number_format($chartData['ONT']['packing']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dalam Proses:</span>
                        <strong class="text-warning">{{ number_format($chartData['ONT']['total'] - $chartData['ONT']['packing']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-tv"></i> STB</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Masuk:</span>
                        <strong>{{ number_format($chartData['STB']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sudah Packing:</span>
                        <strong class="text-success">{{ number_format($chartData['STB']['packing']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dalam Proses:</span>
                        <strong class="text-warning">{{ number_format($chartData['STB']['total'] - $chartData['STB']['packing']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="bi bi-wifi"></i> ROUTER</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Masuk:</span>
                        <strong>{{ number_format($chartData['ROUTER']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sudah Packing:</span>
                        <strong class="text-success">{{ number_format($chartData['ROUTER']['packing']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dalam Proses:</span>
                        <strong class="text-warning">{{ number_format($chartData['ROUTER']['total'] - $chartData['ROUTER']['packing']) }}</strong>
                    </div>
                </div>
            </div>
        </div>
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
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('igi.create') }}" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Tambah Data IGI
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('uji-fungsi.index') }}" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Uji Fungsi
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('repair.index') }}" class="btn btn-warning w-100">
                                <i class="bi bi-tools"></i> Repair
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('download.index') }}" class="btn btn-info w-100">
                                <i class="bi bi-download"></i> Download Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection