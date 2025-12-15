@extends('layouts.app')

@section('title', 'Scan Detail Barang - ' . $bapb->no_ido)
@section('page-title', 'Scan Detail Barang')
@section('content')
<div class="container-fluid">
    <!-- Header Info BAPB -->
    <div class="card mb-3 bg-primary text-white">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Pemilik:</strong> {{ $bapb->pemilik }}
                </div>
                <div class="col-md-3">
                    <strong>Wilayah:</strong> {{ $bapb->wilayah }}
                </div>
                <div class="col-md-3">
                    <strong>Tanggal Datang:</strong> {{ $bapb->tanggal_datang->format('d-m-Y') }}
                </div>
                <div class="col-md-3">
                    <strong>Progress:</strong> 
                    <span class="badge bg-warning text-dark">{{ $bapb->total_scan }} / {{ $bapb->jumlah }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- FORM SCAN -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Form Scan Barang</h5>
                </div>
                <div class="card-body">
                    <form id="scanForm">
                        <input type="hidden" id="bapbId" value="{{ $bapb->id }}">
                        
                        <!-- Jenis, Merk, Type -->
                        <div class="mb-3">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select id="jenis" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="STB">STB</option>
                                <option value="ONT">ONT</option>
                                <option value="ROUTER">ROUTER</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Merk <span class="text-danger">*</span></label>
                            <select id="merk" class="form-select" required disabled>
                                <option value="">Pilih Merk</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select id="type" class="form-select" required disabled>
                                <option value="">Pilih Type</option>
                            </select>
                        </div>

                        <!-- Scan Fields -->
                        <div id="scanFields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Auto-focus aktif. Scan langsung dengan barcode scanner.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Scan Serial Number <span class="text-danger">*</span></label>
                                <input type="text" id="serialNumber" class="form-control form-control-lg" 
                                       placeholder="Scan atau ketik Serial Number" autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Scan MAC Address <span class="text-danger">*</span></label>
                                <input type="text" id="macAddress" class="form-control form-control-lg" 
                                       placeholder="Scan atau ketik MAC Address" autocomplete="off">
                            </div>

                            <!-- STB ID (hanya untuk STB) -->
                            <div class="mb-3" id="stbIdGroup" style="display: none;">
                                <label class="form-label">Scan STB ID <span class="text-danger">*</span></label>
                                <input type="text" id="stbId" class="form-control form-control-lg" 
                                       placeholder="Scan atau ketik STB ID" autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Scan Time</label>
                                <input type="text" id="scanTime" class="form-control" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABLE DATA -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table"></i> Data Barang Ter-scan</h5>
                    <a href="{{ route('igi.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke BAPB
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Scan Time</th>
                                    <th>Serial Number</th>
                                    <th>MAC Address</th>
                                    <th>STB ID</th>
                                    <th>Jenis</th>
                                    <th>Merk</th>
                                    <th>Type</th>
                                    <th>Scan By</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="scanTableBody">
                                @forelse($recentScans as $scan)
                                <tr id="scan-row-{{ $scan->id }}">
                                    <td>{{ $scan->scan_time->format('d-m-Y H:i:s') }}</td>
                                    <td><code>{{ $scan->serial_number }}</code></td>
                                    <td>{{ $scan->mac_address }}</td>
                                    <td>{{ $scan->stb_id ?? '-' }}</td>
                                    <td>{{ $scan->jenis }}</td>
                                    <td>{{ $scan->merk }}</td>
                                    <td>{{ $scan->type }}</td>
                                    <td>{{ $scan->scanner->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger btn-delete" 
                                                data-id="{{ $scan->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="noData">
                                    <td colspan="9" class="text-center">Belum ada data scan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $recentScans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let currentJenis = '';
    let currentMerk = '';
    let currentType = '';
    let merkId = null;
    
    // Update scan time setiap detik
    function updateScanTime() {
        const now = new Date();
        $('#scanTime').val(now.toLocaleString('id-ID'));
    }
    setInterval(updateScanTime, 1000);
    updateScanTime();

    // Jenis change - load merk
    $('#jenis').on('change', function() {
        const jenis = $(this).val();
        currentJenis = jenis;
        
        $('#merk').prop('disabled', !jenis).html('<option value="">Pilih Merk</option>');
        $('#type').prop('disabled', true).html('<option value="">Pilih Type</option>');
        $('#scanFields').hide();
        
        if (jenis) {
            $.get(`/igi/api/merk/${jenis}`, function(data) {
                data.forEach(item => {
                    $('#merk').append(`<option value="${item.id}">${item.merk}</option>`);
                });
            });
        }
    });

    // Merk change - load type
    $('#merk').on('change', function() {
        merkId = $(this).val();
        currentMerk = $(this).find('option:selected').text();
        
        $('#type').prop('disabled', !merkId).html('<option value="">Pilih Type</option>');
        $('#scanFields').hide();
        
        if (merkId) {
            $.get(`/igi/api/type/${merkId}`, function(data) {
                data.forEach(item => {
                    $('#type').append(`<option value="${item.type}">${item.type}</option>`);
                });
            });
        }
    });

    // Type change - show scan fields & auto-focus
    $('#type').on('change', function() {
        currentType = $(this).val();
        
        if (currentType) {
            $('#scanFields').fadeIn();
            
            // Show/hide STB ID field
            if (currentJenis === 'STB') {
                $('#stbIdGroup').show();
                $('#stbId').prop('required', true);
            } else {
                $('#stbIdGroup').hide();
                $('#stbId').prop('required', false).val('');
            }
            
            // AUTO FOCUS ke Serial Number
            setTimeout(() => $('#serialNumber').focus(), 100);
        } else {
            $('#scanFields').hide();
        }
    });

    // Auto-focus logic: Serial → MAC → STB (if STB) → Loop
    $('#serialNumber').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            if ($(this).val().trim()) {
                $('#macAddress').focus();
            }
        }
    });

    $('#macAddress').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($(this).val().trim()) {
                if (currentJenis === 'STB') {
                    $('#stbId').focus();
                } else {
                    submitScan();
                }
            }
        }
    });

    $('#stbId').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($(this).val().trim()) {
                submitScan();
            }
        }
    });

    // Submit Scan
    function submitScan() {
        const serialNumber = $('#serialNumber').val().trim();
        const macAddress = $('#macAddress').val().trim();
        const stbId = $('#stbId').val().trim();

        if (!serialNumber || !macAddress) {
            alert('Serial Number dan MAC Address wajib diisi!');
            return;
        }

        if (currentJenis === 'STB' && !stbId) {
            alert('STB ID wajib diisi untuk jenis STB!');
            return;
        }

        $.ajax({
            url: '{{ route("igi.store-detail") }}',
            method: 'POST',
            data: {
                bapb_id: $('#bapbId').val(),
                jenis: currentJenis,
                merk: currentMerk,
                type: currentType,
                serial_number: serialNumber,
                mac_address: macAddress,
                stb_id: stbId || null
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const row = `
                        <tr id="scan-row-${data.id}">
                            <td>${new Date(data.scan_time).toLocaleString('id-ID')}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.mac_address}</td>
                            <td>${data.stb_id || '-'}</td>
                            <td>${data.jenis}</td>
                            <td>${data.merk}</td>
                            <td>${data.type}</td>
                            <td>${data.scanner.name}</td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    $('#noData').remove();
                    $('#scanTableBody').prepend(row);
                    
                    // Reset scan fields & refocus
                    $('#serialNumber').val('');
                    $('#macAddress').val('');
                    $('#stbId').val('');
                    $('#serialNumber').focus();
                    
                    // Update progress di header
                    $('.badge.bg-warning').text(`${response.total_scan} / ${response.jumlah}`);
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error saat menyimpan!');
                $('#serialNumber').focus();
            }
        });
    }

    // Delete
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (!confirm('Yakin ingin menghapus scan ini?')) return;
        
        $.ajax({
            url: `/igi/detail/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#scan-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        if ($('#scanTableBody tr').length === 0) {
                            $('#scanTableBody').html('<tr id="noData"><td colspan="9" class="text-center">Belum ada data scan</td></tr>');
                        }
                    });
                }
            }
        });
    });
});
</script>
@endpush
@endsection