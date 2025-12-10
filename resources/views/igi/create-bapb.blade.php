{{-- resources/views/igi/create-bapb.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah BAPB Baru')
@section('page-title', 'Tambah BAPB Baru')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Form Tambah BAPB</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('igi.store-bapb') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemilik <span class="text-danger">*</span></label>
                                <select name="pemilik" class="form-select @error('pemilik') is-invalid @enderror" required autofocus>
                                    <option value="">Pilih Pemilik</option>
                                    <option value="Linknet" {{ old('pemilik') === 'Linknet' ? 'selected' : '' }}>Linknet</option>
                                    <option value="Telkomsel" {{ old('pemilik') === 'Telkomsel' ? 'selected' : '' }}>Telkomsel</option>
                                </select>
                                @error('pemilik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. IDO <span class="text-danger">*</span></label>
                                <input type="text" name="no_ido" class="form-control @error('no_ido') is-invalid @enderror" 
                                       value="{{ old('no_ido') }}" required placeholder="Contoh: IDO-2025-001">
                                @error('no_ido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">No. IDO harus unik</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wilayah <span class="text-danger">*</span></label>
                                <input type="text" name="wilayah" class="form-control @error('wilayah') is-invalid @enderror" 
                                       value="{{ old('wilayah') }}" required placeholder="Contoh: Bandung">
                                @error('wilayah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                       value="{{ old('jumlah', 1) }}" required min="1" placeholder="Jumlah total barang">
                                @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Info:</strong> Tanggal datang akan otomatis diisi dengan tanggal hari ini.
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan & Lanjut Scan Barang
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