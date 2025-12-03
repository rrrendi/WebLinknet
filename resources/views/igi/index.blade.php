@extends('layouts.app')

@section('title', 'I.G.I - Incoming Goods Inspection')
@section('page-title', 'I.G.I (Incoming Goods Inspection) - Operasional')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-inbox"></i> Data I.G.I Operasional (Aktif)</h5>
            <a href="{{ route('igi.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </a>
        </div>
        <div class="card-body">
            <!-- Info Alert -->
            

            <!-- Filter Section -->
            <form method="GET" action="{{ route('igi.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari No DO, Serial Number..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" placeholder="Dari Tanggal" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" placeholder="Sampai Tanggal" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="nama_barang" class="form-select">
                            <option value="">Semua Barang</option>
                            @foreach($namaBarangList as $item)
                            <option value="{{ $item }}" {{ request('nama_barang') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status_proses" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="IGI" {{ request('status_proses') == 'IGI' ? 'selected' : '' }}>IGI</option>
                            <option value="UJI_FUNGSI" {{ request('status_proses') == 'UJI_FUNGSI' ? 'selected' : '' }}>Uji Fungsi</option>
                            <option value="REPAIR" {{ request('status_proses') == 'REPAIR' ? 'selected' : '' }}>Repair</option>
                            <option value="REKONDISI" {{ request('status_proses') == 'REKONDISI' ? 'selected' : '' }}>Rekondisi</option>
                            <option value="SERVICE_HANDLING" {{ request('status_proses') == 'SERVICE_HANDLING' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>No DO</th>
                            <th>Tanggal Datang</th>
                            <th>Nama Barang</th>
                            <th>Type</th>
                            <th>Serial Number</th>
                            <th>Total Scan</th>
                            <th>Status Proses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($igis as $index => $igi)
                        <tr>
                            <td>{{ $igis->firstItem() + $index }}</td>
                            <td><span class="badge bg-info">{{ $igi->no_do }}</span></td>
                            <td>{{ $igi->tanggal_datang->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $igi->nama_barang }}</td>
                            <td>{{ $igi->type }}</td>
                            <td><code>{{ $igi->serial_number }}</code></td>
                            <td>{{ $igi->total_scan }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'IGI' => 'secondary',
                                        'UJI_FUNGSI' => 'info',
                                        'REPAIR' => 'warning',
                                        'REKONDISI' => 'success',
                                        'SERVICE_HANDLING' => 'danger',
                                        'PACKING' => 'primary'
                                    ];
                                    $color = $statusColors[$igi->status_proses] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ str_replace('_', ' ', $igi->status_proses) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('igi.edit', $igi->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('igi.destroy', $igi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?\n\nData yang sudah memiliki proses tidak dapat dihapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination dengan Bootstrap 5 - FIX ICON SIZE -->
            <div class="d-flex justify-content-center">
                {{ $igis->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
/* Fix Pagination Icon Size */
.pagination {
    font-size: 14px;
}
.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 14px;
}
.pagination .page-item.disabled .page-link {
    opacity: 0.5;
}
</style>
@endsection