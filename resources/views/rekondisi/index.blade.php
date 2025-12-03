@extends('layouts.app')

@section('title', 'Rekondisi')
@section('page-title', 'Rekondisi')

@section('content')
<div class="container-fluid">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring" type="button">
                <i class="bi bi-bar-chart"></i> Monitoring Rekondisi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="scanning-tab" data-bs-toggle="tab" data-bs-target="#scanning" type="button">
                <i class="bi bi-arrow-clockwise"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab 1: Monitoring Rekondisi -->
        <div class="tab-pane fade show active" id="monitoring" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Rekondisi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ONT</th>
                                    <th>STB</th>
                                    <th>ROUTER</th>
                                    <th class="bg-primary">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-info bg-opacity-10">
                                        <h3 class="mb-0 text-info">{{ $monitoring['ONT'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-info bg-opacity-10">
                                        <h3 class="mb-0 text-info">{{ $monitoring['STB'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-info bg-opacity-10">
                                        <h3 class="mb-0 text-info">{{ $monitoring['ROUTER'] ?? 0 }}</h3>
                                    </td>
                                    <td class="bg-primary bg-opacity-25">
                                        <h2 class="mb-0 text-primary fw-bold">{{ $monitoring['TOTAL'] ?? 0 }}</h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Box -->
                    
                </div>
            </div>
        </div>

        <!-- Tab 2: Actual Scanning -->
        <div class="tab-pane fade" id="scanning" role="tabpanel">
            <div class="row">
                <!-- Form Scanning -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Form Scanning Rekondisi</h5>
                        </div>
                        <div class="card-body">
                            <!-- Validasi Warning -->
                            

                            <form id="rekondisiForm">
                                <!-- Serial Number Input -->
                                <div class="mb-3">
                                    <label class="form-label">Scan Barcode / Serial Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberRekondisi" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                    </div>
                                    <small class="text-muted">Tekan Enter setelah scan barcode</small>
                                </div>

                                <!-- Auto-fill Section -->
                                <div id="autoFillRekondisi" style="display: none;">
                                    

                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" id="namaBarangRekondisi" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Type / Merk</label>
                                        <input type="text" id="typeRekondisi" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Rekondisi</label>
                                        <input type="text" id="waktuRekondisi" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-info w-100 text-white">
                                        <i class="bi bi-save"></i> Simpan ke Rekondisi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Actual Scanning -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Data Actual Scanning Rekondisi</h5>
                            <button class="btn btn-sm btn-primary" onclick="refreshRekondisiTable()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Rekondisi Time</th>
                                            <th>Serial Number</th>
                                            <th>Nama Barang</th>
                                            <th>Type</th>
                                            <th>Result</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rekondisiTableBody">
                                        @forelse($recentRekondisi as $scan)
                                        <tr id="rekondisi-row-{{ $scan->id }}">
                                            <td>{{ $scan->waktu_rekondisi->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $scan->igi->serial_number }}</code></td>
                                            <td>{{ $scan->igi->nama_barang }}</td>
                                            <td>{{ $scan->igi->type }}</td>
                                            <td>
                                                <span class="badge bg-info">REKONDISI</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-rekondisi" data-id="{{ $scan->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="noRekondisiData">
                                            <td colspan="6" class="text-center">Belum ada data rekondisi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $recentRekondisi->links('pagination::bootstrap-5') }}
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
        function updateRekondisiTime() {
            const now = new Date();
            const formatted = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#waktuRekondisi').val(formatted);
        }

        // Check serial number saat Enter ditekan
        $('#serialNumberRekondisi').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkSerialNumberRekondisi();
            }
        });

        function checkSerialNumberRekondisi() {
            const serialNumber = $('#serialNumberRekondisi').val().trim();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            $.ajax({
                url: '{{ route("rekondisi.check-serial") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    if (response.success) {
                        igiId = response.data.igi_id; // Simpan igi_id
                        $('#namaBarangRekondisi').val(response.data.nama_barang);
                        $('#typeRekondisi').val(response.data.type);
                        updateRekondisiTime();
                        setInterval(updateRekondisiTime, 1000);
                        $('#autoFillRekondisi').slideDown();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    let errorMessage = response.message || 'Serial Number tidak valid!';

                    if (xhr.status === 400) {
                        errorMessage = 'Serial Number belum melewati proses repair dengan status OK atau hasil Uji Fungsi masih NOK!\n\nPastikan barang sudah di-repair dengan status OK atau lulus Uji Fungsi.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Serial Number tidak ditemukan dalam database IGI!';
                    }

                    alert(errorMessage);
                    $('#serialNumberRekondisi').val('').focus();
                    $('#autoFillRekondisi').slideUp();
                    igiId = null;
                }
            });
        }

        // Submit form rekondisi
        $('#rekondisiForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                igi_id: igiId // Kirim igi_id
            };

            $.ajax({
                url: '{{ route("rekondisi.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Tambahkan ke tabel
                        const data = response.data;
                        const waktu = new Date(data.waktu_rekondisi).toLocaleString('id-ID');

                        const newRow = `
                        <tr id="rekondisi-row-${data.id}">
                            <td>${waktu}</td>
                            <td><code>${data.igi.serial_number}</code></td>
                            <td>${data.igi.nama_barang}</td>
                            <td>${data.igi.type}</td>
                            <td><span class="badge bg-info">REKONDISI</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete-rekondisi" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                        $('#noRekondisiData').remove();
                        $('#rekondisiTableBody').prepend(newRow);

                        // Reset form
                        $('#rekondisiForm')[0].reset();
                        $('#autoFillRekondisi').slideUp();
                        $('#serialNumberRekondisi').focus();
                        igiId = null;

                        alert('Data rekondisi berhasil disimpan!');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data!');
                }
            });
        });
    });

    // Delete rekondisi with event delegation
    $(document).on('click', '.btn-delete-rekondisi', function() {
        const id = $(this).data('id');

        if (!confirm('Yakin ingin menghapus data rekondisi ini?')) {
            return;
        }

        $.ajax({
            url: `/rekondisi/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#rekondisi-row-${id}`).fadeOut(300, function() {
                        $(this).remove();

                        // Cek jika tabel kosong
                        if ($('#rekondisiTableBody tr').length === 0) {
                            $('#rekondisiTableBody').html('<tr id="noRekondisiData"><td colspan="6" class="text-center">Belum ada data rekondisi</td></tr>');
                        }
                    });
                    alert('Data rekondisi berhasil dihapus!');
                }
            },
            error: function() {
                alert('Gagal menghapus data rekondisi!');
            }
        });
    });

    function refreshRekondisiTable() {
        location.reload();
    }
</script>
@endpush