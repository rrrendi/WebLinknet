<?php
// app/Http/Controllers/PackingController.php
namespace App\Http\Controllers;

use App\Models\{Packing, IgiDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class PackingController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoring();
        $recentPacking = Packing::with(['igiDetail.bapb', 'user'])
                             ->orderBy('packing_time', 'desc')
                             ->paginate(20);
        return view('packing.index', compact('monitoring', 'recentPacking'));
    }

    private function getMonitoring()
    {
        $data = [];
        foreach (['Linknet', 'Telkomsel'] as $pemilik) {
            foreach (['STB', 'ONT', 'ROUTER'] as $jenis) {
                $count = Packing::whereHas('igiDetail', function($q) use ($pemilik, $jenis) {
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

        // Harus sudah rekondisi
        if (!$detail->rekondisi()->exists()) {
            return response()->json(['success' => false, 
                'message' => 'Packing hanya untuk barang yang sudah Rekondisi!'], 400);
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
            
            $packing = Packing::create([
                'igi_detail_id' => $detail->id,
                'packing_time' => Carbon::now(),
                'user_id' => Auth::id(),
                'kondisi_box' => $request->kondisi_box,
                'catatan' => $request->catatan
            ]);

            $detail->updateStatusProses('PACKING');
            $detail->logActivity('PACKING', 'N/A', Auth::id());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Packing berhasil!', 
                                     'data' => $packing->load(['igiDetail.bapb', 'user'])]);
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
            $packing = Packing::with('igiDetail')->findOrFail($id);
            
            if (!$user || !$user->canDeleteActivity($packing->user_id)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada izin!'], 403);
            }

            $packing->delete();
            $packing->igiDetail->updateStatusProses('REKONDISI');
            $packing->igiDetail->activityLogs()->where('aktivitas', 'PACKING')->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}