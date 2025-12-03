@extends('layouts.app')

@section('title', 'Koreksi Barcode')
@section('page-title', 'Koreksi Barcode - Tracking & Koreksi Data')

@section('content')

<style>
    .label-fixed {
        width: 130px;
        white-space: nowrap;
    }

    .value-col {
        flex: 1;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Search & Tracking Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search"></i> Cari & Tracking Barang</h5>
                </div>
                <div class="card-body">
                    <form id="searchForm">
                        <div class="mb-3">
                            <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" id="searchSerial" class="form-control" placeholder="Scan atau ketik Serial Number" required autofocus>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                            <small class="text-muted">Tekan Enter setelah scan</small>
                        </div>
                    </form>

                    <!-- Loading -->
                    <div id="loadingSection" style="display: none;" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Mencari data...</p>
                    </div>

                    <!-- Result Section - Data IGI -->
                    <div id="resultSection" style="display: none;">
                        <hr>
                        <h6 class="mb-3 text-primary"><i class="bi bi-box-seam"></i> Data IGI (Incoming)</h6>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <input type="hidden" id="koreksiIgiId">

                                <div class="mb-2 d-flex align-items-center">
                                    <strong class="label-fixed">No DO:</strong>
                                    <span id="noDo" class="badge bg-info value-col"></span>
                                </div>

                                <div class="mb-2 d-flex align-items-center">
                                    <strong class="label-fixed">Tanggal Datang:</strong>
                                    <span id="tanggalDatang" class="value-col"></span>
                                </div>

                                <div class="mb-2 d-flex align-items-center">
                                    <strong class="label-fixed">Nama Barang:</strong>
                                    <select id="koreksiNamaBarang" class="form-select value-col" required>
                                        <option value="">Pilih Nama Barang</option>
                                        <option value="ONT">ONT</option>
                                        <option value="STB">STB</option>
                                        <option value="ROUTER">ROUTER</option>
                                    </select>
                                </div>

                                <div class="mb-2 d-flex align-items-center">
                                    <strong class="label-fixed">Type:</strong>
                                    <input type="text" id="koreksiType" class="form-control value-col" required>
                                </div>

                                <div class="mb-2 d-flex align-items-center">
                                    <strong class="label-fixed">Serial Number:</strong>
                                    <code id="serialNumber" class="value-col"></code>
                                </div>

                                <div class="mb-0 d-flex align-items-center">
                                    <strong class="label-fixed">Status Proses:</strong>
                                    <span id="statusProses" class="badge bg-primary value-col"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Tracking -->
                        <h6 class="mb-3 text-success"><i class="bi bi-diagram-3"></i> Status Progress Barang</h6>
                        <div id="statusTracking" class="mb-3">
                            <!-- Will be filled by JavaScript -->
                        </div>

                        <!-- Button Simpan Perubahan -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="button" class="btn btn-warning" id="btnSimpanPerubahan">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="col-md-7">
            <!-- History Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Koreksi</h5>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <form method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('koreksi-barcode.index') }}" class="btn btn-sm btn-secondary w-100">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Serial Number</th>
                                    <th>Perubahan</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td>{{ $item->tanggal_koreksi->format('d-m-Y H:i') }}</td>
                                    <td><code>{{ $item->igi->serial_number ?? '-' }}</code></td>
                                    <td>
                                        @if($item->nama_barang_lama != $item->nama_barang_baru)
                                        <div><small>Nama: <del>{{ $item->nama_barang_lama }}</del> → <strong class="text-success">{{ $item->nama_barang_baru }}</strong></small></div>
                                        @endif
                                        @if($item->type_lama != $item->type_baru)
                                        <div><small>Type: <del>{{ $item->type_lama }}</del> → <strong class="text-success">{{ $item->type_baru }}</strong></small></div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $item->user->name }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada riwayat koreksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $history->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentIgiData = null;

    $(document).ready(function() {
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            searchBarang();
        });

        $('#searchSerial').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchBarang();
            }
        });

        function searchBarang() {
            const serialNumber = $('#searchSerial').val().trim();

            if (!serialNumber) {
                alert('Serial Number tidak boleh kosong!');
                return;
            }

            $('#loadingSection').show();
            $('#resultSection').hide();

            $.ajax({
                url: '{{ route("koreksi-barcode.search") }}',
                method: 'POST',
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {
                    $('#loadingSection').hide();

                    if (response.success) {
                        currentIgiData = response.data;
                        displayDataBarang(response.data);
                        loadStatusTracking(serialNumber);
                    }
                },
                error: function(xhr) {
                    $('#loadingSection').hide();
                    const response = xhr.responseJSON;
                    alert(response.message || 'Serial Number tidak ditemukan!');
                    $('#searchSerial').val('').focus();
                }
            });
        }

        function displayDataBarang(data) {
            $('#koreksiIgiId').val(data.id);
            $('#noDo').text(data.no_do);
            $('#tanggalDatang').text(new Date(data.tanggal_datang).toLocaleString('id-ID'));
            $('#koreksiNamaBarang').val(data.nama_barang);
            $('#koreksiType').val(data.type);
            $('#serialNumber').text(data.serial_number);
            $('#statusProses').text(data.status_proses.replace('_', ' '));

            $('#resultSection').slideDown();
        }

        function loadStatusTracking(serialNumber) {
            $.ajax({
                url: '/koreksi-barcode/tracking/' + serialNumber,
                method: 'GET',
                success: function(response) {
                    displayStatusTracking(response);
                },
                error: function() {
                    $('#statusTracking').html('<div class="alert alert-warning">Tidak dapat memuat status tracking</div>');
                }
            });
        }

        function displayStatusTracking(data) {
            let html = '<div class="list-group">';

            html += `<div class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <strong>IGI</strong> <span class="badge bg-success float-end">✓</span></div>`;

            if (data.uji_fungsi) {
                const badgeClass = data.uji_fungsi.status === 'OK' ? 'bg-success' : 'bg-danger';
                html += `<div class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <strong>Uji Fungsi</strong> <span class="badge ${badgeClass} float-end">${data.uji_fungsi.status}</span></div>`;
            } else {
                html += `<div class="list-group-item bg-light"><i class="bi bi-circle text-secondary"></i> <strong class="text-muted">Uji Fungsi</strong> <span class="badge bg-secondary float-end">Belum</span></div>`;
            }

            if (data.repair) {
                const badgeClass = data.repair.status === 'OK' ? 'bg-success' : 'bg-danger';
                html += `<div class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <strong>Repair</strong> <span class="badge ${badgeClass} float-end">${data.repair.status}</span></div>`;
            } else {
                html += `<div class="list-group-item bg-light"><i class="bi bi-circle text-secondary"></i> <strong class="text-muted">Repair</strong> <span class="badge bg-secondary float-end">Belum</span></div>`;
            }

            if (data.rekondisi) {
                html += `<div class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <strong>Rekondisi</strong> <span class="badge bg-info float-end">✓</span></div>`;
            } else {
                html += `<div class="list-group-item bg-light"><i class="bi bi-circle text-secondary"></i> <strong class="text-muted">Rekondisi</strong> <span class="badge bg-secondary float-end">Belum</span></div>`;
            }

            if (data.service_handling) {
                html += `<div class="list-group-item"><i class="bi bi-exclamation-circle-fill text-warning"></i> <strong>Service Handling</strong> <span class="badge bg-danger float-end">NOK</span></div>`;
            }

            if (data.packing) {
                html += `<div class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i> <strong>Packing</strong> <span class="badge bg-success float-end">✓ Selesai</span></div>`;
            } else {
                html += `<div class="list-group-item bg-light"><i class="bi bi-circle text-secondary"></i> <strong class="text-muted">Packing</strong> <span class="badge bg-secondary float-end">Belum</span></div>`;
            }

            html += '</div>';
            $('#statusTracking').html(html);
        }

        $('#btnSimpanPerubahan').on('click', function(e) {
            e.preventDefault();

            const igiId = $('#koreksiIgiId').val();
            const namaBarang = $('#koreksiNamaBarang').val();
            const type = $('#koreksiType').val();

            if (!namaBarang || !type) {
                alert('Nama Barang dan Type harus diisi!');
                return;
            }

            if (!confirm('Yakin ingin mengubah data barang ini?')) {
                return;
            }

            $.ajax({
                url: '{{ route("koreksi-barcode.update-data") }}',
                method: 'POST',
                data: {
                    igi_id: igiId,
                    nama_barang: namaBarang,
                    type: type
                },
                success: function(response) {
                    alert('✓ Data barang berhasil diperbarui di Master & Operasional!');
                    location.reload();
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    alert('✗ Gagal: ' + (response.message || 'Terjadi kesalahan'));
                }
            });
        });
    });
</script>
@endpush