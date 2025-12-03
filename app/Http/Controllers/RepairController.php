<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Igi;
use App\Models\UjiFungsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RepairController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoringData();
        $recentRepairs = Repair::with('igi')->orderBy('waktu_repair', 'desc')->paginate(10);
        
        $jenisKerusakan = [
            'Konektor LAN rusak',
            'Konektor Optic rusak',
            'Adapter rusak',
            'Port mati',
            'LED mati',
            'Board rusak',
            'Tidak bisa nyala',
            'Restart terus',
            'Lainnya'
        ];
        
        return view('repair.index', compact('monitoring', 'recentRepairs', 'jenisKerusakan'));
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
            $ok = Repair::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->where('status', 'OK')->count();
            
            $nok = Repair::whereHas('igi', function($query) use ($category) {
                $query->where('nama_barang', $category);
            })->where('status', 'NOK')->count();
            
            $monitoring[$category] = [
                'ok' => $ok,
                'nok' => $nok,
                'total' => $ok + $nok
            ];
        }

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
        $igi = Igi::where('serial_number', $request->serial_number)->first();

        if (!$igi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan dalam database IGI'
            ], 404);
        }

        // VALIDASI: Harus ada uji_fungsi dengan status NOK
        $ujiFungsi = $igi->ujiFungsi()->where('status', 'NOK')->first();
        
        if (!$ujiFungsi) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number ini tidak memiliki hasil Uji Fungsi NOK. Repair hanya untuk barang yang NOK di Uji Fungsi.'
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
            'jenis_kerusakan' => 'required|string',
            'tindakan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $igi = Igi::findOrFail($request->igi_id);
            
            // VALIDASI: Harus ada uji_fungsi dengan status NOK
            if (!$igi->ujiFungsi()->where('status', 'NOK')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Repair hanya untuk barang yang NOK di Uji Fungsi'
                ], 400);
            }

            // Insert Repair
            $repair = Repair::create([
                'igi_id' => $igi->id,
                'status' => $request->status,
                'jenis_kerusakan' => $request->jenis_kerusakan,
                'tindakan' => $request->tindakan,
                'waktu_repair' => Carbon::now()
            ]);

            // Update status di IGI Operasional
            $igi->update(['status_proses' => 'REPAIR']);

            // Update status di IGI Master
            $igi->master->update(['status_proses' => 'REPAIR']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Repair berhasil disimpan',
                'data' => $repair->load('igi')
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
            $repair = Repair::with('igi.master')->findOrFail($id);
            $igi = $repair->igi;
            
            // PROTEKSI: Cek apakah ada proses selanjutnya
            // Jika ada rekondisi atau service handling, jangan boleh dihapus
            if ($igi->rekondisi()->exists() || $igi->serviceHandling()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Repair tidak bisa dihapus karena sudah melanjut ke proses berikutnya (Rekondisi/Service Handling). Hapus dari proses paling akhir terlebih dahulu!'
                ], 403);
            }
            
            // ROLLBACK LOGIC: Hapus Repair → Kembali ke UJI_FUNGSI
            $igi->update(['status_proses' => 'UJI_FUNGSI']);
            $igi->master->update(['status_proses' => 'UJI_FUNGSI']);
            
            // Delete record repair
            $repair->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data repair berhasil dihapus. Status barang dikembalikan ke UJI_FUNGSI.'
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