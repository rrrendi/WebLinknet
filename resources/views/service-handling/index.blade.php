@extends('layouts.app')

@section('title', 'Service Handling')
@section('page-title', 'Service Handling')

@section('content')
<div class="container-fluid">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring" type="button">
                <i class="bi bi-bar-chart"></i> Monitoring
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="scanning-tab" data-bs-toggle="tab" data-bs-target="#scanning" type="button">
                <i class="bi bi-wrench"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab 1: Monitoring Service Handling -->
        <div class="tab-pane fade show active" id="monitoring" role="tabpanel">
            <!-- Header Info -->

            <!-- Ringkasan Data NOK -->
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Service Handling</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ONT</th>
                                    <th>STB</th>
                                    <th>ROUTER</th>
                                    <th class="bg-danger">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-danger bg-opacity-10">
                                        <h3 class="mb-0 text-danger">{{ $monitoring['ONT'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h3 class="mb-0 text-danger">{{ $monitoring['STB'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h3 class="mb-0 text-danger">{{ $monitoring['ROUTER'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-danger bg-opacity-25">
                                        <h2 class="mb-0 text-danger fw-bold">{{ $monitoring['TOTAL'] ?? 0 }}</h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detail Data NOK -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Detail Data NOK</h5>
                    <button class="btn btn-sm btn-secondary" id="toggleDetailBtn">
                        <i class="bi bi-eye-slash"></i> Sembunyikan Detail
                    </button>
                </div>
                <div class="card-body" id="detailSection">
                    <!-- Filter & Search -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" id="searchNokData" class="form-control" placeholder="Cari Serial Number, Nama Barang, atau Type...">
                        </div>
                        <div class="col-md-4">
                            <select id="categoryFilterNok" class="form-select">
                                <option value="">Semua</option>
                                <option value="ONT">ONT</option>
                                <option value="STB">STB</option>
                                <option value="ROUTER">ROUTER</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabel Detail NOK -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Serial Number</th>
                                    <th>Nama Barang</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Sumber</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="nokDetailTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Actual Scanning -->
        <div class="tab-pane fade" id="scanning" role="tabpanel">
            <div class="row">
                <!-- Form Scanning -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Form Service Handling</h5>
                        </div>
                        <div class="card-body">
                            <!-- Deskripsi -->
                            

                            <!-- Info Box -->
                            

                            <form id="serviceHandlingForm">
                                <!-- Serial Number Input -->
                                <div class="mb-3">
                                    <label class="form-label">Scan Barcode / Serial Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberService" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                    </div>
                                    <small class="text-muted">Tekan Enter setelah scan barcode</small>
                                </div>

                                <!-- Auto-fill Section -->
                                <div id="autoFillService" style="display: none;">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" id="namaBarangService" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type / Merk</label>
                                        <input type="text" id="typeService" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Sumber</label>
                                        <input type="text" id="sumberService" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" value="NOK" class="form-control bg-danger text-white" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Service</label>
                                        <input type="text" id="waktuService" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-save"></i> Simpan ke Service Handling
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Service Handling -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Data Service Handling</h5>
                            <button class="btn btn-sm btn-primary" onclick="refreshServiceTable()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Service Handling Time</th>
                                            <th>Serial Number</th>
                                            <th>Nama Barang</th>
                                            <th>Type</th>
                                            <th>Sumber</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="serviceTableBody">
                                        @forelse($recentService as $scan)
                                        <tr id="service-row-{{ $scan->id }}">
                                            <td>{{ $scan->waktu_service->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $scan->igi->serial_number }}</code></td>
                                            <td>{{ $scan->igi->nama_barang }}</td>
                                            <td>{{ $scan->igi->type }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $scan->sumber }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-nok">{{ $scan->status }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-service" data-id="{{ $scan->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="noServiceData">
                                            <td colspan="7" class="text-center">Belum ada data service handling</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentService->links('pagination::bootstrap-5') }}
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
        let igiId = null; // Simpan igi_id

        // Load NOK detail data
        loadNokDetailData();

        // Toggle detail section
        $('#toggleDetailBtn').on('click', function() {
            $('#detailSection').slideToggle();
            const icon = $(this).find('i');
            if (icon.hasClass('bi-eye-slash')) {
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
                $(this).html('<i class="bi bi-eye"></i> Tampilkan Detail');
            } else {
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
                $(this).html('<i class="bi bi-eye-slash"></i> Sembunyikan Detail');
            }
        });

        // Search NOK data
        let searchTimeout;
        $('#searchNokData, #categoryFilterNok').on('input change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadNokDetailData();
            }, 500);
        });

        // Load NOK detail data via AJAX
        function loadNokDetailData() {
            const search = $('#searchNokData').val();
            const category = $('#categoryFilterNok').val();

            $.ajax({
                url: '{{ route("service-handling.nok-data") }}',
                method: 'GET',
                data: {
                    search: search,
                    category: category
                },
                success: function(data) {
                    let html = '';

                    if (data.length > 0) {
                        data.forEach(function(item) {
                            const timestamp = new Date(item.timestamp).toLocaleString('id-ID');
                            html += `
                            <tr>
                                <td>${timestamp}</td>
                                <td><code>${item.serial_number}</code></td>
                                <td>${item.nama_barang}</td>
                                <td>${item.type}</td>
                                <td><span class="badge badge-nok">${item.status}</span></td>
                                <td><span class="badge bg-secondary">${item.sumber}</span></td>
                                <td><small class="text-muted">Menunggu Service</small></td>
                            </tr>
                        `;
                        });
                    } else {
                        html = '<tr><td colspan="7" class="text-center">Tidak ada data NOK</td></tr>';
                    }

                    $('#nokDetailTableBody').html(html);
                },
                error: function() {
                    $('#nokDetailTableBody').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>');
                }
            });
        }

        // Update waktu real-time
        function updateServiceTime() {
            const now = new Date();
            const formatted = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#waktuService').val(formatted);
        }

        // Check serial number saat Enter ditekan
        $('#serialNumberService').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkSerialNumberService();
            }
        });

        function checkSerialNumberService() {
            const serialNumber = $('#serialNumberService').val().trim();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            $.ajax({
                url: '{{ route("service-handling.check-serial") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    if (response.success) {
                        igiId = response.data.igi_id; // Simpan igi_id
                        $('#namaBarangService').val(response.data.nama_barang);
                        $('#typeService').val(response.data.type);
                        $('#sumberService').val(response.data.sumber);
                        updateServiceTime();
                        setInterval(updateServiceTime, 1000);
                        $('#autoFillService').slideDown();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    let errorMessage = response.message || 'Serial Number tidak valid!';

                    if (xhr.status === 404) {
                        errorMessage = 'Serial Number tidak ditemukan dengan status NOK di Uji Fungsi atau Repair!\n\nPastikan barang sudah di-scan di Uji Fungsi atau Repair dengan status NOK.';
                    }

                    alert(errorMessage);
                    $('#serialNumberService').val('').focus();
                    $('#autoFillService').slideUp();
                    igiId = null;
                }
            });
        }

        // Submit form service handling
        $('#serviceHandlingForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                igi_id: igiId, // Kirim igi_id
                sumber: $('#sumberService').val()
            };

            $.ajax({
                url: '{{ route("service-handling.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Tambahkan ke tabel
                        const data = response.data;
                        const waktu = new Date(data.waktu_service).toLocaleString('id-ID');

                        const newRow = `
                        <tr id="service-row-${data.id}">
                            <td>${waktu}</td>
                            <td><code>${data.igi.serial_number}</code></td>
                            <td>${data.igi.nama_barang}</td>
                            <td>${data.igi.type}</td>
                            <td><span class="badge bg-secondary">${data.sumber}</span></td>
                            <td><span class="badge badge-nok">${data.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete-service" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                        $('#noServiceData').remove();
                        $('#serviceTableBody').prepend(newRow);

                        // Reset form
                        $('#serviceHandlingForm')[0].reset();
                        $('#autoFillService').slideUp();
                        $('#serialNumberService').focus();
                        igiId = null;

                        // Refresh NOK detail data
                        loadNokDetailData();

                        alert('Data service handling berhasil disimpan!');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data!');
                }
            });
        });
    });

    // Delete service with event delegation
    $(document).on('click', '.btn-delete-service', function() {
        const id = $(this).data('id');

        if (!confirm('Yakin ingin menghapus data service handling ini?')) {
            return;
        }

        $.ajax({
            url: `/service-handling/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#service-row-${id}`).fadeOut(300, function() {
                        $(this).remove();

                        // Cek jika tabel kosong
                        if ($('#serviceTableBody tr').length === 0) {
                            $('#serviceTableBody').html('<tr id="noServiceData"><td colspan="7" class="text-center">Belum ada data service handling</td></tr>');
                        }
                    });
                    alert('Data service handling berhasil dihapus!');
                }
            },
            error: function() {
                alert('Gagal menghapus data service handling!');
            }
        });
    });

    function refreshServiceTable() {
        location.reload();
    }
</script>
@endpush