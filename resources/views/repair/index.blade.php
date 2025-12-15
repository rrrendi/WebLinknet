{{-- ================================================================ --}}
{{-- resources/views/repair/index.blade.php --}} 
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Repair')
@section('page-title', 'Repair')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#monitoring">
                <i class="bi bi-bar-chart"></i> Monitoring
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scanning" id="tabScanning">
                <i class="bi bi-upc-scan"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: MONITORING --}}
        <div class="tab-pane fade show active" id="monitoring">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Repair</h5></div>
                <div class="card-body">
                    @foreach(['Linknet', 'Telkomsel'] as $pemilik)
                    <h6 class="text-muted mb-3">{{ $pemilik }}</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-center">
                            <thead class="table-dark">
                                <tr><th>Jenis</th><th class="bg-success">OK</th><th class="bg-danger">NOK</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach(['STB', 'ONT', 'ROUTER'] as $jenis)
                                <tr>
                                    <td><strong>{{ $jenis }}</strong></td>
                                    <td class="bg-success bg-opacity-10"><h5 class="text-success mb-0">{{ $monitoring[$pemilik][$jenis]['ok'] }}</h5></td>
                                    <td class="bg-danger bg-opacity-10"><h5 class="text-danger mb-0">{{ $monitoring[$pemilik][$jenis]['nok'] }}</h5></td>
                                    <td class="bg-light"><h6 class="mb-0">{{ $monitoring[$pemilik][$jenis]['total'] }}</h6></td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td><strong>TOTAL</strong></td>
                                    <td><h5 class="text-success mb-0">{{ $monitoring[$pemilik]['TOTAL']['ok'] }}</h5></td>
                                    <td><h5 class="text-danger mb-0">{{ $monitoring[$pemilik]['TOTAL']['nok'] }}</h5></td>
                                    <td><h5 class="mb-0">{{ $monitoring[$pemilik]['TOTAL']['total'] }}</h5></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TAB 2: SCANNING --}}
        <div class="tab-pane fade" id="scanning">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-upc-scan"></i> Form Repair</h6>
                        </div>
                        <div class="card-body">
                            <form id="RepairForm">
                                {{-- System Test Result --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>System Test Result</strong></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="result" value="OK" id="resultOK" checked>
                                            <label class="form-check-label" for="resultOK">
                                                <span class="badge bg-success">OK</span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="result" value="NOK" id="resultNOK">
                                            <label class="form-check-label" for="resultNOK">
                                                <span class="badge bg-danger">NOK</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Jenis Kerusakan --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jenis Kerusakan</strong></label>
                                    <select class="form-select" id="jenisKerusakan" name="jenis_kerusakan" required>
                                        <option value="">-- Pilih Jenis Kerusakan --</option>
                                        <option value="Rusak Fisik">Rusak Fisik</option>
                                        <option value="Tidak Menyala">Tidak Menyala</option>
                                        <option value="Port Rusak">Port Rusak</option>
                                        <option value="WiFi Bermasalah">WiFi Bermasalah</option>
                                        <option value="Tidak Terdeteksi">Tidak Terdeteksi</option>
                                        <option value="Mati Total">Mati Total</option>
                                        <option value="Hang/Freeze">Hang/Freeze</option>
                                        <option value="Kabel Putus">Kabel Putus</option>
                                        <option value="Adaptor Rusak">Adaptor Rusak</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                {{-- Scan Barcode --}}
                                <div class="mb-3">
                                    <label class="form-label"><strong>Scan Barcode</strong></label>
                                    <input type="text" id="serialNumberRepair" class="form-control form-control-lg" 
                                           placeholder="Scan Serial Number" autofocus autocomplete="off">
                                    <small class="text-muted">Tekan Enter setelah scan</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-table"></i> Hasil Scanning</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Repair Time</th>
                                            <th>Serial Number</th>
                                            <th>Jenis</th>
                                            <th>Kerusakan</th>
                                            <th>Result</th>
                                            <th>User</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="RepairTableBody">
                                        @foreach($recentRepairs as $repair)
                                        <tr id="repair-row-{{ $repair->id }}">
                                            <td>{{ $repair->repair_time->format('d-m-Y H:i:s') }}</td>
                                            <td><code>{{ $repair->igiDetail->serial_number }}</code></td>
                                            <td>{{ $repair->igiDetail->jenis }}</td>
                                            <td><span class="badge bg-warning text-dark">{{ $repair->jenis_kerusakan }}</span></td>
                                            <td><span class="badge badge-{{ $repair->result === 'OK' ? 'ok' : 'nok' }}">{{ $repair->result }}</span></td>
                                            <td>{{ $repair->user->name }}</td>
                                            <td>
                                                @if(auth()->user()->canDeleteActivity($repair->user_id))
                                                <button class="btn btn-sm btn-danger btn-delete-repair" data-id="{{ $repair->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $recentRepairs->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let igiDetailId = null;
    
    // AUTOFOCUS saat pertama load
    $('#serialNumberRepair').focus();
    
    // AUTOFOCUS saat pindah ke tab scanning
    $('#tabScanning').on('shown.bs.tab', function() {
        $('#serialNumberRepair').focus();
    });
    
    // LANGSUNG SUBMIT saat Enter (tanpa preview)
    $('#serialNumberRepair').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            checkAndSubmit();
        }
    });
    
    // Fungsi check serial dan langsung submit
    function checkAndSubmit() {
        const serialNumber = $('#serialNumberRepair').val().trim();
        const result = $('input[name="result"]:checked').val();
        const jenisKerusakan = $('#jenisKerusakan').val();
        
        if (!serialNumber) {
            alert('Serial number tidak boleh kosong!');
            return;
        }
        
        if (!jenisKerusakan) {
            alert('Pilih jenis kerusakan terlebih dahulu!');
            $('#jenisKerusakan').focus();
            return;
        }
        
        // Check serial number
        $.ajax({
            url: '{{ route("repair.check-serial") }}',
            method: 'POST',
            data: { serial_number: serialNumber },
            success: function(response) {
                igiDetailId = response.data.id;
                // Langsung submit repair
                submitRepair();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error validasi serial number!');
                $('#serialNumberRepair').val('').focus();
            }
        });
    }
    
    // Fungsi submit repair
    function submitRepair() {
        const result = $('input[name="result"]:checked').val();
        const jenisKerusakan = $('#jenisKerusakan').val();
        
        $.ajax({
            url: '{{ route("repair.store") }}',
            method: 'POST',
            data: { 
                igi_detail_id: igiDetailId, 
                result: result,
                jenis_kerusakan: jenisKerusakan
            },
            success: function(response) {
                const data = response.data;
                const badge = data.result === 'OK' ? 'badge-ok' : 'badge-nok';
                const deleteBtn = data.can_delete ? `
                    <button class="btn btn-sm btn-danger btn-delete-repair" data-id="${data.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                ` : '';
                
                const row = `
                    <tr id="repair-row-${data.id}">
                        <td>${new Date(data.repair_time).toLocaleString('id-ID')}</td>
                        <td><code>${data.serial_number}</code></td>
                        <td>${data.jenis}</td>
                        <td><span class="badge bg-warning text-dark">${data.jenis_kerusakan}</span></td>
                        <td><span class="badge ${badge}">${data.result}</span></td>
                        <td>${data.user_name}</td>
                        <td>${deleteBtn}</td>
                    </tr>
                `;
                
                $('#RepairTableBody').prepend(row);
                
                // Clear input dan focus kembali
                $('#serialNumberRepair').val('').focus();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error menyimpan data repair!');
                $('#serialNumberRepair').val('').focus();
            }
        });
    }
    
    // Delete handler
    $(document).on('click', '.btn-delete-repair', function() {
        const id = $(this).data('id');
        if (!confirm('Yakin ingin menghapus data repair ini?')) return;
        
        $.ajax({
            url: `/repair/${id}`,
            method: 'DELETE',
            success: function(response) {
                $(`#repair-row-${id}`).fadeOut(300, function() { 
                    $(this).remove(); 
                });
                alert(response.message);
                $('#serialNumberRepair').focus();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error menghapus data!');
            }
        });
    });
});
</script>
@endpush
@endsection