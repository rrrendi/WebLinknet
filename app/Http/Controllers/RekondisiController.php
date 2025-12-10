<?php
// app/Http/Controllers/RekondisiController.php
namespace App\Http\Controllers;

use App\Models\{Rekondisi, IgiDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RekondisiController extends Controller
{
    public function index()
    {
        $monitoring = $this->getMonitoring();
        $recentRekondisi  = Rekondisi::with(['igiDetail.bapb', 'user'])
            ->orderBy('rekondisi_time', 'desc')
            ->paginate(20);
        return view('rekondisi.index', compact('monitoring', 'recentRekondisi '));
    }

    private function getMonitoring()
    {
        $data = [];
        foreach (['Linknet', 'Telkomsel'] as $pemilik) {
            foreach (['STB', 'ONT', 'ROUTER'] as $jenis) {
                $count = Rekondisi::whereHas('igiDetail', function ($q) use ($pemilik, $jenis) {
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

        // Harus dari Uji Fungsi OK atau Repair OK
        $hasOkResult = $detail->ujiFungsi()->where('result', 'OK')->exists() ||
            $detail->repair()->where('result', 'OK')->exists();

        if (!$hasOkResult) {
            return response()->json([
                'success' => false,
                'message' => 'Rekondisi hanya untuk barang dengan result OK!'
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

            $rekondisi = Rekondisi::create([
                'igi_detail_id' => $detail->id,
                'rekondisi_time' => Carbon::now(),
                'user_id' => Auth::id()
            ]);

            $detail->updateStatusProses('REKONDISI');
            $detail->logActivity('REKONDISI', 'N/A', Auth::id());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rekondisi berhasil!',
                'data' => $rekondisi->load(['igiDetail.bapb', 'user'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $rekondisi = Rekondisi::with('igiDetail')->findOrFail($id);

            $currentUser = Auth::user();

            // Cek: Admin bisa hapus semua, user biasa hanya milik sendiri
            $isAdmin = ($currentUser->role === 'admin');
            $isOwner = ($rekondisi->user_id == $currentUser->id);

            if (!$isAdmin && !$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini.'
                ], 403);
            }

            if (
                $rekondisi->igiDetail->serviceHandling()->exists() ||
                $rekondisi->igiDetail->packing()->exists()
            ) {
                return response()->json(['success' => false, 'message' => 'Ada proses lanjutan!'], 403);
            }

            $rekondisi->delete();
            $rekondisi->igiDetail->updateStatusProses('REPAIR');
            $rekondisi->igiDetail->activityLogs()->where('aktivitas', 'REKONDISI')->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
