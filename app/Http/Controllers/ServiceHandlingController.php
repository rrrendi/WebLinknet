<?php
// app/Http/Controllers/ServiceHandlingController.php
namespace App\Http\Controllers;

use App\Models\{ServiceHandling, IgiDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceHandlingController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoring();
        $services = ServiceHandling::with(['igiDetail.bapb', 'user'])
            ->orderBy('service_time', 'desc')
            ->paginate(10);

        return view('service-handling.index', compact('monitoring', 'services'));
    }

    private function getMonitoring()
    {
        $data = [];
        foreach (['Linknet', 'Telkomsel'] as $pemilik) {
            foreach (['STB', 'ONT', 'ROUTER'] as $jenis) {
                $count = ServiceHandling::whereHas('igiDetail', function ($q) use ($pemilik, $jenis) {
                    $q->whereHas('bapb', fn($q2) => $q2->where('pemilik', $pemilik))
                        ->where('jenis', $jenis);
                })->count();
                $data[$pemilik][$jenis] = $count;
            }
            $data[$pemilik]['TOTAL'] = array_sum($data[$pemilik]);
        }
        return $data;
    }

    public function checkSerial(Request $request)
    {
        $detail = IgiDetail::where('serial_number', $request->serial_number)->first();

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Serial Number tidak ditemukan!'], 404);
        }
        
        // Validasi 1: Cek status tidak boleh dari PACKING atau setelahnya
        $forbiddenStatuses = ['PACKING', 'FINISH'];
        if (in_array($detail->status_proses, $forbiddenStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa masuk Service Handling! Barang sudah dalam proses ' . $detail->status_proses
            ], 403);
        }

        // Validasi: belum pernah di Service Handling
        if ($detail->ServiceHandling()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial Number sudah pernah di Service Handling!'
            ], 400);
        }

        // Ambil data terakhir uji fungsi
        $lastUjiFungsi = $detail->ujiFungsi()->latest()->first();

        // Ambil data terakhir repair
        $lastRepair = $detail->repair()->latest()->first();

        // Validasi: harus Uji Fungsi NOK atau Repair NOK
        if (
            (!$lastUjiFungsi || $lastUjiFungsi->result !== 'NOK') &&
            (!$lastRepair || $lastRepair->result !== 'NOK')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Service Handling hanya untuk barang dengan Uji Fungsi NOK atau Repair NOK!'
            ], 400);
        }


        return response()->json(['success' => true, 'data' => [
            'id' => $detail->id,
            'jenis' => $detail->jenis,
            'merk' => $detail->merk,
            'type' => $detail->type,
            'serial_number' => $detail->serial_number
        ]]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $detail = IgiDetail::findOrFail($request->igi_detail_id);

            $service = ServiceHandling::create([
                'igi_detail_id' => $detail->id,
                'service_time' => Carbon::now(),
                'user_id' => Auth::id(),
                'catatan' => $request->catatan
            ]);

            $detail->updateStatusProses('SERVICE_HANDLING');
            $detail->logActivity('SERVICE_HANDLING', 'N/A', Auth::id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service Handling berhasil!',
                'data' => [
                    'id' => $service->id,
                    'service_time' => $service->service_time->format('d-m-Y H:i:s'),
                    'serial_number' => $detail->serial_number,
                    'jenis' => $detail->jenis,
                    'merk' => $detail->merk,
                    'type' => $detail->type,
                    'user_name' => $service->user->name,
                    'can_delete' => true
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $service = ServiceHandling::with('igiDetail')->findOrFail($id);

            if (!$user || !$user->canDeleteActivity($service->user_id)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada izin!'], 403);
            }

            // Check: harus proses terakhir
            if ($service->igiDetail->status_proses !== 'SERVICE_HANDLING') {
                return response()->json(['success' => false, 'message' => 'Tidak bisa hapus! Barang sudah masuk proses berikutnya.'], 403);
            }

            $service->delete();

            // Kembalikan ke status sebelumnya
            $previousStatus = $service->igiDetail->getPreviousStatus();
            $service->igiDetail->updateStatusProses($previousStatus);
            $service->igiDetail->activityLogs()->where('aktivitas', 'SERVICE_HANDLING')->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
