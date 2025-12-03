@extends('layouts.app')

@section('title', 'Uji Fungsi')
@section('page-title', 'Uji Fungsi')

@section('content')
<div class="container-fluid">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring" type="button">
                <i class="bi bi-bar-chart"></i> Monitoring Uji Fungsi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="scanning-tab" data-bs-toggle="tab" data-bs-target="#scanning" type="button">
                <i class="bi bi-upc-scan"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab 1: Monitoring -->
        <div class="tab-pane fade show active" id="monitoring" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Uji Fungsi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-light">FUNGSI OK</th>
                                    <th class="text-light">FUNGSI NOK</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['ONT', 'STB', 'ROUTER'] as $category)
                                <tr>
                                    <td><strong>{{ $category }}</strong></td>
                                    <td class="bg-success bg-opacity-10">
                                        <h4 class="mb-0 text-success">{{ $monitoring[$category]['ok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h4 class="mb-0 text-danger">{{ $monitoring[$category]['nok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-light">
                                        <h5 class="mb-0">{{ $monitoring[$category]['total'] ?? 0 }}</h5>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td><strong>TOTAL</strong></td>
                                    <td>
                                        <h3 class="mb-0 text-success">{{ $monitoring['TOTAL']['ok'] ?? 0 }}</h3>
                                    </td>
                                    <td>
                                        <h3 class="mb-0 text-danger">{{ $monitoring['TOTAL']['nok'] ?? 0 }}</h3>
                                    </td>
                                    <td>
                                        <h3 class="mb-0">{{ $monitoring['TOTAL']['total'] ?? 0 }}</h3>
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
                <!-- Scanning Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Form Scanning</h5>
                        </div>
                        <div class="card-body">
                            <form id="scanningForm">
                                <!-- Status Selection -->
                                <div class="mb-3">
                                    <label class="form-label">Pilih Status Hasil Uji <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusOK" value="OK" required checked>
                                            <label class="form-check-label" for="statusOK">
                                                <span class="badge bg-success">OK</span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusNOK" value="NOK" required>
                                            <label class="form-check-label" for="statusNOK">
                                                <span class="badge bg-danger">NOK</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Serial Number Input -->
                                <div class="mb-3">
                                    <label class="form-label">Scan Barcode / Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" id="serialNumber" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                    <small class="text-muted">Tekan Enter setelah scan</small>
                                </div>

                                <!-- Auto-fill Fields -->
                                <div id="autoFillSection" style="display: none;">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" id="namaBarang" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type</label>
                                        <input type="text" id="type" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Uji</label>
                                        <input type="text" id="waktuUji" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save"></i> Simpan Hasil Uji
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Hasil Scanning</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="resultsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Waktu Uji Fungsi</th>
                                            <th>Serial Number</th>
                                            <th>Nama Barang</th>
                                            <th>Type/Merk</th>
                                            <th>Hasil</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
                                        @foreach($recentUjiFungsi as $scan)
                                        <tr id="row-{{ $scan->id }}">
                                            <td>{{ $scan->waktu_uji->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $scan->igi->serial_number }}</code></td>
                                            <td>{{ $scan->igi->nama_barang }}</td>
                                            <td>{{ $scan->igi->type }}</td>
                                            <td>
                                                <span class="badge badge-{{ $scan->status == 'OK' ? 'ok' : 'nok' }}">
                                                    {{ $scan->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-uji" data-id="{{ $scan->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentUjiFungsi->links('pagination::bootstrap-5') }}
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
        let igiId = null; // Simpan igi_id di sini

        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            const formatted = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }).replace(/\//g, '-');
            $('#waktuUji').val(formatted);
        }

        // Check serial number on Enter key
        $('#serialNumber').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkSerialNumber();
            }
        });

        function checkSerialNumber() {
            const serialNumber = $('#serialNumber').val().trim();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            // Check if status is selected
            const status = $('input[name="status"]:checked').val();
            if (!status) {
                alert('Pilih status hasil uji terlebih dahulu!');
                return;
            }

            $.ajax({
                url: '{{ route("uji-fungsi.check-serial") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    if (response.success) {
                        igiId = response.data.igi_id; // Simpan igi_id dari response
                        $('#namaBarang').val(response.data.nama_barang);
                        $('#type').val(response.data.type);
                        updateTime();
                        setInterval(updateTime, 1000);
                        $('#autoFillSection').slideDown();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Serial Number tidak ditemukan!');
                    $('#serialNumber').val('').focus();
                    $('#autoFillSection').slideUp();
                    igiId = null; // Reset igi_id
                }
            });
        }

        // Submit form
        $('#scanningForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                igi_id: igiId, // Kirim igi_id ke controller
                status: $('input[name="status"]:checked').val()
            };

            $.ajax({
                url: '{{ route("uji-fungsi.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Add to table
                        const data = response.data.igi;
                        const badgeClass = response.data.status === 'OK' ? 'badge-ok' : 'badge-nok';
                        const newRow = `
                        <tr id="row-${response.data.id}">
                            <td>${new Date(response.data.waktu_uji).toLocaleString('id-ID')}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.nama_barang}</td>
                            <td>${data.type}</td>
                            <td><span class="badge ${badgeClass}">${response.data.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete-uji" data-id="${response.data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                        $('#resultsBody').prepend(newRow);

                        // Reset form
                        $('#scanningForm')[0].reset();
                        $('#autoFillSection').slideUp();
                        $('#serialNumber').focus();
                        igiId = null; // Reset igi_id

                        // Show success message
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data!');
                }
            });
        });

        // Delete record with event delegation
        $(document).on('click', '.btn-delete-uji', function() {
            const id = $(this).data('id');

            if (!confirm('Yakin ingin menghapus data ini?')) {
                return;
            }

            $.ajax({
                url: `/uji-fungsi/${id}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $(`#row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Gagal menghapus data!');
                }
            });
        });
    });
</script>
@endpush