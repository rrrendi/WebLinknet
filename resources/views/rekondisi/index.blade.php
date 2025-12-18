@extends('layouts.app')
@section('title', 'Rekondisi')
@section('page-title', 'Rekondisi')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-tabs mb-3" id="rekondisiTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#monitoring">
                <i class="bi bi-bar-chart"></i> Monitoring
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#scanning">
                <i class="bi bi-arrow-clockwise"></i> Actual Scanning
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: MONITORING --}}
        <div class="tab-pane fade show active" id="monitoring">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ringkasan Data Rekondisi</h5>
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
                                        <th class="bg-primary">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-info bg-opacity-10">
                                            <h3 class="mb-0 text-info">{{ $monitoring[$pemilik]['STB'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-info bg-opacity-10">
                                            <h3 class="mb-0 text-info">{{ $monitoring[$pemilik]['ONT'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-info bg-opacity-10">
                                            <h3 class="mb-0 text-info">{{ $monitoring[$pemilik]['ROUTER'] ?? 0 }}</h3>
                                        </td>
                                        <td class="bg-primary bg-opacity-25">
                                            <h2 class="mb-0 text-primary fw-bold">{{ $monitoring[$pemilik]['TOTAL'] ?? 0 }}</h2>
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
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-upc-scan"></i> Form Scanning Rekondisi</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Ketentuan:</strong> Hanya barang dengan Uji Fungsi OK atau Repair OK yang bisa masuk Rekondisi.
                            </div>
                            <form id="rekondisiForm">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Scan Barcode / Serial Number</strong> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" id="serialNumberRekondisi" class="form-control form-control-lg" placeholder="Scan atau ketik Serial Number" required autofocus autocomplete="off">
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
                            <h6 class="mb-0"><i class="bi bi-table"></i> Data Actual Scanning Rekondisi</h6>
                            <button class="btn btn-sm btn-primary" onclick="location.reload()">
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
                                            <th>Jenis</th>
                                            <th>Merk</th>
                                            <th>Type</th>
                                            <th>User</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rekondisiTableBody">
                                        @forelse($recentRekondisi as $scan)
                                            <tr id="rekondisi-row-{{ $scan->id }}">
                                                <td>{{ $scan->rekondisi_time->format('d-m-Y H:i:s') }}</td>
                                                <td><code>{{ $scan->igiDetail->serial_number }}</code></td>
                                                <td>{{ $scan->igiDetail->jenis }}</td>
                                                <td>{{ $scan->igiDetail->merk }}</td>
                                                <td>{{ $scan->igiDetail->type }}</td>
                                                <td>{{ $scan->user->name }}</td>
                                                <td>
                                                    @if(auth()->user()->canDeleteActivity($scan->user_id))
                                                        <button class="btn btn-sm btn-danger btn-delete-rekondisi" data-id="{{ $scan->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="noRekondisiData">
                                                <td colspan="7" class="text-center text-muted">Belum ada data rekondisi</td>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Tab State Persistence
    const tabKey = 'rekondisiActiveTab';
    const savedTab = localStorage.getItem(tabKey);
    
    if (savedTab) {
        const tabElement = document.querySelector(`button[data-bs-target="${savedTab}"]`);
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    }
    
    document.querySelectorAll('#rekondisiTabs button[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem(tabKey, e.target.getAttribute('data-bs-target'));
        });
    });

    // Auto-focus when switching to scanning tab
    $('button[data-bs-target="#scanning"]').on('shown.bs.tab', function() {
        $('#serialNumberRekondisi').focus();
    });

    let igiDetailId = null;

    // Scan dan Submit Langsung
    $('#serialNumberRekondisi').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const serialNumber = $(this).val().trim();
            
            if (!serialNumber) {
                alert('Serial number tidak boleh kosong!');
                return;
            }

            // Check Serial
            $.ajax({
                url: '{{ route("rekondisi.check-serial") }}',
                method: 'POST',
                data: { serial_number: serialNumber },
                success: function(response) {
                    if (response.success) {
                        igiDetailId = response.data.id;
                        submitRekondisi();
                    }
                },
                error: function(xhr) {
                    playScanErrorSound(); // Play error sound
                    alert(xhr.responseJSON?.message || 'Serial Number tidak valid!');
                    $('#serialNumberRekondisi').val('').focus();
                }
            });
        }
    });

    function submitRekondisi() {
        $.ajax({
            url: '{{ route("rekondisi.store") }}',
            method: 'POST',
            data: { igi_detail_id: igiDetailId },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const deleteBtn = data.can_delete ? `
                        <button class="btn btn-sm btn-danger btn-delete-rekondisi" data-id="${data.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : '';

                    const row = `
                        <tr id="rekondisi-row-${data.id}">
                            <td>${data.rekondisi_time}</td>
                            <td><code>${data.serial_number}</code></td>
                            <td>${data.jenis}</td>
                            <td>${data.merk}</td>
                            <td>${data.type}</td>
                            <td>${data.user_name}</td>
                            <td>${deleteBtn}</td>
                        </tr>
                    `;
                    
                    $('#noRekondisiData').remove();
                    $('#rekondisiTableBody').prepend(row);
                    $('#serialNumberRekondisi').val('').focus();
                    
                    // Play success sound
                    playScanSuccessSound();
                    
                    // Success sound/notification (optional)
                    console.log('✓ Rekondisi berhasil disimpan');
                }
            },
            error: function(xhr) {
                playScanErrorSound(); // Play error sound
                alert(xhr.responseJSON?.message || 'Error menyimpan data!');
                $('#serialNumberRekondisi').val('').focus();
            }
        });
    }

    // Delete Rekondisi
    $(document).on('click', '.btn-delete-rekondisi', function() {
        const id = $(this).data('id');
        
        if (!confirm('Yakin ingin menghapus data rekondisi ini?\n\nData akan dikembalikan ke proses sebelumnya.')) {
            return;
        }

        $.ajax({
            url: `/rekondisi/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#rekondisi-row-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#rekondisiTableBody tr').length === 0) {
                            $('#rekondisiTableBody').html('<tr id="noRekondisiData"><td colspan="7" class="text-center text-muted">Belum ada data rekondisi</td></tr>');
                        }
                    });
                    alert(response.message);
                    $('#serialNumberRekondisi').focus();
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