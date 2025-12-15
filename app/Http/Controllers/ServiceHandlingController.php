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
                                     ->paginate(20);
        return view('service-handling.index', compact('monitoring', 'services'));
    }

    private function getMonitoring()
    {
        $data = [];
        foreach (['Linknet', 'Telkomsel'] as $pemilik) {
            foreach (['STB', 'ONT', 'ROUTER'] as $jenis) {
                $count = ServiceHandling::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
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
            $detail->logActivity('SERVICE_HANDLING', 'NOK', Auth::id());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Service Handling berhasil!', 
                                     'data' => $service->load(['igiDetail.bapb', 'user'])]);
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

            $service->delete();
            $service->igiDetail->updateStatusProses('REKONDISI');
            $service->igiDetail->activityLogs()->where('aktivitas', 'SERVICE_HANDLING')->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}