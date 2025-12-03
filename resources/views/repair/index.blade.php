@extends('layouts.app')

@section('title', 'Repair')
@section('page-title', 'Repair')

@section('content')
<div class="container-fluid">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring" type="button">
                <i class="bi bi-bar-chart"></i> Monitoring Repair
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="actual-tab" data-bs-toggle="tab" data-bs-target="#actual" type="button">
                <i class="bi bi-tools"></i> Actual Repair
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab 1: Monitoring Repair -->
        <div class="tab-pane fade show active" id="monitoring" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Status Repair</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-light">REPAIR OK</th>
                                    <th class="text-light">REPAIR NOK</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ONT</strong></td>
                                    <td class="bg-success bg-opacity-10">
                                        <h4 class="mb-0 text-success">{{ $monitoring['ONT']['ok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h4 class="mb-0 text-danger">{{ $monitoring['ONT']['nok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-light">
                                        <h5 class="mb-0">{{ $monitoring['ONT']['total'] ?? 0 }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>STB</strong></td>
                                    <td class="bg-success bg-opacity-10">
                                        <h4 class="mb-0 text-success">{{ $monitoring['STB']['ok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h4 class="mb-0 text-danger">{{ $monitoring['STB']['nok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-light">
                                        <h5 class="mb-0">{{ $monitoring['STB']['total'] ?? 0 }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>ROUTER</strong></td>
                                    <td class="bg-success bg-opacity-10">
                                        <h4 class="mb-0 text-success">{{ $monitoring['ROUTER']['ok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-danger bg-opacity-10">
                                        <h4 class="mb-0 text-danger">{{ $monitoring['ROUTER']['nok'] ?? 0 }}</h4>
                                    </td>
                                    <td class="bg-light">
                                        <h5 class="mb-0">{{ $monitoring['ROUTER']['total'] ?? 0 }}</h5>
                                    </td>
                                </tr>
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

        <!-- Tab 2: Actual Repair -->
        <div class="tab-pane fade" id="actual" role="tabpanel">
            <div class="row">
                <!-- Form Repair -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="bi bi-tools"></i> Form Repair</h5>
                        </div>
                        <div class="card-body">
                            <form id="repairForm">
                                <!-- Status Repair -->
                                <div class="mb-3">
                                    <label class="form-label">Status Repair <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="repairOK" value="OK" required checked>
                                            <label class="form-check-label" for="repairOK">
                                                <span class="badge bg-success">OK (Berhasil Diperbaiki)</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="repairNOK" value="NOK" required>
                                            <label class="form-check-label" for="repairNOK">
                                                <span class="badge bg-danger">NOK (Masih Rusak)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Serial Number Input -->
                                <div class="mb-3">
                                    <label class="form-label">Scan Barcode / Serial Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberRepair" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                    </div>
                                    <small class="text-muted">Tekan Enter setelah scan barcode</small>
                                </div>

                                <!-- Auto-fill Section -->
                                <div id="autoFillRepair" style="display: none;">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" id="namaBarangRepair" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type / Merk</label>
                                        <input type="text" id="typeRepair" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kerusakan <span class="text-danger">*</span></label>
                                        <select id="jenisKerusakan" class="form-select" required>
                                            <option value="">Pilih Jenis Kerusakan</option>
                                            @foreach($jenisKerusakan as $jenis)
                                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Repair</label>
                                        <input type="text" id="waktuRepair" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-save"></i> Simpan Hasil Perbaikan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat Repair -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Repair</h5>
                            <button class="btn btn-sm btn-primary" onclick="refreshRepairTable()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Repair Time</th>
                                            <th>Barcode</th>
                                            <th>Nama Barang</th>
                                            <th>Type</th>
                                            <th>Result</th>
                                            <th>Hasil Perbaikan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="repairTableBody">
                                        @forelse($recentRepairs as $repair)
                                        <tr id="repair-row-{{ $repair->id }}">
                                            <td>{{ $repair->waktu_repair->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $repair->igi->serial_number }}</code></td>
                                            <td>{{ $repair->igi->nama_barang }}</td>
                                            <td>{{ $repair->igi->type }}</td>
                                            <td>
                                                <span class="badge badge-{{ $repair->status == 'OK' ? 'ok' : 'nok' }}">
                                                    {{ $repair->status }}
                                                </span>
                                            </td>
                                            <td>{{ $repair->jenis_kerusakan }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-repair" data-id="{{ $repair->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="noRepairData">
                                            <td colspan="7" class="text-center">Belum ada data repair</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentRepairs->links('pagination::bootstrap-5') }}
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

        // Update waktu real-time
        function updateRepairTime() {
            const now = new Date();
            const formatted = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#waktuRepair').val(formatted);
        }

        // Check serial number saat Enter ditekan
        $('#serialNumberRepair').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkSerialNumberRepair();
            }
        });

        function checkSerialNumberRepair() {
            const serialNumber = $('#serialNumberRepair').val().trim();
            const status = $('input[name="status"]:checked').val();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            if (!status) {
                alert('Pilih status repair terlebih dahulu (OK/NOK)!');
                $('#serialNumberRepair').val('');
                return;
            }

            $.ajax({
                url: '{{ route("repair.check-serial") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    if (response.success) {
                        igiId = response.data.igi_id; // Simpan igi_id
                        $('#namaBarangRepair').val(response.data.nama_barang);
                        $('#typeRepair').val(response.data.type);
                        updateRepairTime();
                        setInterval(updateRepairTime, 1000);
                        $('#autoFillRepair').slideDown();
                        $('#jenisKerusakan').focus();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Serial Number tidak ditemukan dalam database IGI!');
                    $('#serialNumberRepair').val('').focus();
                    $('#autoFillRepair').slideUp();
                    igiId = null;
                }
            });
        }

        // Submit form repair
        $('#repairForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                igi_id: igiId, // Kirim igi_id
                status: $('input[name="status"]:checked').val(),
                jenis_kerusakan: $('#jenisKerusakan').val()
            };

            if (!formData.jenis_kerusakan) {
                alert('Pilih jenis kerusakan terlebih dahulu!');
                $('#jenisKerusakan').focus();
                return;
            }

            $.ajax({
                url: '{{ route("repair.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Tambahkan ke tabel
                        const data = response.data;
                        const badgeClass = data.status === 'OK' ? 'badge-ok' : 'badge-nok';
                        const waktu = new Date(data.waktu_repair).toLocaleString('id-ID');

                        const newRow = `
                        <tr id="repair-row-${data.id}">
                            <td>${waktu}</td>
                            <td><code>${data.igi.serial_number}</code></td>
                            <td>${data.igi.nama_barang}</td>
                            <td>${data.igi.type}</td>
                            <td><span class="badge ${badgeClass}">${data.status}</span></td>
                            <td>${data.jenis_kerusakan}</td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete-repair" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                        $('#noRepairData').remove();
                        $('#repairTableBody').prepend(newRow);

                        // Reset form
                        $('#repairForm')[0].reset();
                        $('#autoFillRepair').slideUp();
                        $('#serialNumberRepair').focus();
                        igiId = null;

                        alert('Data repair berhasil disimpan!');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data!');
                }
            });
        });
    });

    // Delete repair with event delegation
    $(document).on('click', '.btn-delete-repair', function() {
        const id = $(this).data('id');

        if (!confirm('Yakin ingin menghapus data repair ini?')) {
            return;
        }

        $.ajax({
            url: `/repair/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#repair-row-${id}`).fadeOut(300, function() {
                        $(this).remove();

                        // Cek jika tabel kosong
                        if ($('#repairTableBody tr').length === 0) {
                            $('#repairTableBody').html('<tr id="noRepairData"><td colspan="7" class="text-center">Belum ada data repair</td></tr>');
                        }
                    });
                    alert('Data repair berhasil dihapus!');
                }
            },
            error: function() {
                alert('Gagal menghapus data repair!');
            }
        });
    });

    function refreshRepairTable() {
        location.reload();
    }
</script>
@endpush