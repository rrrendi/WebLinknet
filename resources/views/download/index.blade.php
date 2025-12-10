{{-- ================================================================ --}}
{{-- resources/views/download/index.blade.php --}}
{{-- ================================================================ --}}
@extends('layouts.app')
@section('title', 'Download Data')
@section('page-title', 'Download Data Excel')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-download"></i> Form Download Data</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('download.export') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Modul <span class="text-danger">*</span></label>
                            <select name="modul" class="form-select" required>
                                <option value="">Pilih Modul</option>
                                <option value="igi">IGI (Incoming Goods)</option>
                                <option value="uji_fungsi">Uji Fungsi</option>
                                <option value="repair">Repair</option>
                                <option value="rekondisi">Rekondisi</option>
                                <option value="service_handling">Service Handling</option>
                                <option value="packing">Packing</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemilik</label>
                                <select name="pemilik" class="form-select">
                                    <option value="">Semua Pemilik</option>
                                    @foreach($pemilikList as $pemilik)
                                    <option value="{{ $pemilik }}">{{ $pemilik }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wilayah</label>
                                <select name="wilayah" class="form-select">
                                    <option value="">Semua Wilayah</option>
                                    @foreach($wilayahList as $wilayah)
                                    <option value="{{ $wilayah }}">{{ $wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Datang</label>
                                <input type="date" name="tanggal_datang" class="form-control">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control">
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            File akan terdownload dalam format Excel (.xlsx)
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-download"></i> Download Data Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection