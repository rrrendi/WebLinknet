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
                                <option value="igi">I.G.I</option>
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
                                <select name="pemilik" id="pemilik" class="form-select">
                                    <option value="">Semua Pemilik</option>
                                    @foreach($pemilikList as $pemilik)
                                    <option value="{{ $pemilik }}">{{ $pemilik }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Wilayah</label>
                                <select name="wilayah" id="wilayah" class="form-select">
                                    <option value="">Semua Wilayah</option>
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

@push('scripts')
<script>
$(document).ready(function() {
    loadWilayah('');
    
    $('#pemilik').on('change', function() {
        const pemilik = $(this).val();
        loadWilayah(pemilik);
    });
    
    function loadWilayah(pemilik) {
        $.ajax({
            url: '{{ route("download.wilayah-by-pemilik") }}',
            type: 'GET',
            data: { pemilik: pemilik },
            beforeSend: function() {
                $('#wilayah').html('<option value="">Loading...</option>');
                $('#wilayah').prop('disabled', true);
            },
            success: function(response) {
                let options = '<option value="">Semua Wilayah</option>';
                
                if (response.length > 0) {
                    response.forEach(function(wilayah) {
                        options += `<option value="${wilayah}">${wilayah}</option>`;
                    });
                } else {
                    options = '<option value="">Tidak ada wilayah</option>';
                }
                
                $('#wilayah').html(options);
                $('#wilayah').prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error loading wilayah:', xhr);
                $('#wilayah').html('<option value="">Error loading wilayah</option>');
                $('#wilayah').prop('disabled', false);
            }
        });
    }
});
</script>
@endpush
@endsection