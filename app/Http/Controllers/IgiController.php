<?php

namespace App\Http\Controllers;

use App\Models\Igi;
use App\Models\IgiMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IgiController extends Controller
{
    public function index(Request $request)
    {
        $query = Igi::with('master');
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_do', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_datang', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_datang', '<=', $request->end_date);
        }
        
        // Filter by nama_barang
        if ($request->has('nama_barang') && $request->nama_barang != '') {
            $query->where('nama_barang', $request->nama_barang);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status_proses') && $request->status_proses != '') {
            $query->where('status_proses', $request->status_proses);
        }
        
        $igis = $query->orderBy('tanggal_datang', 'desc')->paginate(20);
        
        // Get unique values for filters
        $namaBarangList = Igi::select('nama_barang')->distinct()->pluck('nama_barang');
        $typeList = Igi::select('type')->distinct()->pluck('type');
        
        return view('igi.index', compact('igis', 'namaBarangList', 'typeList'));
    }

    public function create()
    {
        return view('igi.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_do' => 'required|unique:igi_master,no_do',
            'nama_barang' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'serial_number' => 'required|unique:igi_master,serial_number',
            'total_scan' => 'required|integer|min:0',
        ], [
            'no_do.required' => 'No DO wajib diisi',
            'no_do.unique' => 'No DO sudah terdaftar dalam sistem',
            'nama_barang.required' => 'Nama Barang wajib diisi',
            'type.required' => 'Type wajib diisi',
            'serial_number.required' => 'Serial Number wajib diisi',
            'serial_number.unique' => 'Serial Number sudah terdaftar dalam sistem',
            'total_scan.required' => 'Total Scan wajib diisi',
            'total_scan.integer' => 'Total Scan harus berupa angka',
            'total_scan.min' => 'Total Scan minimal 0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Insert ke IGI MASTER (Permanen)
            $master = IgiMaster::create([
                'no_do' => $request->no_do,
                'tanggal_datang' => Carbon::now(),
                'nama_barang' => $request->nama_barang,
                'type' => $request->type,
                'serial_number' => $request->serial_number,
                'total_scan' => $request->total_scan,
                'status_proses' => 'IGI'
            ]);

            // 2. Insert ke IGI OPERASIONAL (Aktif)
            Igi::create([
                'master_id' => $master->id,
                'no_do' => $request->no_do,
                'tanggal_datang' => Carbon::now(),
                'nama_barang' => $request->nama_barang,
                'type' => $request->type,
                'serial_number' => $request->serial_number,
                'total_scan' => $request->total_scan,
                'status_proses' => 'IGI'
            ]);

            DB::commit();
            return redirect()->route('igi.index')
                ->with('success', 'Data IGI berhasil ditambahkan ke Master & Operasional');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $igi = Igi::with('master')->findOrFail($id);
        return view('igi.edit', compact('igi'));
    }

    public function update(Request $request, $id)
    {
        $igi = Igi::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ], [
            'nama_barang.required' => 'Nama Barang wajib diisi',
            'type.required' => 'Type wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update IGI Operasional
            $igi->update([
                'nama_barang' => $request->nama_barang,
                'type' => $request->type,
            ]);

            // Update IGI Master juga
            $igi->master->update([
                'nama_barang' => $request->nama_barang,
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->route('igi.index')
                ->with('success', 'Data IGI berhasil diperbarui di Master & Operasional');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $igi = Igi::findOrFail($id);
            $master = $igi->master;
            
            // Cek apakah sudah ada proses
            if ($igi->ujiFungsi()->exists() || 
                $igi->repair()->exists() || 
                $igi->rekondisi()->exists() || 
                $igi->serviceHandling()->exists() || 
                $igi->packing()->exists()) {
                
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus data yang sudah memiliki riwayat proses. Hapus proses terkait terlebih dahulu.');
            }
            
            // Hapus dari operasional (soft delete)
            $igi->delete();
            
            // Hapus dari master juga (permanent delete)
            // OPSIONAL: Atau biarkan di master untuk histori
            $master->delete();
            
            DB::commit();
            return redirect()->route('igi.index')
                ->with('success', 'Data IGI berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Method untuk get data by serial number (untuk scanning)
    public function getBySerial($serialNumber)
    {
        $igi = Igi::where('serial_number', $serialNumber)
            ->with(['master', 'ujiFungsi', 'repair', 'rekondisi'])
            ->first();
            
        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $igi
        ]);
    }
}