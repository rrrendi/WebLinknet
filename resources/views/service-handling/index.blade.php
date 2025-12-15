@extends('layouts.app')
@section('title', 'Service Handling')
@section('page-title', 'Service Handling')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-tabs mb-3" id="serviceHandlingTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#monitoring">
                <i class="bi bi-bar-chart"></i> Monitoring
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scanning">
                <i class="bi bi-wrench"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: MONITORING --}}
        <div class="tab-pane fade show active" id="monitoring">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Service Handling</h5>
                </div>
                <div class="card-body">
                    @foreach(['Linknet', 'Telkomsel'] as $pemilik)
                        <h6 class="text-muted mb-3">{{ $pemilik }}</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>STB</th>
                                        <th>ONT</th>
                                        <th>ROUTER</th>
                                        <th class="bg-warning">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-warning bg-opacity-10">
                                            <h3 class="mb-0 text-warning">{{ $monitoring[$pemilik]['STB'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-warning bg-opacity-10">
                                            <h3 class="mb-0 text-warning">{{ $monitoring[$pemilik]['ONT'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-warning bg-opacity-10">
                                            <h3 class="mb-0 text-warning">{{ $monitoring[$pemilik]['ROUTER'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-warning bg-opacity-25">
                                            <h2 class="mb-0 text-warning fw-bold">{{ $monitoring[$pemilik]['TOTAL'] ?? 0 }}</h2>
                                        </td>
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
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="bi bi-upc-scan"></i> Form Service Handling</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle"></i>
                                <strong>Info:</strong> Service Handling sama seperti Rekondisi - scan barcode langsung masuk data.
                            </div>
                            <form id="serviceHandlingForm">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Scan Barcode / Serial Number</strong> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberService" class="form-control form-control-lg" placeholder="Scan atau ketik Serial Number" required autofocus autocomplete="off">
                                    </div>
                                    <small class="text-muted">Tekan Enter setelah scan barcode</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="bi bi-table"></i> Data Service Handling</h6>
                            <button class="btn btn-sm btn-primary" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Service Time</th>
                                            <th>Serial Number</th>
                                            <th>Jenis</th>
                                            <th>Merk</th>
                                            <th>Type</th>
                                            <th>User</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="serviceTableBody">
                                        @forelse($services as $scan)
                                            <tr id="service-row-{{ $scan->id }}">
                                                <td>{{ $scan->service_time->format('d-m-Y H:i:s') }}</td>
                                                <td><code>{{ $scan->igiDetail->serial_number }}</code></td>
                                                <td>{{ $scan->igiDetail->jenis }}</td>
                                                <td>{{ $scan->igiDetail->merk }}</td>
                                                <td>{{ $scan->igiDetail->type }}</td>
                                                <td>{{ $scan->user->name }}</td>
                                                <td>
                                                    @if(auth()->user()->canDeleteActivity($scan->user_id))
                                                        <button class="btn btn-sm btn-danger btn-delete-service" data-id="{{ $scan->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="noServiceData">
                                                <td colspan="7" class="text-center text-muted">Belum ada data service handling</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $services->links('pagination::bootstrap-5') }}
                            </div>
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
    // Tab State Persistence
    const tabKey = 'serviceHandlingActiveTab';
    const savedTab = localStorage.getItem(tabKey);
    
    if (savedTab) {
        const tabElement = document.querySelector(`button[data-bs-target="${savedTab}"]`);
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    }
    
    document.querySelectorAll('#serviceHandlingTabs button[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem(tabKey, e.target.getAttribute('data-bs-target'));
        });
    });

    // Auto-focus when switching to scanning tab
    $('button[data-bs-target="#scanning"]').on('shown.bs.tab', function() {
        $('#serialNumberService').focus();
    });

    let igiDetailId = null;

    // Scan dan Submit Langsung
    $('#serialNumberService').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const serialNumber = $(this).val().trim();
            
            if (!serialNumber) {
                alert('Serial number tidak boleh kosong!');
                return;
            }

            // Check Serial
            $.ajax({
                url: '{{ route("service-handling.check-serial") }}',
                method: 'POST',
                data: { serial_number: serialNumber },
                success: function(response) {
                    if (response.success) {
                        igiDetailId = response.data.id;
                        submitServiceHandling();
                    }
                },
                error: function(xhr) {
                    playScanErrorSound(); // Play error sound
                    alert(xhr.responseJSON?.message || 'Serial Number tidak valid!');
                    $('#serialNumberService').val('').focus();
                }
            });
        }
    });

    function submitServiceHandling() {
        $.ajax({
            url: '{{ route("service-handling.store") }}',
            method: 'POST',
            data: { igi_detail_id: igiDetailId },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const deleteBtn = data.can_delete ? `
                        <button class="btn btn-sm btn-danger btn-delete-service" data-id="${data.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : '';

                    const row = `
                        <tr id="service-row-${data.id}">
                            <td>${data.service_time}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.jenis}</td>
                            <td>${data.merk}</td>
                            <td>${data.type}</td>
                            <td>${data.user_name}</td>
                            <td>${deleteBtn}</td>
                        </tr>
                    `;
                    
                    $('#noServiceData').remove();
                    $('#serviceTableBody').prepend(row);
                    $('#serialNumberService').val('').focus();
                    
                    // Play success sound
                    playScanSuccessSound();
                    
                    console.log('✓ Service Handling berhasil disimpan');
                }
            },
            error: function(xhr) {
                playScanErrorSound(); // Play error sound
                alert(xhr.responseJSON?.message || 'Error menyimpan data!');
                $('#serialNumberService').val('').focus();
            }
        });
    }

    // Delete Service Handling
    $(document).on('click', '.btn-delete-service', function() {
        const id = $(this).data('id');
        
        if (!confirm('Yakin ingin menghapus data service handling ini?\n\nData akan dikembalikan ke proses sebelumnya.')) {
            return;
        }

        $.ajax({
            url: `/service-handling/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#service-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#serviceTableBody tr').length === 0) {
                            $('#serviceTableBody').html('<tr id="noServiceData"><td colspan="7" class="text-center text-muted">Belum ada data service handling</td></tr>');
                        }
                    });
                    alert(response.message);
                    $('#serialNumberService').focus();
                }
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