{{-- resources/views/igi/edit-bapb.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit BAPB')
@section('page-title', 'Edit BAPB - ' . $bapb->no_ido)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Form Edit BAPB</h5>
                </div>
                <div class="card-body">
                    <!-- Info Read Only -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="text-muted">Data yang Tidak Dapat Diubah:</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>No. IDO:</strong> <code>{{ $bapb->no_ido }}</code>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Tanggal Datang:</strong> {{ $bapb->tanggal_datang->format('d-m-Y') }}
                                </div>
                                <div class="col-md-6 mb-0">
                                    <strong>Total Scan:</strong> 
                                    <span class="badge bg-success">{{ $bapb->total_scan }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('igi.update', $bapb->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemilik <span class="text-danger">*</span></label>
                                <select name="pemilik" class="form-select @error('pemilik') is-invalid @enderror" required>
                                    <option value="Linknet" {{ $bapb->pemilik === 'Linknet' ? 'selected' : '' }}>Linknet</option>
                                    <option value="Telkomsel" {{ $bapb->pemilik === 'Telkomsel' ? 'selected' : '' }}>Telkomsel</option>
                                </select>
                                @error('pemilik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wilayah <span class="text-danger">*</span></label>
                                <input type="text" name="wilayah" class="form-control @error('wilayah') is-invalid @enderror" 
                                       value="{{ old('wilayah', $bapb->wilayah) }}" required>
                                @error('wilayah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                   value="{{ old('jumlah', $bapb->jumlah) }}" required min="{{ $bapb->total_scan }}">
                            @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">
                                Minimal {{ $bapb->total_scan }} (sesuai jumlah yang sudah di-scan)
                            </small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update BAPB
                            </button>
                            <a href="{{ route('igi.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection