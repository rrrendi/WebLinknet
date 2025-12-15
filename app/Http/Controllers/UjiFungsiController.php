<?php
// app/Http/Controllers/UjiFungsiController.php

namespace App\Http\Controllers;

use App\Models\UjiFungsi;
use App\Models\IgiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UjiFungsiController extends Controller
{
    // TAB 1: Monitoring
    public function index()
    {
        $monitoring = $this->getMonitoringData();
        
        // Recent tests untuk TAB 2
        $recentTests = UjiFungsi::with(['igiDetail.bapb', 'user'])
                                ->orderBy('uji_fungsi_time', 'desc')
                                ->paginate(20);

        return view('uji-fungsi.index', compact('monitoring', 'recentTests'));
    }

    // Get Monitoring Data
    private function getMonitoringData()
    {
        $monitoring = [];
        $pemilikList = ['Linknet', 'Telkomsel'];
        $jenisList = ['STB', 'ONT', 'ROUTER'];

        foreach ($pemilikList as $pemilik) {
            foreach ($jenisList as $jenis) {
                $ok = UjiFungsi::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', function($q2) use ($pemilik) {
                        $q2->where('pemilik', $pemilik);
                    })->where('jenis', $jenis);
                })->where('result', 'OK')->count();

                $nok = UjiFungsi::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', function($q2) use ($pemilik) {
                        $q2->where('pemilik', $pemilik);
                    })->where('jenis', $jenis);
                })->where('result', 'NOK')->count();

                $monitoring[$pemilik][$jenis] = [
                    'ok' => $ok,
                    'nok' => $nok,
                    'total' => $ok + $nok
                ];
            }
        }

        // Total per pemilik
        foreach ($pemilikList as $pemilik) {
            $totalOk = array_sum(array_column($monitoring[$pemilik], 'ok'));
            $totalNok = array_sum(array_column($monitoring[$pemilik], 'nok'));
            $monitoring[$pemilik]['TOTAL'] = [
                'ok' => $totalOk,
                'nok' => $totalNok,
                'total' => $totalOk + $totalNok
            ];
        }

        return $monitoring;
    }

    // Check Serial Number
    public function checkSerial(Request $request)
    {
        $request->validate([
            'serial_number' => 'required',
            'result' => 'required|in:OK,NOK'
        ]);

        $detail = IgiDetail::where('serial_number', $request->serial_number)->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan di database IGI!'
            ], 404);
        }

        // Validasi: status harus masih IGI
        if ($detail->status_proses !== 'IGI') {
            return response()->json([
                'success' => false,
                'message' => 'Barang sudah masuk proses: ' . $detail->status_proses
            ], 400);
        }

        // Validasi: belum pernah di uji fungsi
        if ($detail->ujiFungsi()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number sudah pernah di Uji Fungsi!'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $detail->id,
                'jenis' => $detail->jenis,
                'merk' => $detail->merk,
                'type' => $detail->type,
                'serial_number' => $detail->serial_number
            ]
        ]);
    }

    // Store Uji Fungsi Result
    public function store(Request $request)
    {
        $request->validate([
            'igi_detail_id' => 'required|exists:igi_details,id',
            'result' => 'required|in:OK,NOK'
        ]);

        DB::beginTransaction();
        try {
            $detail = IgiDetail::findOrFail($request->igi_detail_id);

            // Validasi lagi
            if ($detail->ujiFungsi()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial Number sudah pernah di Uji Fungsi!'
                ], 400);
            }

            // Create Uji Fungsi record
            $ujiFungsi = UjiFungsi::create([
                'igi_detail_id' => $detail->id,
                'result' => $request->result,
                'uji_fungsi_time' => Carbon::now(),
                'user_id' => Auth::id()
            ]);

            // Update status proses
            $detail->updateStatusProses('UJI_FUNGSI');

            // Log activity
            $detail->logActivity('UJI_FUNGSI', $request->result, Auth::id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Uji Fungsi berhasil disimpan!',
                'data' => $ujiFungsi->load(['igiDetail.bapb', 'user'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete Uji Fungsi (hanya jika tidak ada proses lanjutan)
    public function destroy(request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $ujiFungsi = UjiFungsi::with('igiDetail')->findOrFail($id);
            $detail = $ujiFungsi->igiDetail;

            // Check permission: hanya user yang membuat bisa hapus
            if (!$user || !$user->canDeleteActivity($ujiFungsi->user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus aktivitas ini!'
                ], 403);
            }

            // Check jika ada proses lanjutan
            if ($detail->repair()->exists() || 
                $detail->rekondisi()->exists() || 
                $detail->serviceHandling()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa hapus. Sudah ada proses lanjutan!'
                ], 403);
            }

            // Hapus uji fungsi
            $ujiFungsi->delete();

            // Kembalikan status ke IGI
            $detail->updateStatusProses('IGI');

            // Hapus activity log
            $detail->activityLogs()->where('aktivitas', 'UJI_FUNGSI')->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Uji Fungsi berhasil dihapus!'
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