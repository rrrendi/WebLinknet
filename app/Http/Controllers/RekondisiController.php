<?php

namespace App\Http\Controllers;

use App\Models\Rekondisi;
use App\Models\Repair;
use App\Models\UjiFungsi;
use App\Models\Igi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekondisiController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoringData();
        $recentRekondisi = Rekondisi::with('igi')->orderBy('waktu_rekondisi', 'desc')->paginate(10);
        
        return view('rekondisi.index', compact('monitoring', 'recentRekondisi'));
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
            $count = Rekondisi::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->count();
            
            $monitoring[$category] = $count;
        }

        $monitoring['TOTAL'] = array_sum($monitoring);
        return $monitoring;
    }

    public function checkSerial(Request $request)
    {
        $serialNumber = $request->serial_number;
        
        $igi = Igi::where('serial_number', $serialNumber)->first();
        
        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        // VALIDASI: Harus ada uji_fungsi OK ATAU repair OK
        $ujiFungsiOk = $igi->ujiFungsi()->where('status', 'OK')->exists();
        $repairOk = $igi->repair()->where('status', 'OK')->exists();

        if (!$ujiFungsiOk && !$repairOk) {
            return response()->json([
                'success' => false,
                'message' => 'Rekondisi hanya untuk barang dengan status OK dari Uji Fungsi atau Repair.'
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
            'tindakan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::findOrFail($request->igi_id);
            
            // VALIDASI: Harus ada uji_fungsi OK ATAU repair OK
            $ujiFungsiOk = $igi->ujiFungsi()->where('status', 'OK')->exists();
            $repairOk = $igi->repair()->where('status', 'OK')->exists();

            if (!$ujiFungsiOk && !$repairOk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rekondisi hanya untuk barang dengan status OK'
                ], 400);
            }

            // Insert Rekondisi
            $rekondisi = Rekondisi::create([
                'igi_id' => $igi->id,
                'tindakan' => $request->tindakan,
                'waktu_rekondisi' => Carbon::now()
            ]);

            // Update status di IGI Operasional
            $igi->update(['status_proses' => 'REKONDISI']);

            // Update status di IGI Master
            $igi->master->update(['status_proses' => 'REKONDISI']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Rekondisi berhasil disimpan',
                'data' => $rekondisi->load('igi')
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
            $rekondisi = Rekondisi::with('igi.master', 'igi.repair', 'igi.ujiFungsi')->findOrFail($id);
            $igi = $rekondisi->igi;
            
            // PROTEKSI: Cek apakah ada proses selanjutnya
            // Jika ada packing, jangan boleh dihapus
            if ($igi->packing()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Rekondisi tidak bisa dihapus karena sudah melanjut ke proses Packing. Hapus dari Packing terlebih dahulu!'
                ], 403);
            }
            
            // ROLLBACK LOGIC: Cek sumber terakhir
            // Jika ada repair OK → kembali ke REPAIR
            // Jika hanya uji fungsi OK → kembali ke UJI_FUNGSI
            
            $lastRepairOk = $igi->repair()->where('status', 'OK')->latest()->first();
            
            if ($lastRepairOk) {
                // Kembali ke REPAIR
                $igi->update(['status_proses' => 'REPAIR']);
                $igi->master->update(['status_proses' => 'REPAIR']);
            } else {
                // Kembali ke UJI_FUNGSI
                $igi->update(['status_proses' => 'UJI_FUNGSI']);
                $igi->master->update(['status_proses' => 'UJI_FUNGSI']);
            }
            
            // Delete record rekondisi
            $rekondisi->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data rekondisi berhasil dihapus. Status barang dikembalikan.'
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