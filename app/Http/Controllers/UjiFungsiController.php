<?php

namespace App\Http\Controllers;

use App\Models\UjiFungsi;
use App\Models\Igi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UjiFungsiController extends Controller
{
    public function index()
    {
        // Get monitoring data
        $monitoring = $this->getMonitoringData();
        
        // Get recent scans
        $recentUjiFungsi = UjiFungsi::with('igi')
            ->orderBy('waktu_uji', 'desc')
            ->paginate(10);
        
        return view('uji-fungsi.index', compact('monitoring', 'recentUjiFungsi'));
    }

    public function getMonitoring()
    {
        $monitoring = $this->getMonitoringData();
        return response()->json($monitoring);
    }

    private function getMonitoringData()
    {
        $categories = ['ONT', 'STB', 'ROUTER'];
        $monitoring = [];

        foreach ($categories as $category) {
            $ok = UjiFungsi::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->where('status', 'OK')->count();
            
            $nok = UjiFungsi::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->where('status', 'NOK')->count();
            
            $monitoring[$category] = [
                'ok' => $ok,
                'nok' => $nok,
                'total' => $ok + $nok
            ];
        }

        // Total
        $totalOk = array_sum(array_column($monitoring, 'ok'));
        $totalNok = array_sum(array_column($monitoring, 'nok'));
        
        $monitoring['TOTAL'] = [
            'ok' => $totalOk,
            'nok' => $totalNok,
            'total' => $totalOk + $totalNok
        ];

        return $monitoring;
    }

    public function checkSerial(Request $request)
    {
        $request->validate([
            'serial_number' => 'required'
        ]);

        $igi = Igi::where('serial_number', $request->serial_number)->first();

        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        // VALIDASI: Tidak boleh uji fungsi 2x
        if ($igi->ujiFungsi()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number sudah pernah di Uji Fungsi.'
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
            'status' => 'required|in:OK,NOK',
            'keterangan' => 'nullable|string'
        ], [
            'igi_id.required' => 'IGI ID wajib diisi',
            'igi_id.exists' => 'IGI ID tidak ditemukan',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus OK atau NOK'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::findOrFail($request->igi_id);
            
            // VALIDASI LAGI: Tidak boleh uji fungsi 2x
            if ($igi->ujiFungsi()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial Number sudah pernah di Uji Fungsi.'
                ], 400);
            }

            // Insert Uji Fungsi
            $ujiFungsi = UjiFungsi::create([
                'igi_id' => $igi->id,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'waktu_uji' => Carbon::now()
            ]);

            // Update status di IGI Operasional
            $igi->update(['status_proses' => 'UJI_FUNGSI']);

            // Update status di IGI Master
            $igi->master->update(['status_proses' => 'UJI_FUNGSI']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Uji Fungsi berhasil disimpan',
                'data' => $ujiFungsi->load('igi')
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ujiFungsi = UjiFungsi::with('igi.master')->findOrFail($id);
            $igi = $ujiFungsi->igi;
            
            // PROTEKSI: Cek apakah ada proses selanjutnya
            // Jika ada repair atau rekondisi, jangan boleh dihapus
            if ($igi->repair()->exists() || $igi->rekondisi()->exists() || $igi->serviceHandling()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Uji Fungsi tidak bisa dihapus karena sudah melanjut ke proses berikutnya (Repair/Rekondisi/Service Handling). Hapus dari proses paling akhir terlebih dahulu!'
                ], 403);
            }
            
            // ROLLBACK LOGIC: Hapus Uji Fungsi → Kembali ke IGI
            $igi->update(['status_proses' => 'IGI']);
            $igi->master->update(['status_proses' => 'IGI']);
            
            // Delete record uji fungsi
            $ujiFungsi->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data uji fungsi berhasil dihapus. Status barang dikembalikan ke IGI.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}