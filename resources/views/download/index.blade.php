@extends('layouts.app')

@section('title', 'Download Data')
@section('page-title', 'Download Data Excel')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-download"></i> Export Data ke Excel</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('download.export') }}" method="POST" id="downloadForm">
                        @csrf

                        <!-- Pilih Modul -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Tabel/Modul <span class="text-danger">*</span></label>
                            <select name="modul" id="modul" class="form-select" required>
                                <option value="">-- Pilih Modul --</option>
                                <option value="igi">I.G.I Operasional (Data Aktif)</option>
                                <option value="igi_master">I.G.I Master (Data Permanen/Histori)</option>
                                <option value="uji_fungsi">Uji Fungsi</option>
                                <option value="repair">Repair</option>
                                <option value="rekondisi">Rekondisi</option>
                                <option value="service_handling">Service Handling</option>
                                <option value="packing">Packing</option>
                            </select>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                <strong>IGI Operasional:</strong> Data yang sedang aktif diproses | 
                                <strong>IGI Master:</strong> Semua histori termasuk yang sudah packing
                            </small>
                        </div>

                        <hr>

                        <!-- Filter Section -->
                        <h6 class="mb-3"><i class="bi bi-funnel"></i> Filter Data (Opsional)</h6>

                        <!-- Date Range -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" id="startDate">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" id="endDate">
                            </div>
                        </div>

                        <!-- Nama Barang Filter -->
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <select name="nama_barang[]" class="form-select" id="namaBarangFilter" multiple size="4">
                                @foreach($namaBarangList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Tekan <kbd>Ctrl</kbd> untuk pilih multiple. Kosongkan untuk semua.
                            </small>
                        </div>

                        <!-- Type Filter -->
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type[]" class="form-select" id="typeFilter" multiple size="4">
                                @foreach($typeList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Tekan <kbd>Ctrl</kbd> untuk pilih multiple. Kosongkan untuk semua.
                            </small>
                        </div>

                        <!-- Status Filter (only for Uji Fungsi & Repair) -->
                        <div class="mb-3" id="statusFilterDiv" style="display: none;">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" id="statusFilter">
                                <option value="">-- Semua Status --</option>
                                <option value="OK">OK</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </div>

                        <hr>

                        <!-- Info Box -->
                        

                        <!-- Download Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="btnDownload">
                                <i class="bi bi-download"></i> Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Format Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-table"></i> Format Data per Modul</h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionFormat">
                        <!-- IGI -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatIGI">
                                    I.G.I (Operasional & Master)
                                </button>
                            </h2>
                            <div id="formatIGI" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>No DO | Tanggal Datang | Nama Barang | Type | Serial Number | Total Scan | Status Proses</code>
                                </div>
                            </div>
                        </div>

                        <!-- Uji Fungsi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatUjiFungsi">
                                    Uji Fungsi
                                </button>
                            </h2>
                            <div id="formatUjiFungsi" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>Serial Number | Nama Barang | Type | Status | Keterangan | Waktu Uji</code>
                                </div>
                            </div>
                        </div>

                        <!-- Repair -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatRepair">
                                    Repair
                                </button>
                            </h2>
                            <div id="formatRepair" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>Serial Number | Nama Barang | Type | Status | Jenis Kerusakan | Tindakan | Waktu Repair</code>
                                </div>
                            </div>
                        </div>

                        <!-- Rekondisi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatRekondisi">
                                    Rekondisi
                                </button>
                            </h2>
                            <div id="formatRekondisi" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>Serial Number | Nama Barang | Type | Tindakan | Waktu Rekondisi</code>
                                </div>
                            </div>
                        </div>

                        <!-- Service Handling -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatService">
                                    Service Handling
                                </button>
                            </h2>
                            <div id="formatService" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>Serial Number | Nama Barang | Type | Sumber | Status | Keterangan | Waktu Service</code>
                                </div>
                            </div>
                        </div>

                        <!-- Packing -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatPacking">
                                    Packing
                                </button>
                            </h2>
                            <div id="formatPacking" class="accordion-collapse collapse" data-bs-parent="#accordionFormat">
                                <div class="accordion-body">
                                    <code>Serial Number | Nama Barang | Type | Kondisi Box | Catatan | Waktu Packing</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide status filter based on selected modul
    $('#modul').on('change', function() {
        const modul = $(this).val();
        
        if (modul === 'uji_fungsi' || modul === 'repair') {
            $('#statusFilterDiv').slideDown();
        } else {
            $('#statusFilterDiv').slideUp();
            $('#statusFilter').val('');
        }
    });

    // Validate date range
    $('#downloadForm').on('submit', function(e) {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir!');
                return false;
            }
        }

        // Show loading
        const btn = $('#btnDownload');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengunduh...');
        
        // Re-enable after 3 seconds
        setTimeout(function() {
            btn.prop('disabled', false);
            btn.html('<i class="bi bi-download"></i> Download Excel');
        }, 3000);
    });
});
</script>
@endpush