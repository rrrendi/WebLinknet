<?php
// app/Http/Controllers/RepairController.php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\IgiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoringData();

        $recentRepairs = Repair::with(['igiDetail.bapb', 'user'])
            ->orderBy('repair_time', 'desc')
            ->paginate(20);

        return view('repair.index', compact('monitoring', 'recentRepairs'));
    }

    private function getMonitoringData()
    {
        $monitoring = [];
        $pemilikList = ['Linknet', 'Telkomsel'];
        $jenisList = ['STB', 'ONT', 'ROUTER'];

        foreach ($pemilikList as $pemilik) {
            foreach ($jenisList as $jenis) {
                $ok = Repair::whereHas('igiDetail', function ($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', function ($q2) use ($pemilik) {
                        $q2->where('pemilik', $pemilik);
                    })->where('jenis', $jenis);
                })->where('result', 'OK')->count();

                $nok = Repair::whereHas('igiDetail', function ($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', function ($q2) use ($pemilik) {
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

    public function checkSerial(Request $request)
    {
        $request->validate([
            'serial_number' => 'required',
            'jenis_kerusakan' => 'required|in:Masih Hidup,Mati Total',
            'result' => 'required|in:OK,NOK'
        ]);

        $detail = IgiDetail::where('serial_number', $request->serial_number)->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number tidak ditemukan!'
            ], 404);
        }

        // Validasi: harus sudah uji fungsi dengan result NOK
        $lastUjiFungsi = $detail->ujiFungsi()->latest()->first();
        if (!$lastUjiFungsi || $lastUjiFungsi->result !== 'NOK') {
            return response()->json([
                'success' => false,
                'message' => 'Repair hanya untuk barang dengan Uji Fungsi NOK!'
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

    public function store(Request $request)
    {
        $request->validate([
            'igi_detail_id' => 'required|exists:igi_details,id',
            'jenis_kerusakan' => 'required|in:Masih Hidup,Mati Total',
            'result' => 'required|in:OK,NOK',
            'catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $detail = IgiDetail::findOrFail($request->igi_detail_id);

            $repair = Repair::create([
                'igi_detail_id' => $detail->id,
                'jenis_kerusakan' => $request->jenis_kerusakan,
                'result' => $request->result,
                'repair_time' => Carbon::now(),
                'user_id' => Auth::id(),
                'catatan' => $request->catatan
            ]);

            $detail->updateStatusProses('REPAIR');
            $detail->logActivity(
                'REPAIR',
                $request->result,
                Auth::id(),
                'Jenis Kerusakan: ' . $request->jenis_kerusakan
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repair berhasil disimpan!',
                'data' => $repair->load(['igiDetail.bapb', 'user'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $repair = Repair::with('igiDetail')->findOrFail($id);
            $detail = $repair->igiDetail;

            $currentUser = Auth::user();

            // Cek: Admin bisa hapus semua, user biasa hanya milik sendiri
            $isAdmin = ($currentUser->role === 'admin');
            $isOwner = ($repair->user_id == $currentUser->id);

            if (!$isAdmin && !$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini.'
                ], 403);
            }

            if ($detail->rekondisi()->exists() || $detail->serviceHandling()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa hapus. Sudah ada proses lanjutan!'
                ], 403);
            }

            $repair->delete();
            $detail->updateStatusProses('UJI_FUNGSI');
            $detail->activityLogs()->where('aktivitas', 'REPAIR')->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Repair berhasil dihapus!'
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
