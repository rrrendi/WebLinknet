<?php
// app/Http/Controllers/PackingController.php
namespace App\Http\Controllers;

use App\Models\{Packing, IgiDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
                $count = Packing::whereHas('igiDetail', function ($q) use ($pemilik, $jenis) {
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
            return response()->json([
                'success' => false,
                'message' => 'Packing hanya untuk barang yang sudah Rekondisi!'
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
            return response()->json([
                'success' => true,
                'message' => 'Packing berhasil!',
                'data' => $packing->load(['igiDetail.bapb', 'user'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $packing = Packing::findOrFail($id);

        // Ambil user yang sedang login
        $currentUser = Auth::user();

        // Cek: Admin bisa hapus semua, user biasa hanya milik sendiri
        $isAdmin = ($currentUser->role === 'admin');
        $isOwner = ($packing->user_id == $currentUser->id);

        if (!$isAdmin && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus data ini.'
            ], 403);
        }

        $packing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
