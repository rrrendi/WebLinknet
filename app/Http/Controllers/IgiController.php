<?php
// app/Http/Controllers/IgiController.php

namespace App\Http\Controllers;

use App\Models\IgiBapb;
use App\Models\IgiDetail;
use App\Models\MasterMerk;
use App\Models\MasterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IgiController extends Controller
{
    // STEP 1: List BAPB Header
    public function index(Request $request)
    {
        $query = IgiBapb::query();

        // Filter
        if ($request->filled('pemilik')) {
            $query->where('pemilik', $request->pemilik);
        }
        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->wilayah);
        }
        if ($request->filled('tanggal_datang')) {
            $query->whereDate('tanggal_datang', $request->tanggal_datang);
        }
        if ($request->filled('search')) {
            $query->where('no_ido', 'like', '%' . $request->search . '%');
        }

        $bapbList = $query->orderBy('tanggal_datang', 'desc')
                          ->paginate(20);

        // Get unique wilayah untuk filter
        $wilayahList = IgiBapb::select('wilayah')->distinct()->pluck('wilayah');

        return view('igi.index', compact('bapbList', 'wilayahList'));
    }

    // STEP 2: Form Create BAPB Header
    public function create()
    {
        return view('igi.create-bapb');
    }

    // STEP 3: Store BAPB Header
    public function storeBapb(Request $request)
    {
        $request->validate([
            'pemilik' => 'required|in:Linknet,Telkomsel',
            'no_ido' => 'required|unique:igi_bapb,no_ido',
            'wilayah' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
        ], [
            'no_ido.unique' => 'No. IDO sudah terdaftar di sistem',
            'jumlah.min' => 'Jumlah minimal 1',
        ]);

        $bapb = IgiBapb::create([
            'pemilik' => $request->pemilik,
            'tanggal_datang' => Carbon::now(),
            'no_ido' => $request->no_ido,
            'wilayah' => $request->wilayah,
            'jumlah' => $request->jumlah,
            'total_scan' => 0
        ]);

        return redirect()->route('igi.scan-detail', $bapb->id)
                         ->with('success', 'BAPB berhasil dibuat. Silakan scan barang.');
    }

    // STEP 4: Form Scan Detail Barang
    public function scanDetail($bapbId)
    {
        $bapb = IgiBapb::with('details')->findOrFail($bapbId);
        
        // Get recent scans untuk BAPB ini
        $recentScans = IgiDetail::where('bapb_id', $bapbId)
                                ->with('scanner')
                                ->orderBy('scan_time', 'desc')
                                ->paginate(20);

        return view('igi.scan-detail', compact('bapb', 'recentScans'));
    }

    // API: Get Merk by Jenis
    public function getMerkByJenis($jenis)
    {
        $merkList = MasterMerk::active()
                              ->byJenis($jenis)
                              ->orderBy('merk')
                              ->get(['id', 'merk']);

        return response()->json($merkList);
    }

    // API: Get Type by Merk
    public function getTypeByMerk($merkId)
    {
        $typeList = MasterType::active()
                              ->where('merk_id', $merkId)
                              ->orderBy('type')
                              ->get(['id', 'type']);

        return response()->json($typeList);
    }

    // STEP 5: Store Detail Barang (Scan)
    public function storeDetail(Request $request)
    {
        $request->validate([
            'bapb_id' => 'required|exists:igi_bapb,id',
            'jenis' => 'required|in:STB,ONT,ROUTER',
            'merk' => 'required|string',
            'type' => 'required|string',
            'serial_number' => 'required|unique:igi_details,serial_number,NULL,id,deleted_at,NULL',
            'mac_address' => 'required|string',
            'stb_id' => 'nullable|string', // Required jika jenis = STB
        ], [
            'serial_number.unique' => 'Serial Number sudah terdaftar!',
        ]);

        // Validation: STB ID wajib untuk STB
        if ($request->jenis === 'STB' && empty($request->stb_id)) {
            return response()->json([
                'success' => false,
                'message' => 'STB ID wajib diisi untuk jenis STB'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $bapb = IgiBapb::findOrFail($request->bapb_id);

            // Check jika sudah mencapai batas
            if ($bapb->total_scan >= $bapb->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total scan sudah mencapai batas jumlah BAPB!'
                ], 422);
            }

            // Create detail - PERBAIKAN: Gunakan Auth::id()
            $detail = IgiDetail::create([
                'bapb_id' => $bapb->id,
                'jenis' => $request->jenis,
                'merk' => $request->merk,
                'type' => $request->type,
                'serial_number' => $request->serial_number,
                'mac_address' => $request->mac_address,
                'stb_id' => $request->stb_id,
                'scan_time' => Carbon::now(),
                'scan_by' => Auth::id(),  // PERBAIKAN
                'status_proses' => 'IGI'
            ]);

            // Increment total_scan di BAPB
            $bapb->incrementTotalScan();

            // Log activity - PERBAIKAN: Gunakan Auth::id()
            $detail->logActivity('IGI', 'N/A', Auth::id(), 'Barang masuk ke IGI');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil di-scan!',
                'data' => $detail->load('scanner'),
                'total_scan' => $bapb->fresh()->total_scan,
                'jumlah' => $bapb->jumlah
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Edit BAPB Header
    public function edit($id)
    {
        $bapb = IgiBapb::findOrFail($id);
        return view('igi.edit-bapb', compact('bapb'));
    }

    // Update BAPB Header
    public function update(Request $request, $id)
    {
        $bapb = IgiBapb::findOrFail($id);

        $request->validate([
            'pemilik' => 'required|in:Linknet,Telkomsel',
            'wilayah' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:' . $bapb->total_scan,
        ], [
            'jumlah.min' => 'Jumlah tidak boleh kurang dari total yang sudah di-scan (' . $bapb->total_scan . ')',
        ]);

        $bapb->update([
            'pemilik' => $request->pemilik,
            'wilayah' => $request->wilayah,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('igi.index')
                         ->with('success', 'BAPB berhasil diperbarui');
    }

    // Delete Detail (dikurangi dari total_scan)
    public function deleteDetail($id)
    {
        DB::beginTransaction();
        try {
            $detail = IgiDetail::findOrFail($id);
            $bapb = $detail->bapb;

            // Check jika sudah ada proses lanjutan
            if ($detail->status_proses !== 'IGI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa hapus. Barang sudah masuk proses: ' . $detail->status_proses
                ], 403);
            }

            // Soft delete detail
            $detail->delete();

            // Decrement total_scan
            $bapb->decrementTotalScan();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}