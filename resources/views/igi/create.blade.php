@extends('layouts.app')

@section('title', 'Tambah Data I.G.I')
@section('page-title', 'Tambah Data I.G.I')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Form Tambah Data I.G.I</h5>
                </div>
                <div class="card-body">
                    <!-- Info Alert -->
                    

                    <form action="{{ route('igi.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">No DO <span class="text-danger">*</span></label>
                            <input type="text" name="no_do" class="form-control @error('no_do') is-invalid @enderror" 
                                   value="{{ old('no_do') }}" required placeholder="Contoh: DO-20250101-0001">
                            @error('no_do')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">No DO harus unique di seluruh sistem</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <select name="nama_barang" class="form-select @error('nama_barang') is-invalid @enderror" required>
                                <option value="">Pilih Nama Barang</option>
                                <option value="ONT" {{ old('nama_barang') == 'ONT' ? 'selected' : '' }}>ONT</option>
                                <option value="STB" {{ old('nama_barang') == 'STB' ? 'selected' : '' }}>STB</option>
                                <option value="ROUTER" {{ old('nama_barang') == 'ROUTER' ? 'selected' : '' }}>ROUTER</option>
                            </select>
                            @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type / Merk <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" 
                                   value="{{ old('type') }}" required placeholder="Contoh: ZTE F609">
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" 
                                   value="{{ old('serial_number') }}" required placeholder="Contoh: ONT202500001">
                            @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Serial Number harus unique dan tidak boleh duplikat</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Scan <span class="text-danger">*</span></label>
                            <input type="number" name="total_scan" class="form-control @error('total_scan') is-invalid @enderror" 
                                   value="{{ old('total_scan', 1) }}" min="1" required readonly>
                            @error('total_scan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Data
                            </button>
                            <a href="{{ route('igi.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-eye"></i> Preview Workflow</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-center">
                            <i class="bi bi-1-circle fs-3 text-primary"></i>
                            <p class="mb-0 small">IGI</p>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="text-center">
                            <i class="bi bi-2-circle fs-3 text-info"></i>
                            <p class="mb-0 small">Uji Fungsi</p>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="text-center">
                            <i class="bi bi-3-circle fs-3 text-warning"></i>
                            <p class="mb-0 small">Repair</p>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="text-center">
                            <i class="bi bi-4-circle fs-3 text-success"></i>
                            <p class="mb-0 small">Rekondisi</p>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="text-center">
                            <i class="bi bi-5-circle fs-3 text-primary"></i>
                            <p class="mb-0 small">Packing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection