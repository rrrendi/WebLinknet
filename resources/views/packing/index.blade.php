@extends('layouts.app')

@section('title', 'Packing')
@section('page-title', 'Packing')

@section('content')
<div class="container-fluid">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring" type="button">
                <i class="bi bi-bar-chart"></i> Monitoring Packing
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="packing-tab" data-bs-toggle="tab" data-bs-target="#packing" type="button">
                <i class="bi bi-box-seam"></i> Packing
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab 1: Monitoring Packing -->
        <div class="tab-pane fade show active" id="monitoring" role="tabpanel">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Packing</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ONT</th>
                                    <th>STB</th>
                                    <th>ROUTER</th>
                                    <th class="bg-success text-white">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-success bg-opacity-10">
                                        <h3 class="mb-0 text-success">{{ $monitoring['ONT'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-success bg-opacity-10">
                                        <h3 class="mb-0 text-success">{{ $monitoring['STB'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-success bg-opacity-10">
                                        <h3 class="mb-0 text-success">{{ $monitoring['ROUTER'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-success bg-opacity-25">
                                        <h2 class="mb-0 text-success fw-bold">{{ $monitoring['TOTAL'] ?? 0 }}</h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Progress Summary -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bi bi-trophy"></i> Status Packing</h6>
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h4 class="text-success">{{ $monitoring['TOTAL'] ?? 0 }}</h4>
                                            <small class="text-muted">Barang Selesai Di-packing</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-primary">{{ $monitoring['TOTAL'] ?? 0 }}</h4>
                                            <small class="text-muted">Siap Dikirim</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-info">100%</h4>
                                            <small class="text-muted">Tingkat Penyelesaian</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Packing -->
        <div class="tab-pane fade" id="packing" role="tabpanel">
            <div class="row">
                <!-- Form Packing -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Form Packing</h5>
                        </div>
                        <div class="card-body">

                            <form id="packingForm">
                                <div class="mb-3">
                                    <label class="form-label">Scan Barcode / Serial Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberPacking" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                    </div>
                                    <small class="text-muted">Tekan Enter setelah scan barcode</small>
                                </div>

                                <div id="autoFillPacking" style="display: none;">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" id="namaBarangPacking" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type / Merk</label>
                                        <input type="text" id="typePacking" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Packing</label>
                                        <input type="text" id="waktuPacking" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-box-seam"></i> Simpan Data Packing
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Packing -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Data Packing</h5>
                            <button class="btn btn-sm btn-primary" onclick="refreshPackingTable()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Packing Time</th>
                                            <th>Serial Number</th>
                                            <th>Nama Barang</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="packingTableBody">
                                        @forelse($recentPacking as $pack)
                                        <tr id="packing-row-{{ $pack->id }}">
                                            <td>{{ $pack->waktu_packing->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $pack->igi->serial_number ?? '-' }}</code></td>
                                            <td>{{ $pack->igi->nama_barang ?? '-' }}</td>
                                            <td>{{ $pack->igi->type ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> PACKED
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-warning btn-rollback-packing"
                                                        data-id="{{ $pack->id }}"
                                                        title="Rollback ke Rekondisi">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-fulldelete-packing"
                                                        data-id="{{ $pack->id }}"
                                                        title="Hapus dari semua proses">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="noPackingData">
                                            <td colspan="6" class="text-center">Belum ada data packing</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentPacking->links('pagination::bootstrap-5') }}
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
        let igiId = null;
        let timeIntervalId = null;

        function updatePackingTime() {
            const now = new Date();
            const formatted = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#waktuPacking').val(formatted);
        }

        $('#serialNumberPacking').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkSerialNumberPacking();
            }
        });

        function checkSerialNumberPacking() {
            const serialNumber = $('#serialNumberPacking').val().trim();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            $.ajax({
                url: '{{ route("packing.check-serial") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    if (response.success) {
                        igiId = response.data.igi_id;
                        $('#namaBarangPacking').val(response.data.nama_barang);
                        $('#typePacking').val(response.data.type);
                        updatePackingTime();

                        // Clear interval lama jika ada
                        if (timeIntervalId) clearInterval(timeIntervalId);
                        // Set interval baru
                        timeIntervalId = setInterval(updatePackingTime, 1000);

                        $('#autoFillPacking').slideDown();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Serial Number tidak valid!');
                    $('#serialNumberPacking').val('').focus();
                    $('#autoFillPacking').slideUp();
                    igiId = null;
                    if (timeIntervalId) clearInterval(timeIntervalId);
                }
            });
        }

        $('#packingForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                igi_id: igiId
            };

            $.ajax({
                url: '{{ route("packing.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        const waktu = new Date(data.waktu_packing).toLocaleString('id-ID');

                        const newRow = `
                        <tr id="packing-row-${data.id}">
                            <td>${waktu}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.nama_barang}</td>
                            <td>${data.type}</td>
                            <td><span class="badge bg-success"><i class="bi bi-check-circle"></i> PACKED</span></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-warning btn-rollback-packing" 
                                            data-id="${data.id}" 
                                            title="Rollback ke Rekondisi">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-fulldelete-packing" 
                                            data-id="${data.id}" 
                                            title="Hapus dari semua proses">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;

                        $('#noPackingData').remove();
                        $('#packingTableBody').prepend(newRow);

                        $('#packingForm')[0].reset();
                        $('#autoFillPacking').slideUp();
                        $('#serialNumberPacking').focus();
                        if (timeIntervalId) clearInterval(timeIntervalId);
                        igiId = null;

                        alert('✅ Data packing berhasil disimpan!\nBarang siap untuk dikirim.');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan!');
                }
            });
        });

        // Cleanup saat form atau tab hide
        $('#packingForm').on('reset', function() {
            if (timeIntervalId) clearInterval(timeIntervalId);
        });

        $('a[data-bs-toggle="tab"]').on('hide.bs.tab', function() {
            if (timeIntervalId) clearInterval(timeIntervalId);
        });

        // Cleanup saat page unload
        $(window).on('beforeunload', function() {
            if (timeIntervalId) clearInterval(timeIntervalId);
        });
    });

    // Rollback
    $(document).on('click', '.btn-rollback-packing', function() {
        const id = $(this).data('id');

        if (!confirm('Yakin ingin rollback data ini ke Rekondisi?')) {
            return;
        }

        $.ajax({
            url: `/packing/${id}/rollback`,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    $(`#packing-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        if ($('#packingTableBody tr').length === 0) {
                            $('#packingTableBody').html('<tr id="noPackingData"><td colspan="6" class="text-center">Belum ada data packing</td></tr>');
                        }
                    });
                    alert('✓ Data berhasil di-rollback ke Rekondisi!');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal melakukan rollback!');
            }
        });
    });

    // Full Delete
    $(document).on('click', '.btn-fulldelete-packing', function() {
        const id = $(this).data('id');

        if (!confirm('⚠️ PERINGATAN: Ini akan menghapus data dari SEMUA proses!\n\nYakin untuk melanjutkan?')) {
            return;
        }

        $.ajax({
            url: `/packing/${id}/full-delete`,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    $(`#packing-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        if ($('#packingTableBody tr').length === 0) {
                            $('#packingTableBody').html('<tr id="noPackingData"><td colspan="6" class="text-center">Belum ada data packing</td></tr>');
                        }
                    });
                    alert('✓ Data berhasil dihapus dari semua proses!');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menghapus data!');
            }
        });
    });

    function refreshPackingTable() {
        location.reload();
    }
</script>
@endpush