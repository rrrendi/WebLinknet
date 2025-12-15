{{-- ================================================================ --}}
{{-- resources/views/igi/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')

@section('title', 'IGI - Incoming Goods Inspection')
@section('page-title', 'IGI - Incoming Goods Inspection')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-inbox"></i> Daftar BAPB</h5>
            <a href="{{ route('igi.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah BAPB Baru
            </a>
        </div>
        
        <div class="card-body">
            <!-- Filter Section -->
            <form method="GET" action="{{ route('igi.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-2">
                        <select name="pemilik" class="form-select">
                            <option value="">Semua Pemilik</option>
                            <option value="Linknet" {{ request('pemilik') === 'Linknet' ? 'selected' : '' }}>Linknet</option>
                            <option value="Telkomsel" {{ request('pemilik') === 'Telkomsel' ? 'selected' : '' }}>Telkomsel</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="wilayah" class="form-select">
                            <option value="">Semua Wilayah</option>
                            @foreach($wilayahList as $wilayah)
                            <option value="{{ $wilayah }}" {{ request('wilayah') === $wilayah ? 'selected' : '' }}>
                                {{ $wilayah }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <input type="date" name="tanggal_datang" class="form-control" 
                               value="{{ request('tanggal_datang') }}" placeholder="Tanggal Datang">
                    </div>
                    
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari No. IDO..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('igi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Pemilik</th>
                            <th>Wilayah</th>
                            <th>No. IDO</th>
                            <th>Tanggal Datang</th>
                            <th>Total BAPB</th>
                            <th>Total Scan</th>
                            <th>Progress</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bapbList as $index => $bapb)
                        <tr>
                            <td>{{ $bapbList->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-{{ $bapb->pemilik === 'Linknet' ? 'primary' : 'success' }}">
                                    {{ $bapb->pemilik }}
                                </span>
                            </td>
                            <td>{{ $bapb->wilayah }}</td>
                            <td><code>{{ $bapb->no_ido }}</code></td>
                            <td>{{ $bapb->tanggal_datang->format('d-m-Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $bapb->jumlah }}</span>
                            </td>
                            <td class="text-center">
                                @if($bapb->total_scan === 0)
                                    <a href="{{ route('igi.scan-detail', $bapb->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-upc-scan"></i> Scan
                                    </a>
                                @else
                                    <a href="{{ route('igi.scan-detail', $bapb->id) }}" 
                                       class="badge bg-success" style="text-decoration: none; font-size: 1rem;">
                                        {{ $bapb->total_scan }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @php
                                    $percentage = $bapb->jumlah > 0 
                                        ? round(($bapb->total_scan / $bapb->jumlah) * 100) 
                                        : 0;
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-{{ $percentage >= 100 ? 'success' : 'warning' }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%;">
                                        {{ $percentage }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('igi.scan-detail', $bapb->id) }}" 
                                   class="btn btn-sm btn-primary" title="Scan Barang">
                                    <i class="bi bi-upc-scan"></i>
                                </a>
                                <a href="{{ route('igi.edit', $bapb->id) }}" 
                                   class="btn btn-sm btn-warning" title="Edit BAPB">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($bapb->total_scan === 0)
                                <form action="{{ route('igi.destroy', $bapb->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus BAPB ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus BAPB">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data BAPB. Klik "Tambah BAPB Baru" untuk mulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $bapbList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection