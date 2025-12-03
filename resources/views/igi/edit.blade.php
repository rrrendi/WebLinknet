@extends('layouts.app')

@section('title', 'Edit Data I.G.I')
@section('page-title', 'Edit Data I.G.I')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Form Edit Data I.G.I</h5>
                </div>
                <div class="card-body">
                    <!-- Info Alert -->
                    

                    <form action="{{ route('igi.update', $igi->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Read Only Fields -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-muted">Data yang Tidak Dapat Diubah:</h6>
                                
                                <div class="mb-2">
                                    <strong>No DO:</strong>
                                    <span class="badge bg-info ms-2">{{ $igi->no_do }}</span>
                                </div>

                                <div class="mb-2">
                                    <strong>Serial Number:</strong>
                                    <code>{{ $igi->serial_number }}</code>
                                </div>

                                <div class="mb-2">
                                    <strong>Tanggal Datang:</strong>
                                    {{ $igi->tanggal_datang->format('d-m-Y H:i:s') }}
                                </div>

                                <div class="mb-2">
                                    <strong>Status Proses:</strong>
                                    <span class="badge bg-primary">{{ str_replace('_', ' ', $igi->status_proses) }}</span>
                                </div>

                                <div class="mb-0">
                                    <strong>Master ID:</strong>
                                    <span class="badge bg-secondary">#{{ $igi->master_id }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Editable Fields -->
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <select name="nama_barang" class="form-select @error('nama_barang') is-invalid @enderror" required>
                                <option value="ONT" {{ $igi->nama_barang == 'ONT' ? 'selected' : '' }}>ONT</option>
                                <option value="STB" {{ $igi->nama_barang == 'STB' ? 'selected' : '' }}>STB</option>
                                <option value="ROUTER" {{ $igi->nama_barang == 'ROUTER' ? 'selected' : '' }}>ROUTER</option>
                            </select>
                            @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type / Merk <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" 
                                   value="{{ old('type', $igi->type) }}" required>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update Data
                            </button>
                            <a href="{{ route('igi.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- History Info -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Proses</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($igi->ujiFungsi->count() > 0)
                        <div class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Uji Fungsi:</strong> {{ $igi->ujiFungsi->last()->status }}
                            <small class="text-muted">({{ $igi->ujiFungsi->last()->waktu_uji->format('d-m-Y H:i') }})</small>
                        </div>
                        @endif

                        @if($igi->repair->count() > 0)
                        <div class="mb-2">
                            <i class="bi bi-tools text-warning"></i> 
                            <strong>Repair:</strong> {{ $igi->repair->last()->status }}
                            <small class="text-muted">({{ $igi->repair->last()->waktu_repair->format('d-m-Y H:i') }})</small>
                        </div>
                        @endif

                        @if($igi->rekondisi->count() > 0)
                        <div class="mb-2">
                            <i class="bi bi-arrow-clockwise text-info"></i> 
                            <strong>Rekondisi:</strong> Selesai
                            <small class="text-muted">({{ $igi->rekondisi->last()->waktu_rekondisi->format('d-m-Y H:i') }})</small>
                        </div>
                        @endif

                        @if($igi->serviceHandling->count() > 0)
                        <div class="mb-2">
                            <i class="bi bi-wrench text-danger"></i> 
                            <strong>Service Handling:</strong> NOK
                            <small class="text-muted">({{ $igi->serviceHandling->last()->waktu_service->format('d-m-Y H:i') }})</small>
                        </div>
                        @endif

                        @if($igi->packing->count() > 0)
                        <div class="mb-0">
                            <i class="bi bi-box-seam text-success"></i> 
                            <strong>Packing:</strong> Selesai
                            <small class="text-muted">({{ $igi->packing->last()->waktu_packing->format('d-m-Y H:i') }})</small>
                        </div>
                        @endif

                        @if($igi->ujiFungsi->count() == 0 && $igi->repair->count() == 0 && $igi->rekondisi->count() == 0)
                        <p class="text-muted mb-0">Belum ada proses yang dilakukan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection