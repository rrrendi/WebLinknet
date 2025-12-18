@extends('layouts.app')
@section('title', 'Packing')
@section('page-title', 'Packing')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-tabs mb-3" id="packingTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#monitoring">
                <i class="bi bi-bar-chart"></i> Monitoring
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scanning">
                <i class="bi bi-box-seam"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: MONITORING --}}
        <div class="tab-pane fade show active" id="monitoring">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Packing</h5>
                </div>
                <div class="card-body">
                    @foreach(['Linknet', 'Telkomsel'] as $pemilik)
                        <h6 class="text-muted mb-3">{{ $pemilik }}</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center">
                                <thead class="table">
                                    <tr>
                                        <th>STB</th>
                                        <th>ONT</th>
                                        <th>ROUTER</th>
                                        <th class="bg-success">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-success bg-opacity-10">
                                            <h3 class="mb-0 text-success">{{ $monitoring[$pemilik]['STB'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-success bg-opacity-10">
                                            <h3 class="mb-0 text-success">{{ $monitoring[$pemilik]['ONT'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-success bg-opacity-10">
                                            <h3 class="mb-0 text-success">{{ $monitoring[$pemilik]['ROUTER'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-success bg-opacity-25">
                                            <h2 class="mb-0 text-success fw-bold">{{ $monitoring[$pemilik]['TOTAL'] ?? 0 }}</h2>
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
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-box-seam"></i> Form Packing</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="bi bi-info-circle"></i>
                                <strong>Ketentuan:</strong> Hanya barang yang sudah melewati Rekondisi yang bisa masuk Packing.
                            </div>
                            <form id="packingForm">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Scan Barcode / Serial Number</strong> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberPacking" class="form-control form-control-lg" placeholder="Scan atau ketik Serial Number" required autofocus autocomplete="off">
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
                            <h6 class="mb-0"><i class="bi bi-table"></i> Data Packing</h6>
                            <button class="btn btn-sm btn-primary" onclick="location.reload()">
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
                                            <th>Jenis</th>
                                            <th>Merk</th>
                                            <th>Type</th>
                                            <th>User</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="packingTableBody">
                                        @forelse($recentPacking as $pack)
                                            <tr id="packing-row-{{ $pack->id }}">
                                                <td>{{ $pack->packing_time->format('d-m-Y H:i:s') }}</td>
                                                <td><code>{{ $pack->igiDetail->serial_number }}</code></td>
                                                <td>{{ $pack->igiDetail->jenis }}</td>
                                                <td>{{ $pack->igiDetail->merk }}</td>
                                                <td>{{ $pack->igiDetail->type }}</td>
                                                <td>{{ $pack->user->name }}</td>
                                                <td>
                                                    @if(auth()->user()->canDeleteActivity($pack->user_id))
                                                        <button class="btn btn-sm btn-danger btn-delete-packing" data-id="{{ $pack->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="noPackingData">
                                                <td colspan="7" class="text-center text-muted">Belum ada data packing</td>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Tab State Persistence
    const tabKey = 'packingActiveTab';
    const savedTab = localStorage.getItem(tabKey);
    
    if (savedTab) {
        const tabElement = document.querySelector(`button[data-bs-target="${savedTab}"]`);
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    }
    
    document.querySelectorAll('#packingTabs button[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem(tabKey, e.target.getAttribute('data-bs-target'));
        });
    });

    // Auto-focus when switching to scanning tab
    $('button[data-bs-target="#scanning"]').on('shown.bs.tab', function() {
        $('#serialNumberPacking').focus();
    });

    let igiDetailId = null;

    // Scan dan Submit Langsung
    $('#serialNumberPacking').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const serialNumber = $(this).val().trim();
            
            if (!serialNumber) {
                alert('Serial number tidak boleh kosong!');
                return;
            }

            // Check Serial
            $.ajax({
                url: '{{ route("packing.check-serial") }}',
                method: 'POST',
                data: { serial_number: serialNumber },
                success: function(response) {
                    if (response.success) {
                        igiDetailId = response.data.id;
                        submitPacking();
                    }
                },
                error: function(xhr) {
                    playScanErrorSound(); // Play error sound
                    alert(xhr.responseJSON?.message || 'Serial Number tidak valid!');
                    $('#serialNumberPacking').val('').focus();
                }
            });
        }
    });

    function submitPacking() {
        $.ajax({
            url: '{{ route("packing.store") }}',
            method: 'POST',
            data: { igi_detail_id: igiDetailId },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const deleteBtn = data.can_delete ? `
                        <button class="btn btn-sm btn-danger btn-delete-packing" data-id="${data.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : '';

                    const row = `
                        <tr id="packing-row-${data.id}">
                            <td>${data.packing_time}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.jenis}</td>
                            <td>${data.merk}</td>
                            <td>${data.type}</td>
                            <td>${data.user_name}</td>
                            <td>${deleteBtn}</td>
                        </tr>
                    `;
                    
                    $('#noPackingData').remove();
                    $('#packingTableBody').prepend(row);
                    $('#serialNumberPacking').val('').focus();
                    
                    // Play success sound
                    playScanSuccessSound();
                    
                    console.log('✓ Packing berhasil disimpan - Barang siap dikirim!');
                }
            },
            error: function(xhr) {
                playScanErrorSound(); // Play error sound
                alert(xhr.responseJSON?.message || 'Error menyimpan data!');
                $('#serialNumberPacking').val('').focus();
            }
        });
    }

    // Delete Packing
    $(document).on('click', '.btn-delete-packing', function() {
        const id = $(this).data('id');
        
        if (!confirm('Yakin ingin menghapus data packing ini?\n\nData akan dikembalikan ke proses sebelumnya.')) {
            return;
        }

        $.ajax({
            url: `/packing/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#packing-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#packingTableBody tr').length === 0) {
                            $('#packingTableBody').html('<tr id="noPackingData"><td colspan="7" class="text-center text-muted">Belum ada data packing</td></tr>');
                        }
                    });
                    alert(response.message);
                    $('#serialNumberPacking').focus();
                }
                playScanSuccessSound();
            },
            error: function(xhr) {
                playScanErrorSound();
                alert(xhr.responseJSON?.message || 'Error menghapus data!');
            }
        });
    });
});
</script>
@endpush
@endsection