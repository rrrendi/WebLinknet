<?php
// app/Http/Controllers/RepairController.php
namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\IgiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
                $ok = Repair::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', function($q2) use ($pemilik) {
                        $q2->where('pemilik', $pemilik);
                    })->where('jenis', $jenis);
                })->where('result', 'OK')->count();

                $nok = Repair::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
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
            $detail->logActivity('REPAIR', $request->result, Auth::id(), 'Jenis Kerusakan: ' . $request->jenis_kerusakan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repair berhasil disimpan!',
                'data' => [
                    'id' => $repair->id,
                    'repair_time' => $repair->repair_time->format('d-m-Y H:i:s'),
                    'serial_number' => $detail->serial_number,
                    'jenis' => $detail->jenis,
                    'merk' => $detail->merk,
                    'type' => $detail->type,
                    'jenis_kerusakan' => $repair->jenis_kerusakan,
                    'result' => $repair->result,
                    'user_name' => $repair->user->name,
                    'can_delete' => true
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $repair = Repair::with('igiDetail')->findOrFail($id);
            $detail = $repair->igiDetail;

            if (!$user || !$user->canDeleteActivity($repair->user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus aktivitas ini!'
                ], 403);
            }

            // Check: harus proses terakhir
            if ($detail->status_proses !== 'REPAIR') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa hapus! Barang sudah masuk proses berikutnya: ' . $detail->status_proses
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