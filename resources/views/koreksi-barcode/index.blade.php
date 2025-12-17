{{-- ================================================================ --}}
{{-- resources/views/koreksi-barcode/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Koreksi Barcode')
@section('page-title', 'Koreksi Barcode')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-search"></i> Cari & Tracking Barang</h6>
                </div>
                <div class="card-body">
                    <form id="searchForm">
                        <div class="mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" id="searchSerial" class="form-control form-control-lg"
                                placeholder="Scan atau ketik Serial Number" required autofocus>
                        </div>
                    </form>

                    <div id="loadingSection" style="display: none;" class="text-center py-3">
                        <div class="spinner-border"></div>
                        <p>Mencari data...</p>
                    </div>

                    <div id="resultSection" style="display: none;">
                        <hr>
                        <h6 class="text-primary"><i class="bi bi-box-seam"></i> Data IGI</h6>
                        <form id="koreksiForm">
                            <input type="hidden" id="koreksiIgiId">
                            <div class="mb-2"><strong>Pemilik:</strong> <span id="pemilik"></span></div>
                            <div class="mb-2"><strong>Wilayah:</strong> <span id="wilayah"></span></div>
                            <div class="mb-2"><strong>Tanggal Datang:</strong> <span id="tanggalDatang"></span></div>
                            <div class="mb-3">
                                <label class="form-label">Serial Number (Read Only)</label>
                                <input type="text" id="serialNumber" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">MAC Address</label>
                                <input type="text" id="macAddress" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis</label>
                                <select id="jenis" class="form-select" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="STB">STB</option>
                                    <option value="ONT">ONT</option>
                                    <option value="ROUTER">ROUTER</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Merk</label>
                                <input type="text" id="merk" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input type="text" id="type" class="form-control" required>
                            </div>
                            <div class="mb-3" id="stbIdGroup" style="display: none;">
                                <label class="form-label">STB ID</label>
                                <input type="text" id="stbId" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-save"></i> Update Data
                            </button>
                        </form>

                        <hr>
                        <h6 class="text-success"><i class="bi bi-clock-history"></i> Riwayat Aktivitas</h6>
                        <div id="activityHistory"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let currentIgiId = null;

        $('#searchSerial').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchBarang();
            }
        });

        function searchBarang() {
            const serialNumber = $('#searchSerial').val().trim();
            if (!serialNumber) return;

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
                        currentIgiId = response.data.id;
                        displayData(response.data);
                        loadActivityHistory(currentIgiId);
                    }
                },
                error: function(xhr) {
                    $('#loadingSection').hide();
                    alert(xhr.responseJSON?.message || 'Serial Number tidak ditemukan!');
                    $('#searchSerial').val('').focus();
                }
            });
        }

        function displayData(data) {
            $('#koreksiIgiId').val(data.id);
            $('#pemilik').text(data.pemilik);
            $('#wilayah').text(data.wilayah);
            $('#tanggalDatang').text(data.tanggal_datang);
            $('#serialNumber').val(data.serial_number);
            $('#macAddress').val(data.mac_address);
            $('#jenis').val(data.jenis);
            $('#merk').val(data.merk);
            $('#type').val(data.type);
            $('#stbId').val(data.stb_id || '');

            if (data.jenis === 'STB') {
                $('#stbIdGroup').show();
            } else {
                $('#stbIdGroup').hide();
            }

            $('#resultSection').slideDown();
        }

        function loadActivityHistory(igiId) {
            $.ajax({
                url: `/koreksi-barcode/${igiId}/activity`,
                method: 'GET',
                success: function(response) {
                    let html = '<div class="list-group">';

                    response.data.forEach(activity => {
                        // Tentukan warna badge berdasarkan result
                        const badgeClass = activity.result === 'OK' ? 'bg-success' :
                            activity.result === 'NOK' ? 'bg-danger' :
                            'bg-primary';

                        // Tentukan icon berdasarkan aktivitas
                        const icon = getActivityIcon(activity.aktivitas);

                        // Button delete (jika user bisa delete)
                        const canDelete = activity.can_delete ?
                            `<button class="btn btn-sm btn-danger btn-delete-activity" data-id="${activity.id}">
                        <i class="bi bi-trash"></i>
                    </button>` : '';

                        html += `
                    <div class="list-group-item list-group-item-action activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi ${icon} me-2 activity-icon"></i>
                                    <strong class="me-2">${activity.aktivitas}</strong>
                                    <span class="badge ${badgeClass}">${activity.result_label}</span>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock"></i> ${activity.tanggal} | 
                                    <i class="bi bi-person"></i> ${activity.user_name}
                                </small>
                                ${activity.keterangan ? `<small class="text-muted d-block mt-1"><i class="bi bi-info-circle"></i> ${activity.keterangan}</small>` : ''}
                            </div>
                            <div class="ms-2">
                                ${canDelete}
                            </div>
                        </div>
                    </div>
                `;
                    });

                    html += '</div>';

                    $('#activityHistory').html(html);
                },
                error: function() {
                    $('#activityHistory').html(`
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Tidak dapat memuat riwayat aktivitas
                </div>
            `);
                }
            });
        }

        // Helper function untuk icon
        function getActivityIcon(aktivitas) {
            const icons = {
                'IGI': 'bi-inbox-fill',
                'UJI_FUNGSI': 'bi-check-circle-fill',
                'REPAIR': 'bi-tools',
                'REKONDISI': 'bi-arrow-clockwise',
                'SERVICE_HANDLING': 'bi-wrench',
                'PACKING': 'bi-box-seam-fill',
                'KOREKSI': 'bi-pencil-square'
            };
            return icons[aktivitas] || 'bi-circle-fill';
        }

        $('#koreksiForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: `/koreksi-barcode/${currentIgiId}/update`,
                method: 'PUT',
                data: {
                    mac_address: $('#macAddress').val(),
                    jenis: $('#jenis').val(),
                    merk: $('#merk').val(),
                    type: $('#type').val(),
                    stb_id: $('#stbId').val()
                },
                success: function(response) {
                    alert('Data berhasil diperbarui!');
                    loadActivityHistory(currentIgiId);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error!');
                }
            });
        });

        $(document).on('click', '.btn-delete-activity', function() {
            const id = $(this).data('id');
            const activityName = $(this)
                .closest('.activity-item')
                .find('strong')
                .first()
                .text();

            // CONFIRM BAWAAN (sama seperti Uji Fungsi)
            if (!confirm(`Yakin hapus aktivitas "${activityName}"?`)) return;

            const $btn = $(this);
            const originalHtml = $btn.html();

            $btn.prop('disabled', true).html('...');

            $.ajax({
                url: `/koreksi-barcode/activity/${id}`,
                method: 'DELETE',

                success: function(response) {
                    alert(response.message || 'Aktivitas berhasil dihapus!');
                    playScanSuccessSound();
                    loadActivityHistory(currentIgiId);
                },

                error: function(xhr) {
                    playScanErrorSound();
                    $btn.prop('disabled', false).html(originalHtml);

                    let msg = 'Gagal menghapus aktivitas!';

                    if (xhr.responseJSON) {
                        const res = xhr.responseJSON;

                        if (res.message && res.detail) {
                            msg = `${res.message}\n\nKeterangan:\n${res.detail}`;
                        } else if (res.message) {
                            msg = res.message;
                        }
                    }

                    alert(msg);
                }
            });
        });
    });
</script>
@endpush
@endsection