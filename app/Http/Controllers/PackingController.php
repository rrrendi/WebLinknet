<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\Rekondisi;
use App\Models\Repair;
use App\Models\UjiFungsi;
use App\Models\Igi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PackingController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoringData();
        
        // Get packing data - tanpa withTrashed karena IGI tidak pake soft delete
        $recentPacking = Packing::with('igi')
            ->orderBy('waktu_packing', 'desc')
            ->paginate(15); // Changed from take(50) to paginate(15) for performance
        
        return view('packing.index', compact('monitoring', 'recentPacking'));
    }

    public function getMonitoring()
    {
        $monitoring = $this->getMonitoringData();
        return response()->json($monitoring)
            ->header('Cache-Control', 'max-age=60, public');
    }

    private function getMonitoringData()
    {
        $categories = ['ONT', 'STB', 'ROUTER'];
        $monitoring = [];

        foreach ($categories as $category) {
            // Count dari IGI Master dengan status PACKING
            $count = \App\Models\Igi::where('nama_barang', $category)
                ->where('status_proses', 'PACKING')
                ->count();
            
            $monitoring[$category] = $count;
        }

        $monitoring['TOTAL'] = array_sum($monitoring);
        return $monitoring;
    }

    public function checkSerial(Request $request)
    {
        $request->validate([
            'serial_number' => 'required'
        ]);

        $serialNumber = $request->serial_number;
        
        // Ambil IGI yang ada (tidak perlu withTrashed)
        $igi = Igi::where('serial_number', $serialNumber)->first();
        
        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        // VALIDASI: Harus ada rekondisi
        if (!$igi->rekondisi()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Packing hanya untuk barang yang sudah melewati Rekondisi'
            ], 400);
        }

        // VALIDASI: Tidak boleh ada packing sebelumnya
        if ($igi->packing()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number sudah pernah di-packing'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'igi_id' => $igi->id,
                'nama_barang' => $igi->nama_barang,
                'type' => $igi->type,
                'serial_number' => $igi->serial_number
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'igi_id' => 'required|exists:igi,id',
            'kondisi_box' => 'nullable|string',
            'catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::with('master')->findOrFail($request->igi_id);
            
            // VALIDASI: Harus ada rekondisi
            if (!$igi->rekondisi()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Packing hanya untuk barang yang sudah Rekondisi'
                ], 400);
            }

            // VALIDASI: Tidak boleh ada packing sebelumnya
            if ($igi->packing()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial Number sudah pernah di-packing'
                ], 400);
            }

            // SIMPAN data ke Packing
            $packing = Packing::create([
                'igi_id' => $igi->id,
                'waktu_packing' => Carbon::now(),
                'kondisi_box' => $request->kondisi_box,
                'catatan' => $request->catatan
            ]);

            // Update status di IGI Operasional
            $igi->update(['status_proses' => 'PACKING']);

            // Update status di IGI Master
            $igi->master->update(['status_proses' => 'PACKING']);

            DB::commit();

            // Load relasi untuk response
            $packing->load('igi');

            return response()->json([
                'success' => true,
                'message' => 'Data Packing berhasil disimpan. Barang siap dikirim.',
                'data' => [
                    'id' => $packing->id,
                    'waktu_packing' => $packing->waktu_packing,
                    'nama_barang' => $igi->nama_barang,
                    'type' => $igi->type,
                    'serial_number' => $igi->serial_number,
                    'kondisi_box' => $packing->kondisi_box
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Aksi 1: ROLLBACK - Kembalikan ke status Rekondisi
    public function rollback($id)
    {
        DB::beginTransaction();
        try {
            $packing = Packing::findOrFail($id);
            $igi = $packing->igi;
            
            if (!$igi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data IGI tidak ditemukan'
                ], 404);
            }
            
            // ROLLBACK LOGIC: Kembalikan status ke REKONDISI
            $igi->update(['status_proses' => 'REKONDISI']);
            $igi->master->update(['status_proses' => 'REKONDISI']);
            
            // Delete record packing
            $packing->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data packing berhasil di-rollback. Barang dikembalikan ke Rekondisi.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Aksi 2: FULL DELETE - Hapus semua data dari semua tabel
    public function fullDelete($id)
    {
        DB::beginTransaction();
        try {
            $packing = Packing::findOrFail($id);
            $igiId = $packing->igi_id;
            $igi = Igi::find($igiId);
            
            if (!$igi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data IGI tidak ditemukan'
                ], 404);
            }
            
            // FULL DELETE LOGIC: Hapus semua record yang terhubung
            UjiFungsi::where('igi_id', $igiId)->delete();
            Repair::where('igi_id', $igiId)->delete();
            Rekondisi::where('igi_id', $igiId)->delete();
            Packing::where('igi_id', $igiId)->delete();
            
            // Reset status IGI ke awal (tidak delete IGI, hanya reset status)
            $igi->update(['status_proses' => 'IGI']);
            $igi->master->update(['status_proses' => 'IGI']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data barang berhasil dihapus dari semua proses. Barang dikembalikan ke status IGI awal.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Original destroy method - panggil rollback
    public function destroy($id)
    {
        return $this->rollback($id);
    }
}